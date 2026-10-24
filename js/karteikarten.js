/*
 * karteikarten.js – Modus "Karteikarten":
 * Karte umdrehen (mit CSS-Animation), vor/zurück blättern, mischen,
 * Vorder- und Rückseite tauschen, vorlesen und automatisch abspielen.
 */

const kk = {
    set: null,
    karten: [],         // Karten in der aktuellen Reihenfolge
    position: 0,        // welche Karte gerade angezeigt wird
    begriffVorne: true, // false = Seiten getauscht
    auto: false,        // läuft "Automatisch abspielen"?
};

const kkKarte = document.getElementById('kk-karte');

/** Zeigt die Karte an der aktuellen Position an (immer mit der Vorderseite zuerst). */
function zeigeKarte() {
    const karte = kk.karten[kk.position];
    // Neue Karte ohne Dreh-Animation auf die Vorderseite legen, sonst sähe man kurz die neue Rückseite.
    kkKarte.classList.add('ohne-animation');
    kkKarte.classList.remove('umgedreht');
    void kkKarte.offsetWidth; // Browser zwingen, den Zustand sofort zu übernehmen
    kkKarte.classList.remove('ohne-animation');
    document.getElementById('kk-vorne').textContent = kk.begriffVorne ? karte.begriff : karte.definition;
    document.getElementById('kk-hinten').textContent = kk.begriffVorne ? karte.definition : karte.begriff;
    document.getElementById('kk-position').textContent = kk.position + 1;
    document.getElementById('kk-anzahl').textContent = kk.karten.length;
    document.getElementById('kk-stern').classList.toggle('aktiv', karte.stern);
}

function umdrehen() {
    kkKarte.classList.toggle('umgedreht');
}

function weiter() {
    kk.position = (kk.position + 1) % kk.karten.length; // nach der letzten wieder von vorn
    zeigeKarte();
}

function zurueck() {
    kk.position = (kk.position - 1 + kk.karten.length) % kk.karten.length;
    zeigeKarte();
}

/** Liest die gerade sichtbare Seite in der passenden Sprache vor. */
function leseSichtbareSeite(beiEnde) {
    const karte = kk.karten[kk.position];
    const hintenSichtbar = kkKarte.classList.contains('umgedreht');
    const zeigtBegriff = kk.begriffVorne !== hintenSichtbar;
    if (zeigtBegriff) {
        vorlesen(karte.begriff, kk.set.sprache_begriff, beiEnde);
    } else {
        vorlesen(karte.definition, kk.set.sprache_definition, beiEnde);
    }
}

/** Automatisch abspielen: Vorderseite vorlesen, umdrehen, Rückseite vorlesen, nächste Karte … */
function autoSchritt() {
    if (!kk.auto) return;
    leseSichtbareSeite(() => {
        if (!kk.auto) return;
        setTimeout(() => {
            if (!kk.auto) return;
            if (kkKarte.classList.contains('umgedreht')) {
                weiter();
            } else {
                umdrehen();
            }
            setTimeout(autoSchritt, 700); // kurz warten, bis die Dreh-Animation fertig ist
        }, 800);
    });
}

function schalteAuto() {
    kk.auto = !kk.auto;
    document.getElementById('kk-auto').textContent = kk.auto ? 'Anhalten' : 'Automatisch abspielen';
    if (kk.auto) {
        autoSchritt();
    } else if ('speechSynthesis' in window) {
        speechSynthesis.cancel();
    }
}

async function schalteStern() {
    const karte = kk.karten[kk.position];
    const daten = await sendeAnServer({ aktion: 'stern', id: kk.set.id, karte: karte.id });
    if (daten) {
        karte.stern = daten.stern;
        zeigeKarte();
    }
}

/* ---------- Ereignisse verbinden ---------- */

kkKarte.addEventListener('click', umdrehen);
document.getElementById('kk-umdrehen').addEventListener('click', umdrehen);
document.getElementById('kk-weiter').addEventListener('click', weiter);
document.getElementById('kk-zurueck').addEventListener('click', zurueck);
document.getElementById('kk-vorlesen').addEventListener('click', () => leseSichtbareSeite());
document.getElementById('kk-auto').addEventListener('click', schalteAuto);
document.getElementById('kk-stern').addEventListener('click', schalteStern);

document.getElementById('kk-mischen').addEventListener('click', () => {
    kk.karten = mische(kk.karten);
    kk.position = 0;
    zeigeKarte();
});

document.getElementById('kk-tauschen').addEventListener('click', () => {
    kk.begriffVorne = !kk.begriffVorne;
    zeigeKarte();
});

// Tastatursteuerung
document.addEventListener('keydown', ereignis => {
    // Auf Knöpfen löst die Leertaste schon selbst einen Klick aus -> hier nichts doppelt machen.
    if (ereignis.target.closest('button, input, textarea, select')) return;
    if (ereignis.key === ' ') {
        ereignis.preventDefault(); // sonst scrollt die Seite
        umdrehen();
    } else if (ereignis.key === 'ArrowRight') {
        weiter();
    } else if (ereignis.key === 'ArrowLeft') {
        zurueck();
    }
});

/* ---------- Start: Karten vom Server laden ---------- */

ladeLernSet().then(({ set, karten }) => {
    kk.set = set;
    kk.karten = karten;
    document.getElementById('karteikarten').hidden = false;
    zeigeKarte();
});
