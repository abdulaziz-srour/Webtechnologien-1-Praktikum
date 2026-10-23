# Lernkarten-App (WT1-Praktikum)

Eine eigene kleine Quizlet-Alternative für das Praktikum **Webtechnologien 1** an der HS Bochum (WiSe 2026/27).
Lernsets mit Karten (Begriff und Definition) anlegen und in verschiedenen Modi lernen.

**Starten:** im Projektordner `php -S localhost:8000` ausführen und `http://localhost:8000` öffnen.
Ausführlich: siehe [INSTALLATION.md](INSTALLATION.md).

## Funktionen

- Lernsets anlegen, bearbeiten, löschen; Karten mit Begriff und Definition
- Import per Copy-Paste (eine Karte pro Zeile, z. B. `Begriff ; Definition`)
- **Karteikarten:** umdrehen mit Animation, blättern, mischen, Seiten tauschen, vorlesen, automatisch abspielen
- **Lernen:** erst Multiple Choice, dann Schreiben; falsche Karten kommen wieder, bis alle sitzen
- **Schreiben:** Antwort eintippen, tolerant bei Groß-/Kleinschreibung und kleinen Tippfehlern; Runden mit den falschen Karten

## Aufbau

| Datei / Ordner | Sprache | Aufgabe |
|---|---|---|
| `index.php` | PHP + HTML | Startseite: alle Sets nach Fach, Suche über alle Karten |
| `set.php` | PHP + HTML | Set-Ansicht: Statistik, Modus-Auswahl, Kartenliste |
| `bearbeiten.php` | PHP + HTML | Set anlegen/bearbeiten, Import |
| `lernen.php` | PHP + HTML | Rahmen für alle Lernmodi |
| `modi/*.php` | HTML | HTML-Teil der einzelnen Lernmodi |
| `api.php` | PHP | JSON-Schnittstelle für JavaScript (Ajax): Set laden, Stern, Lernfortschritt |
| `inc/daten.php` | PHP | Laden/Speichern der Sets in `data/sets.json` |
| `inc/kopf.php`, `inc/fuss.php` | PHP + HTML | gemeinsamer Seitenkopf und -fuß |
| `inc/modi.php` | PHP | Liste der Lernmodi |
| `css/style.css` | CSS | gesamtes Layout, Dark Mode, Umdreh-Animation |
| `js/gemeinsam.js` | JavaScript | Dark Mode, Vorlesen, Ajax, Mischen, tolerante Antwortprüfung |
| `js/bearbeiten.js` | JavaScript | Zeilen im Editor hinzufügen/entfernen, Import-Vorschau |
| `js/<modus>.js` | JavaScript | Logik des jeweiligen Lernmodus |
| `data/sets.json` | JSON | gespeicherte Daten (wird automatisch angelegt) |
