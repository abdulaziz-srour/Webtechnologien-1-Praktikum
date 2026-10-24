<?php
/*
 * Set anlegen oder bearbeiten.
 *
 *   bearbeiten.php          -> neues Set anlegen
 *   bearbeiten.php?id=...   -> vorhandenes Set bearbeiten
 *
 * Das Formular schickt alles per POST an diese Seite zurück. PHP speichert dann und leitet auf die Set-Ansicht weiter.
 */
require __DIR__ . '/inc/daten.php';

$id = $_GET['id'] ?? '';
$set = null;
if ($id !== '') {
    $set = gueltige_id($id) ? lade_set($id) : null;
    if ($set === null) {
        http_response_code(404);
        exit('Set nicht gefunden. <a href="index.php">Zur Startseite</a>');
    }
}
$ist_neu = ($set === null);
if ($ist_neu) {
    $set = neues_set();
}

$fehler = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $set['name']         = trim($_POST['name'] ?? '');
    $set['fach']         = trim($_POST['fach'] ?? '');
    $set['beschreibung'] = trim($_POST['beschreibung'] ?? '');

    // Nur Sprachen aus der Liste erlauben.
    $sb = $_POST['sprache_begriff'] ?? 'de-DE';
    $sd = $_POST['sprache_definition'] ?? 'de-DE';
    $set['sprache_begriff']    = isset(SPRACHEN[$sb]) ? $sb : 'de-DE';
    $set['sprache_definition'] = isset(SPRACHEN[$sd]) ? $sd : 'de-DE';

    // Alte Karten nach ID merken, damit Stern und Lernfortschritt erhalten bleiben.
    $alte_karten = [];
    foreach ($set['karten'] as $karte) {
        $alte_karten[$karte['id']] = $karte;
    }

    // Karten aus den Formularzeilen übernehmen. Leere Zeilen werden ignoriert.
    $neue_karten = [];
    foreach ($_POST['karten'] ?? [] as $zeile) {
        $begriff = trim($zeile['begriff'] ?? '');
        $definition = trim($zeile['definition'] ?? '');
        if ($begriff === '' || $definition === '') {
            continue;
        }
        $karten_id = $zeile['id'] ?? '';
        if (isset($alte_karten[$karten_id])) {
            $karte = $alte_karten[$karten_id];
            $karte['begriff'] = $begriff;
            $karte['definition'] = $definition;
        } else {
            $karte = neue_karte($begriff, $definition);
        }
        $neue_karten[] = $karte;
    }

    // Import per Copy-Paste anhängen.
    $import = $_POST['import'] ?? '';
    if (trim($import) !== '') {
        $trenner = $_POST['trenner'] ?? ';';
        if (!in_array($trenner, [';', 'tab', ',', '-', ':'], true)) {
            $trenner = ';';
        }
        $neue_karten = array_merge($neue_karten, importiere_text($import, $trenner));
    }
    $set['karten'] = $neue_karten;

    if ($set['name'] === '') {
        $fehler = 'Bitte gib dem Set einen Namen.';
    } elseif (count($set['karten']) === 0) {
        $fehler = 'Das Set braucht mindestens eine Karte (Begriff und Definition).';
    } else {
        speichere_set($set);
        header('Location: set.php?id=' . $set['id']);
        exit;
    }
}

// Für ein neues Set ein paar leere Zeilen anzeigen.
$zeilen = $set['karten'];
if (count($zeilen) === 0) {
    for ($i = 0; $i < 3; $i++) {
        $zeilen[] = ['id' => '', 'begriff' => '', 'definition' => ''];
    }
}

// Alle vorhandenen Fächer für die Vorschlagsliste sammeln.
$faecher = [];
foreach (lade_alle_sets() as $s) {
    if ($s['fach'] !== '') {
        $faecher[$s['fach']] = true;
    }
}

$titel = $ist_neu ? 'Neues Set' : 'Set bearbeiten';
require __DIR__ . '/inc/kopf.php';
?>

<h1><?= $ist_neu ? 'Neues Lernset anlegen' : 'Set bearbeiten' ?></h1>

<?php if ($fehler !== ''): ?>
    <p class="meldung fehler"><?= escape($fehler) ?></p>
<?php endif; ?>

<form method="post" class="editor" id="editor">
    <section class="box raster-2">
        <label>Name des Sets
            <input type="text" name="name" value="<?= escape($set['name']) ?>" required placeholder="z. B. Englisch Unit 3">
        </label>
        <label>Fach / Ordner
            <input type="text" name="fach" value="<?= escape($set['fach']) ?>" list="faecher" placeholder="z. B. Englisch">
            <datalist id="faecher">
                <?php foreach (array_keys($faecher) as $f): ?>
                    <option value="<?= escape($f) ?>">
                <?php endforeach; ?>
            </datalist>
        </label>
        <label class="breit">Beschreibung (optional)
            <input type="text" name="beschreibung" value="<?= escape($set['beschreibung']) ?>">
        </label>
        <label>Sprache der Begriffe (Vorlesen)
            <select name="sprache_begriff">
                <?php foreach (SPRACHEN as $code => $name): ?>
                    <option value="<?= $code ?>" <?= $code === $set['sprache_begriff'] ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Sprache der Definitionen (Vorlesen)
            <select name="sprache_definition">
                <?php foreach (SPRACHEN as $code => $name): ?>
                    <option value="<?= $code ?>" <?= $code === $set['sprache_definition'] ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </section>

    <h2>Karten</h2>
    <!-- data-naechster sagt dem JavaScript, mit welcher Nummer neue Zeilen weitergezählt werden. -->
    <div id="karten-zeilen" data-naechster="<?= count($zeilen) ?>">
        <?php foreach ($zeilen as $i => $karte): ?>
            <div class="karten-zeile">
                <span class="nummer"><?= $i + 1 ?></span>
                <input type="hidden" name="karten[<?= $i ?>][id]" value="<?= escape($karte['id']) ?>">
                <textarea name="karten[<?= $i ?>][begriff]" rows="2" placeholder="Begriff"><?= escape($karte['begriff']) ?></textarea>
                <textarea name="karten[<?= $i ?>][definition]" rows="2" placeholder="Definition"><?= escape($karte['definition']) ?></textarea>
                <button type="button" class="knopf-klein zeile-loeschen" title="Karte entfernen">Entfernen</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="knopf zweitrangig" id="zeile-hinzufuegen">+ Karte hinzufügen</button>

    <details class="box import">
        <summary>Karten importieren (Copy-Paste)</summary>
        <p class="leise">Eine Karte pro Zeile, Begriff und Definition durch ein Trennzeichen getrennt,
            z. B. <code>HTML ; Auszeichnungssprache</code>. Die Karten werden unten angehängt.</p>
        <label>Trennzeichen
            <select name="trenner" id="trenner">
                <option value=";">Semikolon ( ; )</option>
                <option value="tab">Tabulator (aus Excel / Quizlet)</option>
                <option value=",">Komma ( , )</option>
                <option value="-">Bindestrich ( - )</option>
                <option value=":">Doppelpunkt ( : )</option>
            </select>
        </label>
        <!-- &#10 = Zeilenumbruch -->
        <textarea name="import" id="import" rows="6" placeholder="Begriff ; Definition&#10;Begriff ; Definition"></textarea>
        <p class="leise" id="import-vorschau"></p>
    </details>

    <div class="aktionen">
        <button type="submit" class="knopf">Speichern</button>
        <a class="knopf zweitrangig" href="<?= $ist_neu ? 'index.php' : 'set.php?id=' . escape($set['id']) ?>">Abbrechen</a>
    </div>
</form>

<!-- Vorlage für neue Zeilen. JavaScript kopiert sie und ersetzt __NR__ durch die laufende Nummer. -->
<template id="zeilen-vorlage">
    <div class="karten-zeile">
        <span class="nummer"></span>
        <input type="hidden" name="karten[__NR__][id]" value="">
        <textarea name="karten[__NR__][begriff]" rows="2" placeholder="Begriff"></textarea>
        <textarea name="karten[__NR__][definition]" rows="2" placeholder="Definition"></textarea>
        <button type="button" class="knopf-klein zeile-loeschen" title="Karte entfernen">Entfernen</button>
    </div>
</template>

<script src="js/bearbeiten.js" defer></script>