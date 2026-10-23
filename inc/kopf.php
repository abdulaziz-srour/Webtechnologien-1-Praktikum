<?php
/*
 * kopf.php – gemeinsamer Seitenanfang (HTML-Kopf und Navigationsleiste).
 * Vor dem Einbinden kann $titel gesetzt werden, das steht dann im Browser-Tab.
 */
$titel = $titel ?? 'Lernkarten';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titel) ?> – Lernkarten</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        // Gespeichertes Farbschema (hell/dunkel) sofort setzen, damit die Seite nicht kurz aufblitzt.
        try {
            const thema = localStorage.getItem('thema');
            if (thema) document.documentElement.dataset.thema = thema;
        } catch (fehler) { /* z. B. privates Fenster: dann eben ohne */ }
    </script>
    <script src="js/gemeinsam.js" defer></script>
</head>
<body>
<header class="kopfleiste">
    <a class="logo" href="index.php">🗂️ Lernkarten</a>
    <nav>
        <a href="index.php">Meine Sets</a>
        <a href="bearbeiten.php">+ Neues Set</a>
        <button type="button" id="thema-knopf" class="knopf-klein" title="Hell / Dunkel umschalten">🌙</button>
    </nav>
</header>
<main class="inhalt">
