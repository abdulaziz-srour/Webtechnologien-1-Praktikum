# CLAUDE.md – Webtechnologien 1 (WT1) Praktikum

Dieses Repository enthält das Praktikumsprojekt für das Modul **Webtechnologien 1 (WT1)**
an der Hochschule Bochum, **WiSe 2026/27** (Prof. Dr. C. Köhn, B.Sc. D. Böing).
Alle Infos unten stammen aus dem offiziellen Aufgabenblatt „Praktikum: Projektaufgabe“.
Bei jeder Arbeit an diesem Repo gelten diese Vorgaben.

## Auf einen Blick

| Punkt | Vorgabe |
|---|---|
| Aufgabe | Eine kleine Webanwendung selbst entwickeln (Thema frei wählbar) |
| Form | **Einzelprojekt** |
| Deadline | **06.01.2027** |
| Abgabe | über **Moodle** |
| Pflicht-Sprachen | **HTML, CSS, JavaScript, PHP** (alle vier müssen echt genutzt werden) |
| Laufzeitumgebung | **PHP 8.5**, aktueller Firefox und Chrome |
| Bewertung | Bestanden / nicht bestanden (Testat) |
| Arbeitsaufwand laut Modulhandbuch | ca. 15 Stunden |

## Anmeldung

- Für das Praktikum selbst ist keine Anmeldung nötig. Mit der Abgabe nimmt man automatisch teil.
- **Aber:** Das **Testat muss im Studierendenportal angemeldet werden** (ab Beginn der
  Klausuranmeldephase). Also Klausur **und** Testat getrennt anmelden.
- Keine Teilnahmevoraussetzungen.

## Pflicht-Abgabe (mindestens)

1. **PHP-Datei**, in der auch HTML enthalten ist (alternativ: eine PHP-Datei + eine getrennte HTML-Datei)
2. **CSS-Datei**
3. **JavaScript-Datei**
4. **Installationsanleitung** mit Schritt-für-Schritt-Erklärung zum Starten des Programms
   - Die Installation der gegebenen Rahmenbedingungen (PHP 8.5, Browser) wird **nicht** erklärt.
   - Zusätzliche Software (z. B. Datenbank) **muss** inkl. Einrichtung erklärt werden.
5. **Bildschirmaufnahme (Video)**, ca. **30–60 Sekunden**, **nicht KI-generiert**:
   - Projekt starten und kurz bedienen
   - während der Aufnahme eine kleine, sichtbare Änderung am Quellcode machen (z. B. einen Text ändern)
   - durch Neuladen bzw. Neustart zeigen, dass die Änderung wirkt
   - Bei Problemen mit der Aufnahme frühzeitig Frau Böing kontaktieren; alternativ ist eine Live-Vorführung möglich.

## Technische Regeln

- **HTML, CSS, JavaScript und PHP** müssen jeweils eine **erkennbare Funktion** haben.
  Reine Platzhalterdateien zählen nicht.
- Die Sprachen dürfen **nicht ersetzt** werden (kein Python statt PHP, **kein TypeScript statt
  JavaScript**). Also: kein TypeScript, kein anderes Backend.
- Zusätzliche Technologien (z. B. Datenbank, Frameworks wie Phaser) sind **erlaubt**, müssen
  aber in der Installationsanleitung eingerichtet werden.
- Zielumgebung: **PHP 8.5** + aktueller **Firefox/Chrome**. Das Projekt muss genau damit lauffähig
  sein. Bevorzugt nichts verwenden, was zusätzliche Installation braucht (z. B. SQLite oder
  JSON-Dateien statt eines separaten DB-Servers), um die Installation einfach zu halten.
- Empfehlung für den Start: PHP-Builtin-Server (`php -S localhost:8000`) im Projektordner,
  damit die Installationsanleitung kurz bleibt.

## Erlaubte Hilfsmittel

- Generative KI (Code erzeugen oder anpassen lassen), auch für größere Teile des Codes
- Alle Unterlagen (Vorlesung, Übung, YouTube, Bücher, …)
- Austausch mit und Hilfe von anderen Studierenden

**Aber:** Der Code muss selbst eingerichtet, ausgeführt, getestet und bei Bedarf angepasst
werden können. **Identische Abgaben mehrerer Studierender sind nicht zulässig.**
Deshalb: Code verständlich halten, sinnvoll kommentieren und so bauen, dass Abdulaziz ihn
selbst erklären und ändern kann.

## Bewertungskriterien (alle müssen erfüllt sein)

- **Vollständigkeit:** alle geforderten Dateien eingereicht, alle Anforderungen umgesetzt
- **Pünktlichkeit:** Deadline 06.01.2027 eingehalten
- **Lauffähigkeit:** läuft unter den vorgegebenen Rahmenbedingungen; Installationsanleitung vollständig und nachvollziehbar
- **Demonstration:** Video zeigt Start, Bedienung und eine sichtbare Code-Änderung samt Auswirkung
- **Technologieeinsatz:** HTML, CSS, JS und PHP werden tatsächlich und sinnvoll verwendet

Hinweise:
- Das Projekt muss **nicht vollständig** sein (Beispiel Online-Shop: Produktansicht + Warenkorb
  reichen, Bezahlsystem nicht nötig).
- **Bugs sind erlaubt**, solange die Anwendung grundsätzlich startet.
- Bei Nichtbestehen gibt es **eine einmalige Überarbeitungschance** (voraussichtlich ca. eine Woche,
  getrennte Mitteilung).
- Ergebnis kommt über die Moodle-Aufgabe.

## Empfohlenes Vorgehen (Phasen)

Vor jeder Phase am besten die passende Übung bearbeitet haben (auch bei KI-Nutzung).

1. **Struktur & Inhalte:** HTML-Dokument erstellen
2. **Design:** CSS ergänzen (ggf. HTML anpassen)
3. **Interaktivität:** JavaScript hinzufügen (ggf. HTML anpassen)
4. **Server-Funktionen:** PHP-Skripte erstellen (ggf. HTML oder JS, z. B. für Ajax, anpassen)
5. **Abschließende Tests**

Code immer wieder durch Ausprobieren im Browser testen.

## Beispiel-Projektideen (eigene Ideen ausdrücklich erwünscht)

- **Persönliche Homepage:** Name, Foto, Lebenslauf, aufklappbare Bereiche, Kontaktformular
  (bewusst kleiner Umfang, ohne KI in ca. 15 h machbar)
- **Interaktiver Stundenplan:** Fächer eintragen/speichern, Überschneidungen hervorheben, Drag-and-Drop
- **Workout-Tracker:** Übungen und Trainingspläne speichern, kleine Animationen bei Erfolgen
- **Plattformer-Browser-Spiel:** Jump-and-Run mit dem Phaser-Framework, Highscores speichern

## Lernziele

- Praxisnahes Verständnis von HTML, CSS, JS, PHP
- Code in diesen Sprachen lesen und schreiben
- Webprojekt in einer vorhandenen (Server-)Umgebung einrichten
- Umgang mit, Reflexion und Anpassung von KI-generiertem Code
- Typische Stolperfallen kennenlernen (Zeitplanung, Bugs, realistische Anforderungen)

## Kontakt

Daniela Böing, E-Mail: daniela.boeing@hs-bochum.de, Büro/Labor C6-04 / C6-05.
Fragen auch gerne in den Übungen, wenn wenig los ist.

## Arbeitsregeln für Claude in diesem Repo

- **Niemals etwas ins GitHub-Repository schreiben, ohne Abdulaziz vorher zu fragen.** Das gilt für
  Commits/Pushes, neue Branches, Pull Requests, Kommentare, Issues und jede andere Änderung auf GitHub.
  Änderungen erst lokal vorbereiten, zeigen und nur nach ausdrücklicher Zustimmung pushen.
- Kommunikation und Doku auf **Deutsch**.
- Nur **HTML, CSS, Vanilla-JavaScript und PHP 8.5** als Hauptsprachen. Kein TypeScript, kein Build-Schritt
  (kein npm-Bundling), damit das Projekt mit `php -S` direkt läuft.
- Jede der vier Sprachen muss eine sichtbare, echte Funktion erfüllen (z. B. PHP speichert/liest Daten,
  JS sorgt für Interaktivität, CSS für das Layout).
- Bei jeder Änderung prüfen, ob die **Installationsanleitung** (z. B. `INSTALLATION.md` oder `README.md`)
  noch stimmt, und sie mitpflegen.
- Lieber klein und lauffähig als groß und kaputt. Der Umfang soll zu ca. 15 h Aufwand passen.
- Code einfach und gut kommentiert halten, damit Abdulaziz ihn erklären, ändern und im Video live anpassen kann.
- Vor der Abgabe die Checkliste unten durchgehen.

## Abgabe-Checkliste

- [ ] Testat im Studierendenportal angemeldet (zusätzlich zur Klausur)
- [ ] PHP-Datei mit HTML (oder PHP + separate HTML-Datei)
- [ ] CSS-Datei mit echter Funktion
- [ ] JavaScript-Datei mit echter Funktion
- [ ] PHP mit echter Server-Funktion
- [ ] Installationsanleitung Schritt für Schritt (ohne PHP-/Browser-Installation, aber mit allen Extras)
- [ ] Auf frischem System mit PHP 8.5 + Firefox/Chrome nach Anleitung getestet
- [ ] Video 30–60 s: Start, Bedienung, sichtbare Code-Änderung + Neuladen, nicht KI-generiert
- [ ] Alles bis **06.01.2027** in Moodle hochgeladen
