/*
 * Funktionen, die auf allen Seiten bzw. in allen Lernmodi gebraucht werden:
 *   - Hell/Dunkel umschalten
 *   - Sicherheitsabfrage vor dem Löschen
 *   - Stern-Knöpfe und Vorlesen-Knöpfe
 *   - Set per Ajax laden, Antworten speichern
 *   - Hilfsfunktionen: Mischen, tolerante Antwortprüfung, falsche Antwortmöglichkeiten
 */

/* ---------- Dark Mode ---------- */

const themaKnopf = document.getElementById('thema-knopf');

/** Ist gerade das dunkle Farbschema aktiv? (eigene Wahl oder Systemeinstellung) */
function istDunkel() {
    const thema = document.documentElement.dataset.thema;
    if (thema) return thema === 'dunkel';
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function aktualisiereThemaKnopf() {
    if (themaKnopf) themaKnopf.textContent = istDunkel() ? 'Hell' : 'Dunkel';
}

if (themaKnopf) {
    aktualisiereThemaKnopf();
    themaKnopf.addEventListener('click', () => {
        const neu = istDunkel() ? 'hell' : 'dunkel';
        document.documentElement.dataset.thema = neu;
        try { localStorage.setItem('thema', neu); } catch (fehler) { /* egal */ }
        aktualisiereThemaKnopf();
    });
}

/* ---------- Sicherheitsabfrage für Formulare mit data-bestaetigen="Frage?" ---------- */

document.querySelectorAll('form[data-bestaetigen]').forEach(formular => {
    formular.addEventListener('submit', ereignis => {
        if (!confirm(formular.dataset.bestaetigen)) {
            ereignis.preventDefault();
        }
    });
});

/* ---------- Vorlesen (Web Speech API, im Browser eingebaut) ---------- */

// Liste der Stimmen. Chrome lädt sie erst nach dem Seitenaufruf nach,
// deshalb holen wir sie beim Start UND sobald "voiceschanged" meldet, dass sie da sind.
let stimmen = [];
function ladeStimmen() {
    stimmen = speechSynthesis.getVoices();
}
if ('speechSynthesis' in window) {
    ladeStimmen();
    speechSynthesis.addEventListener('voiceschanged', ladeStimmen);
}

/** Sucht eine Stimme für die Sprache: erst genau (de-DE), sonst nur nach Sprache (de). */
function findeStimme(sprache) {
    const genau = stimmen.find(s => s.lang.replace('_', '-') === sprache);
    if (genau) return genau;
    return stimmen.find(s => s.lang.startsWith(sprache.slice(0, 2)));
}

/**
 * Liest einen Text vor. sprache z. B. "de-DE" oder "en-US".
 * beiEnde (optional) wird aufgerufen, wenn fertig vorgelesen wurde.
 */
function vorlesen(text, sprache, beiEnde) {
    if (!('speechSynthesis' in window)) {
        alert('Dein Browser unterstützt leider keine Sprachausgabe.');
        return;
    }
    speechSynthesis.cancel(); // laufende Ausgabe abbrechen
    if (stimmen.length === 0) ladeStimmen(); // falls die Liste noch leer war
    const aeusserung = new SpeechSynthesisUtterance(text);
    aeusserung.lang = sprache || 'de-DE';
    // Passende Stimme suchen (falls vorhanden), sonst nimmt der Browser die Standardstimme.
    const stimme = speechSynthesis.getVoices().find(s => s.lang.replace('_', '-').startsWith(aeusserung.lang.slice(0, 2)));
    if (stimme) aeusserung.voice = stimme;
    if (beiEnde) aeusserung.onend = beiEnde;
    speechSynthesis.speak(aeusserung);
}

// Alle Knöpfe mit data-vorlesen="Text" lesen beim Klick ihren Text vor.
document.addEventListener('click', ereignis => {
    const knopf = ereignis.target.closest('[data-vorlesen]');
    if (knopf) vorlesen(knopf.dataset.vorlesen, knopf.dataset.sprache);
});

/* ---------- Stern-Knöpfe in der Kartenliste (set.php) ---------- */

document.querySelectorAll('[data-stern]').forEach(knopf => {
    knopf.addEventListener('click', async () => {
        const daten = await sendeAnServer({ aktion: 'stern', id: knopf.dataset.set, karte: knopf.dataset.stern });
        if (daten) knopf.classList.toggle('aktiv', daten.stern);
    });
});

/* ---------- Kommunikation mit dem Server (api.php) ---------- */

// Schickt Daten per POST an api.php und gibt die JSON-Antwort zurück (oder null bei Fehler).
async function sendeAnServer(felder) {
    const formular = new FormData();
    for (const [name, wert] of Object.entries(felder)) {
        formular.append(name, wert);
    }
    try {
        const antwort = await fetch('api.php', { method: 'POST', body: formular });
        return await antwort.json();
    } catch (fehler) {
        console.error('Speichern fehlgeschlagen:', fehler);
        return null;
    }
}

// Speichert, ob eine Karte richtig oder falsch beantwortet wurde. 
function speichereAntwort(setId, karteId, richtig) {
    return sendeAnServer({ aktion: 'antwort', id: setId, karte: karteId, richtig: richtig ? '1' : '0' });
}

// Lädt das Set für den aktuellen Lernmodus (Infos stehen in #lernbereich, siehe lernen.php).
// Gibt ein Objekt { set, karten } zurück. "karten" sind ggf. nur die mit Stern. 
async function ladeLernSet() {
    const bereich = document.getElementById('lernbereich');
    const hinweis = document.getElementById('lade-hinweis');
    const antwort = await fetch('api.php?aktion=set&id=' + encodeURIComponent(bereich.dataset.set));
    const set = await antwort.json();

    let karten = set.karten;
    if (bereich.dataset.nurSterne === '1') {
        karten = karten.filter(karte => karte.stern);
        if (karten.length === 0) {
            hinweis.textContent = 'In diesem Set ist noch keine Karte markiert, deshalb werden alle Karten verwendet.';
            karten = set.karten;
        } else {
            hinweis.textContent = 'Es werden nur die ' + karten.length + ' markierten Karten gelernt.';
        }
    } else {
        hinweis.hidden = true;
    }
    return { set, karten };
}

/* ---------- Hilfsfunktionen ---------- */

// Gibt eine gemischte Kopie des Arrays zurück.
function mische(liste) {
    const kopie = [...liste];
    for (let i = kopie.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [kopie[i], kopie[j]] = [kopie[j], kopie[i]];
    }
    return kopie;
}

// Liefert für eine Karte Frage und Antwort.
// antwortMit = "begriff"    -> Definition wird gezeigt, Begriff ist die Antwort
// antwortMit = "definition" -> Begriff wird gezeigt, Definition ist die Antwort
function frageUndAntwort(karte, set, antwortMit) {
    if (antwortMit === 'definition') {
        return { frage: karte.begriff, antwort: karte.definition,
                 frageSprache: set.sprache_begriff, antwortSprache: set.sprache_definition
                };
    }
    return { frage: karte.definition, antwort: karte.begriff,
             frageSprache: set.sprache_definition, antwortSprache: set.sprache_begriff
            };
}

// Sucht bis zu "anzahl" falsche Antwortmöglichkeiten aus anderen Karten desselben Sets.
// Doppelte Texte werden ausgelassen.
function falscheAntworten(karte, alleKarten, antwortMit, anzahl) {
    const richtig = karte[antwortMit];
    const kandidaten = [];
    for (const andere of mische(alleKarten)) {
        const text = andere[antwortMit];
        if (andere.id !== karte.id && text !== richtig && !kandidaten.includes(text)) {
            kandidaten.push(text);
        }
        if (kandidaten.length === anzahl) break;
    }
    return kandidaten;
}

// Text vereinfachen: klein, ohne doppelte Leerzeichen und ohne Satzzeichen am Ende.
function normalisiere(text) {
    return text.normalize('NFC').toLowerCase().trim()
        .replace(/\s+/g, ' ')
        .replace(/[.,;:!?]+$/, '');
}

// Zählt, wie viele Buchstaben man einfügen, löschen oder ändern muss,
// um aus a das Wort b zu machen (Levenshtein-Distanz).
// Beispiel: zaehleTippfehler("javscript", "javascript") ergibt 1
function zaehleTippfehler(a, b) {
    const zeile = Array.from({ length: b.length + 1 }, (_, i) => i);
    for (let i = 1; i <= a.length; i++) {
        let vorher = zeile[0];
        zeile[0] = i;
        for (let j = 1; j <= b.length; j++) {
            const merk = zeile[j];
            const kosten = a[i - 1] === b[j - 1] ? 0 : 1;
            zeile[j] = Math.min(zeile[j] + 1, zeile[j - 1] + 1, vorher + kosten);
            vorher = merk;
        }
    }
    return zeile[b.length];
}

// Prüft eine eingetippte Antwort tolerant.
// Rückgabe: "richtig", "fast" (kleiner Tippfehler, zählt als richtig) oder "falsch".
function pruefeAntwort(eingabe, loesung) {
    const a = normalisiere(eingabe);
    const b = normalisiere(loesung);
    if (a === '') return 'falsch';
    if (a === b) return 'richtig';
    // Erlaubte Tippfehler: ca. 1 pro 6 Zeichen, bei sehr kurzen Wörtern keiner.
    const erlaubt = b.length < 4 ? 0 : Math.max(1, Math.floor(b.length / 6));
    return zaehleTippfehler(a, b) <= erlaubt ? 'fast' : 'falsch';
}

// Setzt Text sicher in HTML ein (verhindert, dass Karteninhalt als HTML ausgeführt wird).
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}