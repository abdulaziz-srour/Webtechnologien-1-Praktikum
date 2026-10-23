<?php
/*
 * modi.php – Liste aller Lernmodi.
 * Der Schlüssel ist gleichzeitig der Dateiname: modi/<schluessel>.php und js/<schluessel>.js
 * Einen neuen Modus hinzufügen = hier eintragen + die zwei Dateien anlegen.
 */
return [
    'karteikarten' => ['name' => 'Karteikarten', 'text' => 'Umdrehen, blättern, mischen, vorlesen'],
    'lernen'       => ['name' => 'Lernen',       'text' => 'Wiederholt Karten, bis sie sitzen'],
    'schreiben'    => ['name' => 'Schreiben',    'text' => 'Antwort eintippen, Tippfehler erlaubt'],
    'test'         => ['name' => 'Test',         'text' => 'Multiple Choice, Wahr/Falsch, Freitext'],
    'zuordnen'     => ['name' => 'Zuordnen',     'text' => 'Paare per Drag-and-Drop, auf Zeit'],
];
