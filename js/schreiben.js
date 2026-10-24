/*
 * schreiben.js – Modus "Schreiben":
 * Die Antwort wird eingetippt und tolerant geprüft (siehe pruefeAntwort in gemeinsam.js).
 * Nach jeder Runde werden nur die falsch beantworteten Karten wiederholt,
 * bis alle Karten einmal richtig waren.
 */

const sc = {
    set: null,
    karten: [],
    runde: 1,
    rundenKarten: [],  // Karten der aktuellen Runde
    nr: 0,             // Index der aktuellen Karte in rundenKarten
    falsche: [],       // in dieser Runde falsch beantwortete Karten
    antwortMit: 'begriff',
    beantwortet: false,
    richtig: false,
};

const scFormular = document.getElementById('sc-formular');
const scEingabe = document.getElementById('sc-eingabe');
const scFeedback = document.getElementById('sc-feedback');

/** Kompletten Durchgang neu starten (alle Karten, Runde 1). */
function starteSchreiben() {
    sc.antwortMit = document.getElementById('sc-antwort-mit').value;
    sc.runde = 0;
    starteRunde(sc.karten);
}

/** Eine Runde mit den übergebenen Karten (gemischt) beginnen. */
function starteRunde(karten) {
    sc.runde++;
    sc.rundenKarten = mische(karten);
    sc.nr = 0;
    sc.falsche = [];
    document.getElementById('sc-rundenende').hidden = true;
    document.getElementById('sc-frage').hidden = false;
    zeigeSchreibFrage();
}

function zeigeSchreibFrage() {
    const karte = sc.rundenKarten[sc.nr];
    sc.beantwortet = false;
    document.getElementById('sc-frage-text').textContent = frageUndAntwort(karte, sc.set, sc.antwortMit).frage;
    document.getElementById('sc-runde').textContent = sc.runde;
    document.getElementById('sc-nr').textContent = sc.nr + 1;
    document.getElementById('sc-anzahl').textContent = sc.rundenKarten.length;
    document.getElementById('sc-richtig').textContent = sc.nr - sc.falsche.length;
    document.getElementById('sc-falsch').textContent = sc.falsche.length;
    scFeedback.hidden = true;
    scEingabe.value = '';
    scEingabe.disabled = false;
    scEingabe.focus();
}

/** Ergebnis anzeigen. ergebnis = "richtig", "fast" oder "falsch". */
function zeigeSchreibErgebnis(ergebnis, eingabe) {
    const loesung = frageUndAntwort(sc.rundenKarten[sc.nr], sc.set, sc.antwortMit).antwort;
    sc.beantwortet = true;
    sc.richtig = ergebnis !== 'falsch';
    scEingabe.disabled = true;

    let text;
    if (ergebnis === 'richtig') {
        text = 'Richtig!';
    } else if (ergebnis === 'fast') {
        text = 'Fast richtig – kleiner Tippfehler. Genau: „' + loesung + '“';
    } else if (eingabe.trim() === '') {
        text = 'Die richtige Antwort ist: „' + loesung + '“';
    } else {
        text = 'Leider falsch. Deine Antwort: „' + eingabe + '“ – richtig ist: „' + loesung + '“';
    }
    document.getElementById('sc-feedback-text').textContent = text;
    scFeedback.className = 'feedback ' + (sc.richtig ? 'richtig' : 'falsch');
    scFeedback.hidden = false;
    // "Ich hatte doch recht" nur anbieten, wenn wirklich etwas eingetippt wurde.
    document.getElementById('sc-doch-richtig').hidden = sc.richtig || eingabe.trim() === '';
    document.getElementById('sc-weiter').focus();
}

/** Ergebnis übernehmen, speichern und zur nächsten Karte bzw. zum Rundenende. */
function schreibWeiter() {
    const karte = sc.rundenKarten[sc.nr];
    if (!sc.richtig) sc.falsche.push(karte);
    speichereAntwort(sc.set.id, karte.id, sc.richtig);

    sc.nr++;
    if (sc.nr < sc.rundenKarten.length) {
        zeigeSchreibFrage();
    } else {
        zeigeRundenende();
    }
}

function zeigeRundenende() {
    const anzahl = sc.rundenKarten.length;
    const richtig = anzahl - sc.falsche.length;
    document.getElementById('sc-frage').hidden = true;
    document.getElementById('sc-rundenende').hidden = false;
    document.getElementById('sc-ende-prozent').textContent = Math.round(richtig / anzahl * 100) + ' %';
    document.getElementById('sc-ende-text').textContent = richtig + ' von ' + anzahl + ' Karten richtig.';

    const fertig = sc.falsche.length === 0;
    document.getElementById('sc-ende-titel').textContent = fertig
        ? 'Geschafft, alle Karten richtig!'
        : 'Runde ' + sc.runde + ' beendet';
    document.getElementById('sc-naechste-runde').hidden = fertig;
    document.getElementById('sc-naechste-runde').textContent = sc.falsche.length + ' falsche Karte(n) wiederholen';
}

/* ---------- Ereignisse ---------- */

scFormular.addEventListener('submit', ereignis => {
    ereignis.preventDefault();
    if (sc.beantwortet) return;
    const loesung = frageUndAntwort(sc.rundenKarten[sc.nr], sc.set, sc.antwortMit).antwort;
    zeigeSchreibErgebnis(pruefeAntwort(scEingabe.value, loesung), scEingabe.value);
});

document.getElementById('sc-weiss-nicht').addEventListener('click', () => {
    if (!sc.beantwortet) zeigeSchreibErgebnis('falsch', '');
});

document.getElementById('sc-doch-richtig').addEventListener('click', () => {
    sc.richtig = true;
    scFeedback.className = 'feedback richtig';
    document.getElementById('sc-feedback-text').textContent = 'Okay, wird als richtig gezählt.';
    document.getElementById('sc-doch-richtig').hidden = true;
});

document.getElementById('sc-weiter').addEventListener('click', schreibWeiter);
document.getElementById('sc-naechste-runde').addEventListener('click', () => starteRunde(sc.falsche));
document.getElementById('sc-neu-starten').addEventListener('click', starteSchreiben);
document.getElementById('sc-antwort-mit').addEventListener('change', starteSchreiben);
document.getElementById('sc-vorlesen').addEventListener('click', () => {
    const fa = frageUndAntwort(sc.rundenKarten[sc.nr], sc.set, sc.antwortMit);
    vorlesen(fa.frage, fa.frageSprache);
});

document.addEventListener('keydown', ereignis => {
    if (sc.beantwortet && ereignis.key === 'Enter' && !document.getElementById('sc-frage').hidden) {
        ereignis.preventDefault();
        schreibWeiter();
    }
});

/* ---------- Start ---------- */

ladeLernSet().then(({ set, karten }) => {
    sc.set = set;
    sc.karten = karten;
    document.getElementById('schreiben').hidden = false;
    starteSchreiben();
});
