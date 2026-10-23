<?php
/*
 * set.php?id=... – Übersicht über ein Lernset:
 * Statistik, Auswahl des Lernmodus und Liste aller Karten (mit Stern und Vorlesen).
 */
require __DIR__ . '/inc/daten.php';

$id = $_GET['id'] ?? '';
$set = gueltige_id($id) ? lade_set($id) : null;
if ($set === null) {
    http_response_code(404);
    exit('Set nicht gefunden. <a href="index.php">Zur Startseite</a>');
}

// Knöpfe "Löschen" und "Fortschritt zurücksetzen" schicken ein POST-Formular an diese Seite.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aktion = $_POST['aktion'] ?? '';
    if ($aktion === 'loeschen') {
        loesche_set($id);
        header('Location: index.php');
        exit;
    }
    if ($aktion === 'zuruecksetzen') {
        foreach ($set['karten'] as &$karte) {
            $karte['richtig'] = 0;
            $karte['falsch'] = 0;
            $karte['gekonnt'] = false;
        }
        unset($karte);
        $set['bestzeit_zuordnen'] = null;
        speichere_set($set);
        header('Location: set.php?id=' . $id);
        exit;
    }
}

// Alle Lernmodi: Schlüssel = Wert für ?modus=..., dazu Symbol, Name und kurze Erklärung.
$modi = require __DIR__ . '/inc/modi.php';

$stat = statistik($set);
$titel = $set['name'];
require __DIR__ . '/inc/kopf.php';
?>

<section class="kopfbereich">
    <div>
        <p class="leise">📁 <?= e($set['fach'] !== '' ? $set['fach'] : 'Ohne Fach') ?></p>
        <h1><?= e($set['name']) ?></h1>
        <?php if ($set['beschreibung'] !== ''): ?>
            <p><?= e($set['beschreibung']) ?></p>
        <?php endif; ?>
    </div>
    <div class="aktionen">
        <a class="knopf zweitrangig" href="bearbeiten.php?id=<?= e($id) ?>">✏️ Bearbeiten</a>
        <form method="post" data-bestaetigen="Lernfortschritt für dieses Set wirklich zurücksetzen?">
            <input type="hidden" name="aktion" value="zuruecksetzen">
            <button type="submit" class="knopf zweitrangig">↺ Fortschritt zurücksetzen</button>
        </form>
        <form method="post" data-bestaetigen="Set „<?= e($set['name']) ?>“ wirklich löschen?">
            <input type="hidden" name="aktion" value="loeschen">
            <button type="submit" class="knopf gefahr">🗑 Löschen</button>
        </form>
    </div>
</section>

<section class="box statistik">
    <div><strong><?= $stat['gesamt'] ?></strong><span>Karten</span></div>
    <div><strong><?= $stat['gekonnt'] ?></strong><span>gekonnt</span></div>
    <div><strong><?= $stat['gesamt'] - $stat['gekonnt'] ?></strong><span>noch lernen</span></div>
    <div><strong><?= $stat['sterne'] ?></strong><span>mit ★ markiert</span></div>
    <?php if ($set['bestzeit_zuordnen'] !== null): ?>
        <div><strong><?= number_format($set['bestzeit_zuordnen'], 1, ',', '') ?> s</strong><span>Bestzeit Zuordnen</span></div>
    <?php endif; ?>
    <div class="fortschritt gross" title="<?= $stat['prozent'] ?> % gekonnt"><span style="width: <?= $stat['prozent'] ?>%"></span></div>
</section>

<!-- Modus-Auswahl: ein normales GET-Formular. Jeder Knopf schickt einen anderen Wert für "modus". -->
<form class="modi" method="get" action="lernen.php">
    <input type="hidden" name="id" value="<?= e($id) ?>">
    <div class="modi-raster">
        <?php foreach ($modi as $schluessel => $modus): ?>
            <button type="submit" name="modus" value="<?= $schluessel ?>" class="modus-kachel">
                <span class="symbol"><?= $modus['symbol'] ?></span>
                <strong><?= $modus['name'] ?></strong>
                <span class="leise"><?= $modus['text'] ?></span>
            </button>
        <?php endforeach; ?>
    </div>
    <label class="schalter">
        <input type="checkbox" name="sterne" value="1">
        Nur mit ★ markierte Karten lernen
    </label>
</form>

<section class="box">
    <h2>Alle Karten (<?= $stat['gesamt'] ?>)</h2>
    <ul class="kartenliste">
        <?php foreach ($set['karten'] as $karte): ?>
            <li class="<?= $karte['gekonnt'] ? 'gekonnt' : '' ?>">
                <span class="begriff"><?= e($karte['begriff']) ?></span>
                <span class="definition"><?= e($karte['definition']) ?></span>
                <span class="knoepfe">
                    <!-- data-*-Attribute werden von js/gemeinsam.js ausgelesen. -->
                    <button type="button" class="knopf-klein" title="Vorlesen"
                            data-vorlesen="<?= e($karte['begriff']) ?>" data-sprache="<?= e($set['sprache_begriff']) ?>">🔊</button>
                    <button type="button" class="knopf-klein stern <?= $karte['stern'] ? 'aktiv' : '' ?>" title="Markieren"
                            data-stern="<?= e($karte['id']) ?>" data-set="<?= e($id) ?>">★</button>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/inc/fuss.php'; ?>
