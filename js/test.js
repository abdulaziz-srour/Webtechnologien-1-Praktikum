/*
 * test.js – Modus "Test":
 * Erstellt einen Test aus Multiple Choice, Wahr/Falsch und Freitext-Fragen.
 * Falsche Antwortmöglichkeiten kommen aus anderen Karten desselben Sets.
 * Nach dem Abgeben gibt es eine Punktzahl und jede Frage wird markiert.
 */

const ts = {
    set: null,
    karten: [],
    fragen: [], // Liste der Fragen: { typ, karte, frage, loesung, ... }
    antwortMit: 'begriff',
};

const tsListe = document.getElementById('ts-liste');
const typNamen = { mc: 'Multiple Choice', wf: 'Wahr oder falsch?', frei: 'Freitext' };

/** Erstellt die Fragen aus den Einstellungen. */
function erstelleTest(anzahl, typen) {
    const fragen = [];
    mische(ts.karten).slice(0, anzahl).forEach((karte, i) => {
        // Typ abwechselnd aus den gewählten Typen nehmen, damit alle vorkommen.
        let typ = typen[i % typen.length];
        if (ts.karten.length < 2) typ = 'frei'; // MC und Wahr/Falsch brauchen andere Karten
        const fa = frageUndAntwort(karte, ts.set, ts.antwortMit);
        const frage = { typ, karte, frage: fa.frage, loesung: fa.antwort };

        if (typ === 'mc') {
            frage.optionen = mische([fa.antwort, ...falscheAntworten(karte, ts.set.karten, ts.antwortMit, 3)]);
            if (frage.optionen.length < 2) frage.typ = 'frei'; // keine anderen Antworten vorhanden
        } else if (typ === 'wf') {
            // In der Hälfte der Fälle die richtige Antwort zeigen, sonst die einer anderen Karte.
            const falsche = falscheAntworten(karte, ts.set.karten, ts.antwortMit, 1);
            frage.wahr = Math.random() < 0.5 || falsche.length === 0;
            frage.gezeigt = frage.wahr ? fa.antwort : falsche[0];
        }
        fragen.push(frage);
    });
    return mische(fragen);
}

/** Baut das HTML für alle Fragen. */
function zeigeTest() {
    tsListe.innerHTML = '';
    ts.fragen.forEach((frage, nr) => {
        const block = document.createElement('div');
        block.className = 'frage-karte';
        let html = '<p class="art">Frage ' + (nr + 1) + ' von ' + ts.fragen.length + ' · ' + typNamen[frage.typ] + '</p>';

        if (frage.typ === 'mc') {
            html += '<p class="frage-text">' + escapeHtml(frage.frage) + '</p><div class="optionen">';
            frage.optionen.forEach(option => {
                html += '<label class="option"><input type="radio" name="f' + nr + '" value="' + escapeHtml(option) + '"> '
                      + escapeHtml(option) + '</label>';
            });
            html += '</div>';
        } else if (frage.typ === 'wf') {
            html += '<p class="frage-text">' + escapeHtml(frage.frage) + '<br><span class="leise">gehört zu:</span> '
                  + escapeHtml(frage.gezeigt) + '</p><div class="optionen">'
                  + '<label class="option"><input type="radio" name="f' + nr + '" value="wahr"> Wahr</label>'
                  + '<label class="option"><input type="radio" name="f' + nr + '" value="falsch"> Falsch</label></div>';
        } else {
            html += '<p class="frage-text">' + escapeHtml(frage.frage) + '</p>'
                  + '<input type="text" name="f' + nr + '" placeholder="Antwort eintippen …">';
        }
        html += '<div class="feedback" hidden></div>';
        block.innerHTML = html;
        tsListe.appendChild(block);
    });
}

/** Alle Antworten auswerten, markieren und Punktzahl anzeigen. */
function werteTestAus() {
    const formular = document.getElementById('ts-fragen');
    let punkte = 0;

    ts.fragen.forEach((frage, nr) => {
        const block = tsListe.children[nr];
        const feld = formular.elements['f' + nr];
        const eingabe = feld ? feld.value : ''; // bei Radio-Knöpfen: Wert des gewählten Knopfs

        let richtig;
        if (frage.typ === 'mc') {
            richtig = eingabe === frage.loesung;
        } else if (frage.typ === 'wf') {
            richtig = eingabe === (frage.wahr ? 'wahr' : 'falsch');
        } else {
            richtig = pruefeAntwort(eingabe, frage.loesung) !== 'falsch';
        }
        if (richtig) punkte++;

        // Eingaben sperren; bei Auswahlfragen die richtige Option grün, die falsch gewählte rot färben.
        const sollWert = frage.typ === 'mc' ? frage.loesung : (frage.wahr ? 'wahr' : 'falsch');
        block.querySelectorAll('input').forEach(input => {
            input.disabled = true;
            if (input.type !== 'radio') return;
            if (input.value === sollWert) input.parentElement.classList.add('richtig');
            else if (input.checked) input.parentElement.classList.add('falsch');
        });
        const feedback = block.querySelector('.feedback');
        feedback.hidden = false;
        feedback.className = 'feedback ' + (richtig ? 'richtig' : 'falsch');
        let text = richtig ? 'Richtig' : 'Falsch';
        if (frage.typ === 'wf' && !frage.wahr) {
            text += ' – richtig wäre: ' + frage.loesung;
        } else if (!richtig) {
            text += ' – richtig ist: ' + frage.loesung;
        }
        feedback.textContent = text;

        speichereAntwort(ts.set.id, frage.karte.id, richtig);
    });

    const prozent = Math.round(punkte / ts.fragen.length * 100);
    document.getElementById('ts-prozent').textContent = prozent + ' %';
    document.getElementById('ts-prozent').className = 'gross';
    document.getElementById('ts-punkte').textContent = punkte + ' von ' + ts.fragen.length + ' Fragen richtig'
        + '';
    document.getElementById('ts-ergebnis').hidden = false;
    document.getElementById('ts-abgeben').hidden = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* ---------- Ereignisse ---------- */

document.getElementById('ts-einstellungen').addEventListener('submit', ereignis => {
    ereignis.preventDefault();
    const typen = [...document.querySelectorAll('input[name="typ"]:checked')].map(box => box.value);
    const fehler = document.getElementById('ts-fehler');
    if (typen.length === 0) {
        fehler.textContent = 'Bitte mindestens einen Fragetyp auswählen.';
        fehler.hidden = false;
        return;
    }
    fehler.hidden = true;
    const anzahl = Math.max(1, Math.min(ts.karten.length, Number(document.getElementById('ts-anzahl').value) || 1));
    ts.antwortMit = document.getElementById('ts-antwort-mit').value;
    ts.fragen = erstelleTest(anzahl, typen);
    zeigeTest();
    ereignis.target.hidden = true;
    document.getElementById('ts-fragen').hidden = false;
    document.getElementById('ts-abgeben').hidden = false;
});

document.getElementById('ts-fragen').addEventListener('submit', ereignis => {
    ereignis.preventDefault();
    const offen = ts.fragen.filter((frage, nr) => !ereignis.target.elements['f' + nr].value).length;
    if (offen > 0 && !confirm(offen + ' Frage(n) sind noch unbeantwortet. Trotzdem abgeben?')) return;
    werteTestAus();
});

document.getElementById('ts-neuer-test').addEventListener('click', () => {
    document.getElementById('ts-ergebnis').hidden = true;
    document.getElementById('ts-fragen').hidden = true;
    document.getElementById('ts-einstellungen').hidden = false;
});

/* ---------- Start ---------- */

ladeLernSet().then(({ set, karten }) => {
    ts.set = set;
    ts.karten = karten;
    document.getElementById('ts-max').textContent = karten.length;
    document.getElementById('ts-anzahl').max = karten.length;
    document.getElementById('ts-anzahl').value = Math.min(10, karten.length);
    document.getElementById('test').hidden = false;
});
