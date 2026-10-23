/*
 * bearbeiten.js – Komfort im Set-Editor (bearbeiten.php):
 *   - neue Kartenzeilen hinzufügen und Zeilen entfernen
 *   - Nummern der Zeilen aktuell halten
 *   - Vorschau, wie viele Karten der Import ergibt
 */

const zeilenBereich = document.getElementById('karten-zeilen');
const vorlage = document.getElementById('zeilen-vorlage');

/** Nummeriert alle sichtbaren Zeilen neu (1, 2, 3, …). */
function nummeriereZeilen() {
    zeilenBereich.querySelectorAll('.karten-zeile .nummer').forEach((nummer, i) => {
        nummer.textContent = i + 1;
    });
}

/** Fügt eine leere Zeile am Ende hinzu und setzt den Cursor hinein. */
function neueZeile() {
    // Jede Zeile braucht im Namen eine eindeutige Nummer, z. B. karten[7][begriff].
    const nr = Number(zeilenBereich.dataset.naechster);
    zeilenBereich.dataset.naechster = nr + 1;

    const html = vorlage.innerHTML.replaceAll('__NR__', nr);
    zeilenBereich.insertAdjacentHTML('beforeend', html);
    nummeriereZeilen();
    zeilenBereich.lastElementChild.querySelector('textarea').focus();
}

document.getElementById('zeile-hinzufuegen').addEventListener('click', neueZeile);

// Entfernen-Knöpfe: ein Listener für den ganzen Bereich, damit er auch für neue Zeilen gilt.
zeilenBereich.addEventListener('click', ereignis => {
    if (ereignis.target.classList.contains('zeile-loeschen')) {
        ereignis.target.closest('.karten-zeile').remove();
        nummeriereZeilen();
    }
});

// Tab in der letzten Definition -> automatisch neue Zeile (wie bei Quizlet).
zeilenBereich.addEventListener('keydown', ereignis => {
    const letzte = zeilenBereich.lastElementChild;
    if (ereignis.key === 'Tab' && !ereignis.shiftKey && letzte &&
        ereignis.target === letzte.querySelectorAll('textarea')[1]) {
        ereignis.preventDefault();
        neueZeile();
    }
});

/* ---------- Import-Vorschau ---------- */

const importFeld = document.getElementById('import');
const trennerFeld = document.getElementById('trenner');
const vorschau = document.getElementById('import-vorschau');

function zeigeImportVorschau() {
    const trenner = trennerFeld.value === 'tab' ? '\t' : trennerFeld.value;
    // Gleiche Regel wie in PHP: Zeile muss das Trennzeichen enthalten, beide Seiten nicht leer.
    const anzahl = importFeld.value.split(/\r?\n/).filter(zeile => {
        const stelle = zeile.indexOf(trenner);
        return stelle > 0 && zeile.slice(0, stelle).trim() !== '' && zeile.slice(stelle + 1).trim() !== '';
    }).length;
    vorschau.textContent = importFeld.value.trim() === '' ? '' : anzahl + ' Karte(n) werden beim Speichern importiert.';
}

importFeld.addEventListener('input', zeigeImportVorschau);
trennerFeld.addEventListener('change', zeigeImportVorschau);
