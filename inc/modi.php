<?php
/*
 * modi.php – Liste aller Lernmodi.
 * Der Schlüssel ist gleichzeitig der Dateiname: modi/<schluessel>.php und js/<schluessel>.js
 * Einen neuen Modus hinzufügen = hier eintragen + die zwei Dateien anlegen.
 */
return [
    'karteikarten' => ['symbol' => '🃏', 'name' => 'Karteikarten', 'text' => 'Umdrehen, blättern, mischen, vorlesen'],
    'lernen'       => ['symbol' => '🎯', 'name' => 'Lernen',       'text' => 'Wiederholt Karten, bis sie sitzen'],
    'schreiben'    => ['symbol' => '✍️', 'name' => 'Schreiben',    'text' => 'Antwort eintippen, Tippfehler erlaubt'],
    'test'         => ['symbol' => '📝', 'name' => 'Test',         'text' => 'Multiple Choice, Wahr/Falsch, Freitext'],
];
