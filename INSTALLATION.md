# Installationsanleitung – Lernkarten-App

Diese Anleitung erklärt Schritt für Schritt, wie die Lernkarten-App gestartet wird.
Vorausgesetzt werden (laut Aufgabenstellung, daher hier nicht erklärt):

- **PHP 8.5** (Kommandozeile, Befehl `php`)
- ein aktueller **Firefox** oder **Chrome**

**Sonst wird nichts gebraucht:** keine Datenbank, kein Webserver wie Apache, kein npm,
kein Build-Schritt und keine Internetverbindung. Die Daten speichert PHP in einer JSON-Datei.

## Schritt 1: Projektordner bereitlegen

Den Projektordner (z. B. aus dem Moodle-ZIP entpackt oder per `git clone`) an einen
beliebigen Ort legen, z. B. `C:\Projekte\lernkarten` oder `~/lernkarten`.

Im Ordner müssen u. a. diese Dateien liegen: `index.php`, `api.php`, `css/style.css`,
`js/gemeinsam.js` und der (anfangs leere) Ordner `data/`.

## Schritt 2: Terminal im Projektordner öffnen

- **Windows:** Im Explorer in den Projektordner gehen, oben in die Adresszeile `cmd` tippen
  und Enter drücken.
- **macOS / Linux:** Terminal öffnen und mit `cd` in den Ordner wechseln, z. B. `cd ~/lernkarten`.

Prüfen, ob PHP gefunden wird:

```
php -v
```

Es sollte `PHP 8.5...` angezeigt werden.

## Schritt 3: PHP-Server starten

Im Projektordner diesen Befehl ausführen:

```
php -S localhost:8000
```

Die Ausgabe sieht etwa so aus: `PHP 8.5.x Development Server (http://localhost:8000) started`.
Das Terminal-Fenster muss geöffnet bleiben, solange die App benutzt wird.

> Ist Port 8000 schon belegt, einfach einen anderen nehmen, z. B. `php -S localhost:8080`,
> und im Browser entsprechend `http://localhost:8080` aufrufen.

## Schritt 4: App im Browser öffnen

In Firefox oder Chrome diese Adresse öffnen:

```
http://localhost:8000
```

Beim ersten Aufruf legt PHP automatisch die Datei `data/sets.json` mit zwei Beispiel-Sets an
(„WT1 Grundbegriffe“ und „Englisch Vokabeln“). Fertig – die App kann benutzt werden.

## Schritt 5: Beenden

Im Terminal `Strg + C` drücken. Die gespeicherten Sets bleiben in `data/sets.json` erhalten
und sind beim nächsten Start wieder da.

## Häufige Probleme

| Problem | Lösung |
|---|---|
| `php` wird nicht gefunden | PHP 8.5 ist nicht im `PATH`. PHP-Installation prüfen bzw. den vollen Pfad zu `php` verwenden. |
| Fehler beim Speichern / „Permission denied“ | PHP braucht Schreibrechte im Ordner `data/`. Unter Linux/macOS z. B. `chmod 777 data`. |
| Seite zeigt den PHP-Code statt der App | Die Seite wurde direkt als Datei geöffnet. Immer über `http://localhost:8000` aufrufen (Schritt 3 und 4). |
| Vorlesen ist stumm | Die Sprachausgabe nutzt die Stimmen des Betriebssystems. Lautstärke prüfen; unter Linux muss ggf. eine Sprachausgabe (z. B. `speech-dispatcher`) installiert sein. Für eine Sprache ohne installierte Stimme nimmt der Browser die Standardstimme. |
| Alles auf Anfang setzen | Server beenden, `data/sets.json` löschen, Server neu starten. Die Beispiel-Sets werden neu angelegt. |
