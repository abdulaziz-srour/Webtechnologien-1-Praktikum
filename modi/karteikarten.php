<?php /* HTML für den Modus "Karteikarten". Die Logik steckt in js/karteikarten.js. */ ?>
<div id="karteikarten" class="modus" hidden>
    <p class="zaehler"><span id="kk-position">1</span> / <span id="kk-anzahl">0</span></p>

    <!-- Die Karte hat eine Vorder- und eine Rückseite. CSS dreht sie per 3D-Animation um. -->
    <div class="karteikarte" id="kk-karte" tabindex="0" title="Klicken zum Umdrehen (Leertaste)">
        <div class="karte-innen">
            <div class="karte-seite karte-vorne"><span id="kk-vorne"></span></div>
            <div class="karte-seite karte-hinten"><span id="kk-hinten"></span></div>
        </div>
    </div>

    <div class="steuerung">
        <button type="button" class="knopf zweitrangig" id="kk-zurueck" title="Pfeil links">Zurück</button>
        <button type="button" class="knopf" id="kk-umdrehen" title="Leertaste">Umdrehen</button>
        <button type="button" class="knopf zweitrangig" id="kk-weiter" title="Pfeil rechts">Weiter</button>
    </div>
    <div class="steuerung">
        <button type="button" class="knopf-klein" id="kk-mischen">Mischen</button>
        <button type="button" class="knopf-klein" id="kk-tauschen">Seiten tauschen</button>
        <button type="button" class="knopf-klein" id="kk-vorlesen">Vorlesen</button>
        <button type="button" class="knopf-klein" id="kk-auto">Automatisch abspielen</button>
        <button type="button" class="knopf-klein stern" id="kk-stern">Markieren</button>
    </div>
    <p class="leise mitte">Tastatur: Leertaste = umdrehen, Pfeiltasten = blättern</p>
</div>
