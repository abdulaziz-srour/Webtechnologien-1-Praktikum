<?php
// Übersicht über ein Lernset:
// Statistik, Auswahl des Lernmodus und Liste aller Karten (mit Stern und Vorlesen).
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

<p><a href="index.php">Zurück zur Übersicht</a></p>
<h1><?= escape($set['name']) ?></h1>
<p class="leise">
    Fach: <?= escape($set['fach'] !== '' ? $set['fach'] : 'ohne Fach') ?>
    <?php if ($set['beschreibung'] !== ''): ?> – <?= escape($set['beschreibung']) ?><?php endif; ?>
</p>

<div class="aktionen">
    <a class="knopf zweitrangig" href="bearbeiten.php?id=<?= escape($id) ?>">Bearbeiten</a>
    <form method="post" data-bestaetigen="Lernfortschritt für dieses Set wirklich zurücksetzen?">
        <input type="hidden" name="aktion" value="zuruecksetzen">
        <button type="submit" class="knopf zweitrangig">Fortschritt zurücksetzen</button>
    </form>
    <form method="post" data-bestaetigen="Set „<?= escape($set['name']) ?>“ wirklich löschen?">
        <input type="hidden" name="aktion" value="loeschen">
        <button type="submit" class="knopf gefahr">Löschen</button>
    </form>
</div>

<h2>Statistik</h2>
<p>
    <?= $stat['gesamt'] ?> Karten, davon <?= $stat['gekonnt'] ?> gekonnt (<?= $stat['prozent'] ?> %)
    und <?= $stat['sterne'] ?> markiert.
    <?php if ($set['bestzeit_zuordnen'] !== null): ?>
        Bestzeit beim Zuordnen: <?= number_format($set['bestzeit_zuordnen'], 1, ',', '') ?> s.
    <?php endif; ?>
</p>
<span class="fortschritt" title="<?= $stat['prozent'] ?> % gekonnt"><span style="width: <?= $stat['prozent'] ?>%"></span></span>

<!-- Modus-Auswahl: ein normales GET-Formular. Jeder Knopf schickt einen anderen Wert für "modus". -->
<h2>Lernen</h2>
<form method="get" action="lernen.php">
    <input type="hidden" name="id" value="<?= escape($id) ?>">
    <div class="aktionen">
        <?php foreach ($modi as $schluessel => $modus): ?>
            <button type="submit" name="modus" value="<?= $schluessel ?>" class="knopf" title="<?= $modus['text'] ?>"><?= $modus['name'] ?></button>
        <?php endforeach; ?>
    </div>
    <label class="schalter">
        <input type="checkbox" name="sterne" value="1">
        Nur markierte Karten lernen
    </label>
</form>

<h2>Karten</h2>
<table class="kartentabelle">
    <thead>
        <tr>
            <th>Begriff</th>
            <th>Definition</th>
            <th title="So oft richtig / falsch beantwortet">Richtig / Falsch</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($set['karten'] as $karte): ?>
            <tr class="<?= $karte['gekonnt'] ? 'gekonnt' : '' ?>">
                <td class="begriff"><?= escape($karte['begriff']) ?></td>
                <td><?= escape($karte['definition']) ?></td>
                <td><?= $karte['richtig'] ?> / <?= $karte['falsch'] ?></td>
                <td class="knoepfe">
                    <!-- data-*-Attribute werden von js/gemeinsam.js ausgelesen. -->
                    <button type="button" class="knopf-klein"
                            data-vorlesen="<?= escape($karte['begriff']) ?>" data-sprache="<?= escape($set['sprache_begriff']) ?>">Vorlesen</button>
                    <button type="button" class="knopf-klein stern <?= $karte['stern'] ? 'aktiv' : '' ?>" title="Karte markieren"
                            data-stern="<?= escape($karte['id']) ?>" data-set="<?= escape($id) ?>">★</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>