<?php
/*
 * index.php – Startseite: zeigt alle Lernsets, nach Fach (Ordner) gruppiert,
 * und bietet eine Suche über alle Karten.
 */
require __DIR__ . '/inc/daten.php';

$sets = lade_alle_sets();
$suche = trim($_GET['suche'] ?? '');

// Sets nach Fach gruppieren. Sets ohne Fach landen unter "Ohne Fach".
$nach_fach = [];
foreach ($sets as $set) {
    $fach = $set['fach'] !== '' ? $set['fach'] : 'Ohne Fach';
    $nach_fach[$fach][] = $set;
}
ksort($nach_fach, SORT_NATURAL | SORT_FLAG_CASE);

// Suche: alle Karten durchgehen, bei denen Begriff oder Definition passt.
$treffer = [];
if ($suche !== '') {
    foreach ($sets as $set) {
        foreach ($set['karten'] as $karte) {
            if (enthaelt($karte['begriff'], $suche) || enthaelt($karte['definition'], $suche)) {
                $treffer[] = ['set' => $set, 'karte' => $karte];
            }
        }
    }
}

$titel = 'Meine Sets';
require __DIR__ . '/inc/kopf.php';
?>

<section class="kopfbereich">
    <h1>Meine Lernsets</h1>
    <form class="suche" method="get" action="index.php">
        <input type="search" name="suche" value="<?= e($suche) ?>" placeholder="Alle Karten durchsuchen …" aria-label="Suche">
        <button type="submit" class="knopf">Suchen</button>
    </form>
</section>

<?php if ($suche !== ''): ?>
    <section class="box">
        <h2><?= count($treffer) ?> Treffer für „<?= e($suche) ?>“</h2>
        <?php if (count($treffer) === 0): ?>
            <p class="leise">Keine Karte gefunden.</p>
        <?php else: ?>
            <ul class="kartenliste">
                <?php foreach ($treffer as $t): ?>
                    <li>
                        <span class="begriff"><?= e($t['karte']['begriff']) ?></span>
                        <span class="definition"><?= e($t['karte']['definition']) ?></span>
                        <a class="leise" href="set.php?id=<?= e($t['set']['id']) ?>"><?= e($t['set']['name']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <p><a href="index.php">Suche zurücksetzen</a></p>
    </section>
<?php endif; ?>

<?php if (count($sets) === 0): ?>
    <section class="box leer">
        <p>Noch keine Lernsets vorhanden.</p>
        <a class="knopf" href="bearbeiten.php">Erstes Set anlegen</a>
    </section>
<?php endif; ?>

<?php foreach ($nach_fach as $fach => $sets_im_fach): ?>
    <section class="ordner">
        <h2>📁 <?= e($fach) ?></h2>
        <div class="set-raster">
            <?php foreach ($sets_im_fach as $set): ?>
                <?php $stat = statistik($set); ?>
                <a class="set-kachel" href="set.php?id=<?= e($set['id']) ?>">
                    <strong><?= e($set['name']) ?></strong>
                    <span class="leise"><?= $stat['gesamt'] ?> Karten · <?= $stat['prozent'] ?> % gekonnt</span>
                    <span class="fortschritt"><span style="width: <?= $stat['prozent'] ?>%"></span></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>

<?php require __DIR__ . '/inc/fuss.php'; ?>
