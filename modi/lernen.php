<?php /* HTML für den Modus "Lernen". Die Logik steckt in js/lernen.js. */ ?>
<div id="lernen" class="modus" hidden>
    <div class="steuerung">
        <label class="schalter">Antworten mit
            <select id="ln-antwort-mit">
                <option value="begriff">Begriff</option>
                <option value="definition">Definition</option>
            </select>
        </label>
    </div>

    <span class="fortschritt"><span id="ln-balken" style="width: 0%"></span></span>
    <p class="zaehler">
        Neu: <span id="ln-neu">0</span> ·
        Am Lernen: <span id="ln-lernend">0</span> ·
        Gelernt: <span id="ln-gelernt">0</span>
    </p>

    <!-- Frage: entweder Multiple Choice (Knöpfe) oder Schreiben (Eingabefeld) -->
    <div class="frage-karte" id="ln-frage">
        <p class="art" id="ln-art"></p>
        <p class="frage-text">
            <span id="ln-frage-text"></span>
            <button type="button" class="knopf-klein" id="ln-vorlesen" >Vorlesen</button>
        </p>
        <div class="optionen" id="ln-optionen"></div>
        <form class="eingabe-zeile" id="ln-formular" autocomplete="off">
            <input type="text" id="ln-eingabe" placeholder="Antwort eintippen …">
            <button type="submit" class="knopf">Prüfen</button>
        </form>
        <div class="feedback" id="ln-feedback" hidden>
            <p id="ln-feedback-text"></p>
            <div class="aktionen">
                <button type="button" class="knopf" id="ln-weiter">Weiter (Enter)</button>
                <button type="button" class="knopf-klein" id="ln-doch-richtig" hidden>Ich hatte doch recht</button>
            </div>
        </div>
    </div>

    <div class="box ergebnis" id="ln-ende" hidden>
        <h2>Geschafft!</h2>
        <p>Du hast alle <span id="ln-ende-anzahl"></span> Karten gelernt.</p>
        <div class="aktionen" style="justify-content: center">
            <button type="button" class="knopf" id="ln-nochmal">Nochmal lernen</button>
        </div>
    </div>
    <p class="leise mitte">Jede Karte musst du erst im Multiple Choice und dann schriftlich richtig beantworten.
        Falsche Karten kommen bald wieder dran.</p>
</div>
