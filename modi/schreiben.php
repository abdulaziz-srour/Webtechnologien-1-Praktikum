<?php /* HTML für den Modus "Schreiben". Die Logik steckt in js/schreiben.js. */ ?>
<div id="schreiben" class="modus" hidden>
    <div class="steuerung">
        <label class="schalter">Antworten mit
            <select id="sc-antwort-mit">
                <option value="begriff">Begriff</option>
                <option value="definition">Definition</option>
            </select>
        </label>
    </div>

    <p class="zaehler">Runde <span id="sc-runde">1</span> · Karte <span id="sc-nr">1</span> von <span id="sc-anzahl">0</span>
        · Richtig: <span id="sc-richtig">0</span> · Falsch: <span id="sc-falsch">0</span></p>

    <div class="frage-karte" id="sc-frage">
        <p class="art">Tippe die Antwort ein – Groß-/Kleinschreibung und kleine Tippfehler sind egal</p>
        <p class="frage-text">
            <span id="sc-frage-text"></span>
            <button type="button" class="knopf-klein" id="sc-vorlesen" >Vorlesen</button>
        </p>
        <form class="eingabe-zeile" id="sc-formular" autocomplete="off">
            <input type="text" id="sc-eingabe" placeholder="Antwort …">
            <button type="submit" class="knopf">Prüfen</button>
            <button type="button" class="knopf zweitrangig" id="sc-weiss-nicht">Weiß ich nicht</button>
        </form>
        <div class="feedback" id="sc-feedback" hidden>
            <p id="sc-feedback-text"></p>
            <div class="aktionen">
                <button type="button" class="knopf" id="sc-weiter">Weiter (Enter)</button>
                <button type="button" class="knopf-klein" id="sc-doch-richtig" hidden>Ich hatte doch recht</button>
            </div>
        </div>
    </div>

    <div class="box ergebnis" id="sc-rundenende" hidden>
        <h2 id="sc-ende-titel"></h2>
        <p class="gross" id="sc-ende-prozent"></p>
        <p id="sc-ende-text"></p>
        <div class="aktionen" style="justify-content: center">
            <button type="button" class="knopf" id="sc-naechste-runde">Falsche Karten wiederholen</button>
            <button type="button" class="knopf zweitrangig" id="sc-neu-starten">Von vorn beginnen</button>
        </div>
    </div>
</div>
