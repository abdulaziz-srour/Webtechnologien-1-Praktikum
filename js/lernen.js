/*
 * lernen.js – Modus "Lernen" (wie "Lernen" bei Quizlet).
 *
 * Jede Karte hat eine Stufe:
 *   0 = neu        -> wird als Multiple-Choice-Frage gestellt
 *   1 = am Lernen  -> wird als Schreib-Frage gestellt
 *   2 = gelernt    -> fertig
 * Richtige Antwort: Stufe + 1. Falsche Antwort: Stufe bleibt, die Karte kommt nach
 * wenigen anderen Karten wieder dran. Der Modus ist fertig, wenn alle Karten Stufe 2 haben.
 */

const ln = {
    set: null,
    karten: [],
    warteschlange: [], // Karten, die noch gefragt werden (vorne = als Nächstes)
    stufe: {},         // Karten-ID -> Stufe (0, 1 oder 2)
    aktuell: null,     // aktuell gefragte Karte
    antwortMit: 'begriff',
    beantwortet: false,
    richtig: false,    // Ergebnis der letzten Antwort
};

const lnOptionen = document.getElementById('ln-optionen');
const lnFormular = document.getElementById('ln-formular');
const lnEingabe = document.getElementById('ln-eingabe');
const lnFeedback = document.getElementById('ln-feedback');

function starteLernen() {
    ln.antwortMit = document.getElementById('ln-antwort-mit').value;
    ln.stufe = {};
    // Mit nur einer Karte gibt es kein sinnvolles Multiple Choice -> gleich mit Schreiben anfangen.
    const startStufe = ln.karten.length < 2 ? 1 : 0;
    ln.karten.forEach(karte => { ln.stufe[karte.id] = startStufe; });
    ln.warteschlange = mische(ln.karten);
    document.getElementById('ln-ende').hidden = true;
    document.getElementById('ln-frage').hidden = false;
    zeigeLernFortschritt();
    naechsteLernFrage();
}

/** Zähler und Fortschrittsbalken aktualisieren. */
function zeigeLernFortschritt() {
    const stufen = Object.values(ln.stufe);
    document.getElementById('ln-neu').textContent = stufen.filter(s => s === 0).length;
    document.getElementById('ln-lernend').textContent = stufen.filter(s => s === 1).length;
    document.getElementById('ln-gelernt').textContent = stufen.filter(s => s === 2).length;
    const summe = stufen.reduce((a, b) => a + b, 0);
    document.getElementById('ln-balken').style.width = (summe / (stufen.length * 2) * 100) + '%';
}

function naechsteLernFrage() {
    if (ln.warteschlange.length === 0) {
        zeigeLernEnde();
        return;
    }
    ln.aktuell = ln.warteschlange.shift();
    ln.beantwortet = false;
    const fa = frageUndAntwort(ln.aktuell, ln.set, ln.antwortMit);
    document.getElementById('ln-frage-text').textContent = fa.frage;
    lnFeedback.hidden = true;
    lnFeedback.className = 'feedback';
    lnOptionen.innerHTML = '';

    if (ln.stufe[ln.aktuell.id] === 0) {
        // Multiple Choice: richtige Antwort + bis zu 3 falsche aus anderen Karten.
        document.getElementById('ln-art').textContent = 'Multiple Choice · wähle die richtige Antwort (Tasten 1–4)';
        lnFormular.hidden = true;
        const auswahl = mische([fa.antwort, ...falscheAntworten(ln.aktuell, ln.set.karten, ln.antwortMit, 3)]);
        auswahl.forEach((text, i) => {
            const knopf = document.createElement('button');
            knopf.type = 'button';
            knopf.className = 'option';
            knopf.textContent = (i + 1) + '. ' + text;
            knopf.dataset.text = text;
            knopf.addEventListener('click', () => pruefeLernAuswahl(knopf));
            lnOptionen.appendChild(knopf);
        });
    } else {
        document.getElementById('ln-art').textContent = 'Schreiben · tippe die Antwort ein';
        lnFormular.hidden = false;
        lnEingabe.value = '';
        lnEingabe.disabled = false;
        lnEingabe.focus();
    }
}

/** Multiple Choice: angeklickte Option prüfen und richtig/falsch einfärben. */
function pruefeLernAuswahl(knopf) {
    if (ln.beantwortet) return;
    const loesung = frageUndAntwort(ln.aktuell, ln.set, ln.antwortMit).antwort;
    lnOptionen.querySelectorAll('.option').forEach(option => {
        option.disabled = true;
        if (option.dataset.text === loesung) option.classList.add('richtig');
    });
    const richtig = knopf.dataset.text === loesung;
    if (!richtig) knopf.classList.add('falsch');
    zeigeLernFeedback(richtig, richtig ? 'Richtig!' : 'Leider falsch. Richtig ist: ' + loesung);
}

/** Schreiben: Eingabe tolerant prüfen. */
lnFormular.addEventListener('submit', ereignis => {
    ereignis.preventDefault();
    if (ln.beantwortet) return;
    const loesung = frageUndAntwort(ln.aktuell, ln.set, ln.antwortMit).antwort;
    const ergebnis = pruefeAntwort(lnEingabe.value, loesung);
    lnEingabe.disabled = true;
    if (ergebnis === 'richtig') {
        zeigeLernFeedback(true, 'Richtig!');
    } else if (ergebnis === 'fast') {
        zeigeLernFeedback(true, 'Fast richtig (kleiner Tippfehler) – genau: ' + loesung);
    } else {
        zeigeLernFeedback(false, 'Leider falsch. Richtig ist: ' + loesung, true);
    }
});

function zeigeLernFeedback(richtig, text, dochRichtigMoeglich = false) {
    ln.beantwortet = true;
    ln.richtig = richtig;
    lnFeedback.hidden = false;
    lnFeedback.className = 'feedback ' + (richtig ? 'richtig' : 'falsch');
    document.getElementById('ln-feedback-text').textContent = text;
    document.getElementById('ln-doch-richtig').hidden = !dochRichtigMoeglich;
    document.getElementById('ln-weiter').focus();
}

/** "Weiter": Ergebnis übernehmen, auf dem Server speichern und nächste Frage stellen. */
function lernWeiter() {
    const karte = ln.aktuell;
    if (ln.richtig) {
        ln.stufe[karte.id]++;
        if (ln.stufe[karte.id] < 2) ln.warteschlange.push(karte); // nächste Stufe später
    } else {
        // Nach ca. 3 anderen Karten noch einmal fragen.
        ln.warteschlange.splice(Math.min(3, ln.warteschlange.length), 0, karte);
    }
    speichereAntwort(ln.set.id, karte.id, ln.richtig);
    zeigeLernFortschritt();
    naechsteLernFrage();
}

function zeigeLernEnde() {
    document.getElementById('ln-frage').hidden = true;
    document.getElementById('ln-ende-anzahl').textContent = ln.karten.length;
    document.getElementById('ln-ende').hidden = false;
}

/* ---------- Ereignisse ---------- */

document.getElementById('ln-weiter').addEventListener('click', lernWeiter);
document.getElementById('ln-nochmal').addEventListener('click', starteLernen);
document.getElementById('ln-antwort-mit').addEventListener('change', starteLernen);
document.getElementById('ln-vorlesen').addEventListener('click', () => {
    const fa = frageUndAntwort(ln.aktuell, ln.set, ln.antwortMit);
    vorlesen(fa.frage, fa.frageSprache);
});
document.getElementById('ln-doch-richtig').addEventListener('click', () => {
    zeigeLernFeedback(true, 'Okay, wird als richtig gezählt.');
});

document.addEventListener('keydown', ereignis => {
    if (document.getElementById('lernen').hidden) return;
    if (ln.beantwortet && ereignis.key === 'Enter') {
        ereignis.preventDefault(); // verhindert, dass Enter gleichzeitig noch etwas anderes auslöst
        lernWeiter();
        return;
    }
    // Zahlentasten 1–4 wählen im Multiple Choice eine Antwort.
    const optionen = lnOptionen.querySelectorAll('.option');
    const nummer = Number(ereignis.key);
    if (!ln.beantwortet && nummer >= 1 && nummer <= optionen.length) {
        pruefeLernAuswahl(optionen[nummer - 1]);
    }
});

/* ---------- Start ---------- */

ladeLernSet().then(({ set, karten }) => {
    ln.set = set;
    ln.karten = karten;
    document.getElementById('lernen').hidden = false;
    starteLernen();
});
