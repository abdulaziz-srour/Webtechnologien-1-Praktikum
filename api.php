<?php
/*
 * Kleine Schnittstelle für JavaScript (Ajax mit fetch()).
 * Antwortet immer mit JSON.
 *
 *   GET  api.php?aktion=set&id=...                     -> ganzes Set laden
 *   POST api.php  aktion=stern,   id, karte            -> Stern an/aus
 *   POST api.php  aktion=antwort, id, karte, richtig   -> Antwort im Lernfortschritt speichern
 *   POST api.php  aktion=bestzeit, id, zeit            -> Zeit im Zuordnen-Modus speichern
 */

require __DIR__ . '/inc/daten.php';

header('Content-Type: application/json; charset=utf-8');

/** Gibt Daten als JSON aus und beendet das Skript. */
function antworte(array $daten, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($daten, JSON_UNESCAPED_UNICODE);
    exit;
}

$eingabe = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
$aktion = $eingabe['aktion'] ?? '';
$id = $eingabe['id'] ?? '';

$set = gueltige_id($id) ? lade_set($id) : null;
if ($set === null) {
    antworte(['fehler' => 'Set nicht gefunden'], 404);
}

// Set einfach komplett zurückgeben.
if ($aktion === 'set') {
    antworte($set);
}

// Alle weiteren Aktionen ändern Daten und sind deshalb nur per POST erlaubt.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    antworte(['fehler' => 'Nur POST erlaubt'], 405);
}

if ($aktion === 'bestzeit') {
    $zeit = (float) ($eingabe['zeit'] ?? 0);
    $neuer_rekord = false;
    if ($zeit > 0 && ($set['bestzeit_zuordnen'] === null || $zeit < $set['bestzeit_zuordnen'])) {
        $set['bestzeit_zuordnen'] = round($zeit, 1);
        speichere_set($set);
        $neuer_rekord = true;
    }
    antworte(['bestzeit' => $set['bestzeit_zuordnen'], 'neuer_rekord' => $neuer_rekord]);
}

// Ab hier geht es immer um eine einzelne Karte. Position der Karte im Array suchen.
$karten_id = $eingabe['karte'] ?? '';
$position = null;
foreach ($set['karten'] as $i => $karte) {
    if ($karte['id'] === $karten_id) {
        $position = $i;
        break;
    }
}
if ($position === null) {
    antworte(['fehler' => 'Karte nicht gefunden'], 404);
}

if ($aktion === 'stern') {
    $set['karten'][$position]['stern'] = !$set['karten'][$position]['stern'];
    speichere_set($set);
    antworte(['stern' => $set['karten'][$position]['stern']]);
}

if ($aktion === 'antwort') {
    $richtig = ($eingabe['richtig'] ?? '') === '1';
    if ($richtig) {
        $set['karten'][$position]['richtig']++;
    } else {
        $set['karten'][$position]['falsch']++;
    }
    $set['karten'][$position]['gekonnt'] = $richtig;
    speichere_set($set);
    antworte(['ok' => true, 'statistik' => statistik($set)]);
}

antworte(['fehler' => 'Unbekannte Aktion'], 400);