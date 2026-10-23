<?php
/*
 * daten.php – Alles rund ums Laden und Speichern der Lernsets.
 *
 * Alle Sets liegen zusammen in EINER JSON-Datei: data/sets.json
 * Aufbau der Datei (vereinfacht):
 *
 * {
 *   "a1b2c3d4": {
 *     "id": "a1b2c3d4",
 *     "name": "Englisch Vokabeln",
 *     "fach": "Englisch",              // Ordner / Fach, nach dem gruppiert wird
 *     "beschreibung": "...",
 *     "sprache_begriff": "en-US",      // Sprache für das Vorlesen
 *     "sprache_definition": "de-DE",
 *     "bestzeit_zuordnen": 12.3,       // beste Zeit im Zuordnen-Modus (oder null)
 *     "geaendert": "2026-10-23 15:00",
 *     "karten": [
 *       { "id": "...", "begriff": "dog", "definition": "Hund",
 *         "stern": false, "richtig": 0, "falsch": 0, "gekonnt": false }
 *     ]
 *   }
 * }
 *
 * Es wird keine Datenbank gebraucht. Existiert die Datei noch nicht,
 * wird sie beim ersten Aufruf mit zwei Beispiel-Sets angelegt.
 */

const DATEN_ORDNER = __DIR__ . '/../data';
const DATEN_DATEI  = DATEN_ORDNER . '/sets.json';

// Sprachen, die beim Vorlesen (Web Speech API) zur Auswahl stehen.
const SPRACHEN = [
    'de-DE' => 'Deutsch',
    'en-US' => 'Englisch',
    'fr-FR' => 'Französisch',
    'es-ES' => 'Spanisch',
    'it-IT' => 'Italienisch',
    'tr-TR' => 'Türkisch',
    'ar-SA' => 'Arabisch',
];

/** Liest alle Sets aus der JSON-Datei und gibt sie als Array zurück (Schlüssel = Set-ID). */
function lade_alle_sets(): array
{
    if (!file_exists(DATEN_DATEI)) {
        speichere_alle_sets(beispiel_sets());
    }
    $inhalt = file_get_contents(DATEN_DATEI);
    $sets = json_decode($inhalt, true);
    return is_array($sets) ? $sets : [];
}

/** Schreibt alle Sets zurück in die JSON-Datei (schön formatiert, damit man sie auch lesen kann). */
function speichere_alle_sets(array $sets): void
{
    if (!is_dir(DATEN_ORDNER)) {
        mkdir(DATEN_ORDNER, 0777, true);
    }
    $json = json_encode($sets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // LOCK_EX verhindert, dass zwei Anfragen gleichzeitig in die Datei schreiben.
    file_put_contents(DATEN_DATEI, $json, LOCK_EX);
}

/** Gibt ein einzelnes Set zurück oder null, wenn es die ID nicht gibt. */
function lade_set(string $id): ?array
{
    $sets = lade_alle_sets();
    return $sets[$id] ?? null;
}

/** Speichert (oder ersetzt) ein einzelnes Set. */
function speichere_set(array $set): void
{
    $sets = lade_alle_sets();
    $set['geaendert'] = date('Y-m-d H:i');
    $sets[$set['id']] = $set;
    speichere_alle_sets($sets);
}

/** Löscht ein Set komplett. */
function loesche_set(string $id): void
{
    $sets = lade_alle_sets();
    unset($sets[$id]);
    speichere_alle_sets($sets);
}

/** Erzeugt eine zufällige, kurze ID wie "3f9a1c0b". */
function neue_id(): string
{
    return bin2hex(random_bytes(4));
}

/** Prüft, ob eine ID gültig aussieht (nur Kleinbuchstaben a–f und Ziffern). */
function gueltige_id(string $id): bool
{
    return preg_match('/^[a-f0-9]{8}$/', $id) === 1;
}

/** Legt eine neue Karte mit leerem Lernfortschritt an. */
function neue_karte(string $begriff, string $definition): array
{
    return [
        'id'         => neue_id(),
        'begriff'    => $begriff,
        'definition' => $definition,
        'stern'      => false,
        'richtig'    => 0,
        'falsch'     => 0,
        'gekonnt'    => false,
    ];
}

/** Legt ein neues, leeres Set an (noch ohne es zu speichern). */
function neues_set(): array
{
    return [
        'id'                 => neue_id(),
        'name'               => '',
        'fach'               => '',
        'beschreibung'       => '',
        'sprache_begriff'    => 'de-DE',
        'sprache_definition' => 'de-DE',
        'bestzeit_zuordnen'  => null,
        'geaendert'          => date('Y-m-d H:i'),
        'karten'             => [],
    ];
}

/**
 * Wandelt eingefügten Text in Karten um.
 * Jede Zeile ist eine Karte, Begriff und Definition werden durch $trenner getrennt,
 * z. B. "HTML ; Auszeichnungssprache für Webseiten".
 */
function importiere_text(string $text, string $trenner): array
{
    if ($trenner === 'tab') {
        $trenner = "\t";
    }
    $karten = [];
    $zeilen = preg_split('/\r\n|\r|\n/', $text);
    foreach ($zeilen as $zeile) {
        // Nur am ERSTEN Trennzeichen teilen, damit die Definition selbst eins enthalten darf.
        $teile = explode($trenner, $zeile, 2);
        if (count($teile) < 2) {
            continue; // Zeile ohne Trennzeichen (z. B. Leerzeile) überspringen
        }
        $begriff = trim($teile[0]);
        $definition = trim($teile[1]);
        if ($begriff !== '' && $definition !== '') {
            $karten[] = neue_karte($begriff, $definition);
        }
    }
    return $karten;
}

/** Berechnet die Statistik eines Sets: Anzahl Karten, davon gekonnt, markiert, Prozent. */
function statistik(array $set): array
{
    $gesamt = count($set['karten']);
    $gekonnt = 0;
    $sterne = 0;
    foreach ($set['karten'] as $karte) {
        if ($karte['gekonnt']) {
            $gekonnt++;
        }
        if ($karte['stern']) {
            $sterne++;
        }
    }
    return [
        'gesamt'  => $gesamt,
        'gekonnt' => $gekonnt,
        'sterne'  => $sterne,
        'prozent' => $gesamt > 0 ? (int) round($gekonnt / $gesamt * 100) : 0,
    ];
}

/** Sucht (ohne Beachtung der Groß-/Kleinschreibung), ob $suche in $text vorkommt. */
function enthaelt(string $text, string $suche): bool
{
    if (function_exists('mb_stripos')) {
        return mb_stripos($text, $suche) !== false;
    }
    return stripos($text, $suche) !== false;
}

/** Kurzform für htmlspecialchars – schützt vor kaputtem HTML und eingeschleustem Code. */
function e(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/** Beispiel-Sets, damit die App beim ersten Start nicht leer ist. */
function beispiel_sets(): array
{
    $wt1 = neues_set();
    $wt1['name'] = 'WT1 Grundbegriffe';
    $wt1['fach'] = 'Webtechnologien';
    $wt1['beschreibung'] = 'Wichtige Begriffe aus der Vorlesung Webtechnologien 1.';
    $wt1['karten'] = importiere_text(
        "HTML ; Auszeichnungssprache, die Struktur und Inhalt einer Webseite beschreibt\n" .
        "CSS ; Sprache für das Aussehen (Layout, Farben, Schriften) einer Webseite\n" .
        "JavaScript ; Programmiersprache, die im Browser läuft und Webseiten interaktiv macht\n" .
        "PHP ; Skriptsprache, die auf dem Server läuft und z. B. HTML erzeugt oder Daten speichert\n" .
        "DOM ; Baumstruktur der Webseite, über die JavaScript Elemente lesen und ändern kann\n" .
        "HTTP ; Protokoll, mit dem Browser und Webserver Anfragen und Antworten austauschen\n" .
        "JSON ; Textformat zum Speichern und Austauschen von Daten (Objekte und Arrays)\n" .
        "Ajax ; Daten im Hintergrund vom Server laden, ohne die Seite neu zu laden\n" .
        "Selektor ; Teil einer CSS-Regel, der festlegt, welche Elemente gestaltet werden\n" .
        "Formular ; HTML-Element, mit dem Nutzer Daten eingeben und an den Server senden",
        ';'
    );

    $englisch = neues_set();
    $englisch['name'] = 'Englisch Vokabeln';
    $englisch['fach'] = 'Englisch';
    $englisch['beschreibung'] = 'Ein paar Vokabeln zum Ausprobieren der Vorlesen-Funktion.';
    $englisch['sprache_begriff'] = 'en-US';
    $englisch['sprache_definition'] = 'de-DE';
    $englisch['karten'] = importiere_text(
        "dog ; Hund\ncat ; Katze\nhouse ; Haus\ntree ; Baum\nbook ; Buch\n" .
        "computer ; Computer\nwindow ; Fenster\nfriend ; Freund",
        ';'
    );

    return [$wt1['id'] => $wt1, $englisch['id'] => $englisch];
}
