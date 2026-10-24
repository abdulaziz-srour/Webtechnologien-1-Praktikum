<?php /* HTML für den Modus "Zuordnen". Die Logik steckt in js/zuordnen.js. */ ?>
<div id="zuordnen" class="modus" hidden>
    <div class="box ergebnis" id="zo-start">
        <h2>Ordne alle Begriffe ihren Definitionen zu – so schnell du kannst!</h2>
        <p>Ziehe eine Kachel auf die passende andere Kachel (Drag-and-Drop) oder klicke nacheinander auf beide.
            Jeder Fehler kostet 1 Sekunde Strafzeit.</p>
        <p class="leise" id="zo-bestzeit-start"></p>
        <button type="button" class="knopf" id="zo-starten">Spiel starten</button>
    </div>

    <div id="zo-spiel" hidden>
        <div class="zuordnen-kopf">
            <span>Noch <span id="zo-rest">0</span> Paare</span>
            <span class="timer"><span id="zo-zeit">0,0</span> s</span>
        </div>
        <div class="zuordnen-raster" id="zo-raster"></div>
    </div>

    <div class="box ergebnis" id="zo-ende" hidden>
        <h2>Fertig!</h2>
        <p class="gross"><span id="zo-endzeit"></span> s</p>
        <p id="zo-rekord"></p>
        <button type="button" class="knopf" id="zo-nochmal">Nochmal spielen</button>
    </div>
</div>
