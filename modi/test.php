<?php /* HTML für den Modus "Test". Die Logik steckt in js/test.js. */ ?>
<div id="test" class="modus" hidden>
    <!-- 1. Einstellungen -->
    <form class="box test-einstellungen" id="ts-einstellungen">
        <h2>Test einrichten</h2>
        <label>Anzahl Fragen (max. <span id="ts-max"></span>)
            <input type="number" id="ts-anzahl" min="1" value="10">
        </label>
        <fieldset>
            <legend>Fragetypen</legend>
            <label><input type="checkbox" name="typ" value="mc" checked> Multiple Choice</label>
            <label><input type="checkbox" name="typ" value="wf" checked> Wahr / Falsch</label>
            <label><input type="checkbox" name="typ" value="frei" checked> Freitext</label>
        </fieldset>
        <label>Antworten mit
            <select id="ts-antwort-mit">
                <option value="begriff">Begriff</option>
                <option value="definition">Definition</option>
            </select>
        </label>
        <p class="meldung fehler" id="ts-fehler" hidden></p>
        <div class="aktionen">
            <button type="submit" class="knopf">Test starten</button>
        </div>
    </form>

    <!-- 2. Ergebnis (wird nach dem Abgeben oben angezeigt) -->
    <div class="box ergebnis" id="ts-ergebnis" hidden>
        <h2>Dein Ergebnis</h2>
        <p class="gross" id="ts-prozent"></p>
        <p id="ts-punkte"></p>
        <div class="aktionen" style="justify-content: center">
            <button type="button" class="knopf" id="ts-neuer-test">Neuen Test erstellen</button>
        </div>
    </div>

    <!-- 3. Die Fragen selbst werden von JavaScript hier eingefügt -->
    <form id="ts-fragen" autocomplete="off" hidden>
        <div id="ts-liste"></div>
        <div class="aktionen" style="justify-content: center">
            <button type="submit" class="knopf" id="ts-abgeben">Test abgeben</button>
        </div>
    </form>
</div>
