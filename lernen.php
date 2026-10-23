<?php
/*
 * lernen.php?id=...&modus=...&sterne=1 – Rahmen für alle Lernmodi.
 *
 * PHP prüft hier nur, ob Set und Modus gültig sind, und bindet dann
 *   - das HTML des Modus  (modi/<modus>.php) und
 *   - das JavaScript des Modus (js/<modus>.js) ein.
 * Die Karten selbst lädt das JavaScript per fetch() über api.php.
 */
require __DIR__ . '/inc/daten.php';

$id = $_GET['id'] ?? '';
$set = gueltige_id($id) ? lade_set($id) : null;
if ($set === null) {
    http_response_code(404);
    exit('Set nicht gefunden. <a href="index.php">Zur Startseite</a>');
}

$modi = require __DIR__ . '/inc/modi.php';
$modus = $_GET['modus'] ?? 'karteikarten';
if (!isset($modi[$modus])) {
    $modus = 'karteikarten'; // unbekannter Modus -> Standard
}
$nur_sterne = ($_GET['sterne'] ?? '') === '1';

$titel = $modi[$modus]['name'] . ' · ' . $set['name'];
require __DIR__ . '/inc/kopf.php';
?>

<section class="kopfbereich">
    <div>
        <p><a href="set.php?id=<?= e($id) ?>">← <?= e($set['name']) ?></a></p>
        <h1><?= $modi[$modus]['symbol'] ?> <?= $modi[$modus]['name'] ?></h1>
    </div>
    <nav class="modus-wechsel">
        <?php foreach ($modi as $schluessel => $m): ?>
            <a href="lernen.php?id=<?= e($id) ?>&amp;modus=<?= $schluessel ?><?= $nur_sterne ? '&amp;sterne=1' : '' ?>"
               class="<?= $schluessel === $modus ? 'aktiv' : '' ?>" title="<?= $m['name'] ?>"><?= $m['symbol'] ?></a>
        <?php endforeach; ?>
    </nav>
</section>

<!-- Diese Daten liest js/gemeinsam.js aus, um das richtige Set zu laden. -->
<div id="lernbereich" data-set="<?= e($id) ?>" data-nur-sterne="<?= $nur_sterne ? '1' : '0' ?>">
    <p class="leise" id="lade-hinweis">Karten werden geladen …</p>
    <?php require __DIR__ . '/modi/' . $modus . '.php'; ?>
</div>

<script src="js/<?= $modus ?>.js" defer></script>
<?php require __DIR__ . '/inc/fuss.php'; ?>
