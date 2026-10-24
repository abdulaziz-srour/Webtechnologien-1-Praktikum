/*
 * zuordnen.js – Modus "Zuordnen":
 * Bis zu 6 Begriffe und ihre Definitionen liegen gemischt als Kacheln da.
 * Passende Paare per Drag-and-Drop (oder zwei Klicks) zusammenführen, auf Zeit.
 * Die Bestzeit speichert PHP (api.php, aktion=bestzeit) im Set.
 */

const ZO_PAARE = 6;      // so viele Paare pro Spiel
const ZO_STRAFE = 1;     // Strafsekunden pro Fehler

const zo = {
    set: null,
    karten: [],
    rest: 0,             // noch offene Paare
    start: 0,            // Startzeitpunkt (ms)
    strafe: 0,           // gesammelte Strafsekunden
    timer: null,         // setInterval-Handle für die Zeitanzeige
    ausgewaehlt: null,   // per Klick ausgewählte Kachel
};

const zoRaster = document.getElementById('zo-raster');

function zoVergangen() {
    return (Date.now() - zo.start) / 1000 + zo.strafe;
}

function formatiereZeit(sekunden) {
    return sekunden.toFixed(1).replace('.', ',');
}

function zeigeBestzeit() {
    const text = zo.set.bestzeit_zuordnen ? 'Deine Bestzeit: ' + formatiereZeit(zo.set.bestzeit_zuordnen) + ' s' : '';
    document.getElementById('zo-bestzeit-start').textContent = text;
}

/** Neues Spiel: Karten auswählen, Kacheln mischen, Timer starten. */
function starteZuordnen() {
    const auswahl = mische(zo.karten).slice(0, ZO_PAARE);
    const kacheln = [];
    auswahl.forEach(karte => {
        // Beide Kacheln eines Paares bekommen dieselbe Karten-ID -> daran erkennt man ein Paar.
        kacheln.push({ id: karte.id, text: karte.begriff });
        kacheln.push({ id: karte.id, text: karte.definition });
    });

    zoRaster.innerHTML = '';
    mische(kacheln).forEach((kachel, i) => {
        const el = document.createElement('div');
        el.className = 'kachel';
        el.textContent = kachel.text;
        el.draggable = true;
        el.dataset.karte = kachel.id;
        el.dataset.nr = i; // eindeutige Nummer, damit man eine Kachel nicht auf sich selbst zieht
        zoRaster.appendChild(el);
    });

    zo.rest = auswahl.length;
    zo.strafe = 0;
    zo.ausgewaehlt = null;
    zo.start = Date.now();
    document.getElementById('zo-rest').textContent = zo.rest;
    document.getElementById('zo-start').hidden = true;
    document.getElementById('zo-ende').hidden = true;
    document.getElementById('zo-spiel').hidden = false;

    clearInterval(zo.timer);
    zo.timer = setInterval(() => {
        document.getElementById('zo-zeit').textContent = formatiereZeit(zoVergangen());
    }, 100);
}

/** Prüft, ob zwei Kacheln zusammenpassen. */
function versucheTreffer(a, b) {
    if (!a || !b || a === b) return;
    if (a.dataset.karte === b.dataset.karte) {
        a.classList.add('fertig');
        b.classList.add('fertig');
        zo.rest--;
        document.getElementById('zo-rest').textContent = zo.rest;
        if (zo.rest === 0) beendeZuordnen();
    } else {
        zo.strafe += ZO_STRAFE;
        [a, b].forEach(el => {
            el.classList.remove('falsch');
            void el.offsetWidth; // Animation neu starten
            el.classList.add('falsch');
            setTimeout(() => el.classList.remove('falsch'), 500);
        });
    }
}

async function beendeZuordnen() {
    clearInterval(zo.timer);
    const zeit = zoVergangen();
    document.getElementById('zo-endzeit').textContent = formatiereZeit(zeit);
    document.getElementById('zo-spiel').hidden = true;
    document.getElementById('zo-ende').hidden = false;

    // Zeit an PHP schicken – der Server entscheidet, ob es eine neue Bestzeit ist.
    const daten = await sendeAnServer({ aktion: 'bestzeit', id: zo.set.id, zeit: zeit.toFixed(1) });
    const rekord = document.getElementById('zo-rekord');
    if (daten && daten.neuer_rekord) {
        rekord.textContent = 'Neue Bestzeit!';
    } else if (daten && daten.bestzeit) {
        rekord.textContent = 'Bestzeit: ' + formatiereZeit(daten.bestzeit) + ' s';
    }
    if (daten) zo.set.bestzeit_zuordnen = daten.bestzeit;
    zeigeBestzeit();
}

/* ---------- Drag-and-Drop (HTML5) ---------- */

let gezogen = null;

zoRaster.addEventListener('dragstart', ereignis => {
    gezogen = ereignis.target.closest('.kachel');
    // Firefox startet das Ziehen nur, wenn Daten gesetzt werden.
    ereignis.dataTransfer.setData('text/plain', gezogen.dataset.nr);
    ereignis.dataTransfer.effectAllowed = 'move';
});

zoRaster.addEventListener('dragover', ereignis => {
    const ziel = ereignis.target.closest('.kachel');
    if (ziel && ziel !== gezogen) {
        ereignis.preventDefault(); // erlaubt das Ablegen
        ziel.classList.add('ziel');
    }
});

zoRaster.addEventListener('dragleave', ereignis => {
    const ziel = ereignis.target.closest('.kachel');
    if (ziel) ziel.classList.remove('ziel');
});

zoRaster.addEventListener('drop', ereignis => {
    ereignis.preventDefault();
    const ziel = ereignis.target.closest('.kachel');
    if (ziel) ziel.classList.remove('ziel');
    versucheTreffer(gezogen, ziel);
    gezogen = null;
});

/* ---------- Alternative: zwei Kacheln nacheinander anklicken (z. B. auf dem Handy) ---------- */

zoRaster.addEventListener('click', ereignis => {
    const kachel = ereignis.target.closest('.kachel');
    if (!kachel) return;
    if (zo.ausgewaehlt === null) {
        zo.ausgewaehlt = kachel;
        kachel.classList.add('ausgewaehlt');
    } else {
        zo.ausgewaehlt.classList.remove('ausgewaehlt');
        versucheTreffer(zo.ausgewaehlt, kachel);
        zo.ausgewaehlt = null;
    }
});

document.getElementById('zo-starten').addEventListener('click', starteZuordnen);
document.getElementById('zo-nochmal').addEventListener('click', starteZuordnen);

/* ---------- Start ---------- */

ladeLernSet().then(({ set, karten }) => {
    zo.set = set;
    zo.karten = karten;
    zeigeBestzeit();
    document.getElementById('zuordnen').hidden = false;
});
