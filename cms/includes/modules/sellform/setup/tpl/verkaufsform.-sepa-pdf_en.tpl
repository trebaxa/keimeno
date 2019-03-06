<div class="sepa-container">
    <h1>SEPA - Lastschriftmandat</h1>
    <label>Name des ZahlungsempfÃ¤ngers:</label>
    <input value="" disabled>
    <label>Anschrift des ZahlungsempfÃ¤ngers:</label>
    <input value="<%$customer.strasse%> <%$customer.hausnr%>" disabled>
    <label>Postleitzahl und Ort:</label>
    <input value="<%$customer.plz%> <%$customer.ort%>" disabled> | <label>Postleitzahl und Ort:</label> <input value="<%$customer.country%>" disabled>
    <label>Mandatsreferenz (vom ZahlungsempfÃ¤nger auszufÃ¼llen):</label>
    <input value="<%$customer.manref%>" disabled>
    <p>Ich ermÃ¤chtige / Wir ermÃ¤chtigen (A) den ZahlungsempfÃ¤nger FitMitReha GmbH, Zahlungen von meinem / unserem Konto mittels Lastschrift einzuziehen. Zugleich (B) weise ich mein / weisen wir unser Kreditinstitut an,
        die vom ZahlungsempfÃ¤nger FitMItReha GmbH auf mein / unser Konto gezogenen Lastschriften einzulÃ¶sen.
    </p>
    <p>Heinweis: Ich kann / Wir kÃ¶nnen innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem / userem Kreditinstitut vereinbarten Bedingungen.</p>
    <label>Zahlungsart:</label>
    <input type="checkbox" value="" checked disabled>Wiederkehrende Zahlung
    <label>Name des Zahlungspflichtigen:</label>
    <input class="form-control" type="text" value="<%$customer.vorname%> <%$customer.nachname%>" disabled>
    <label>Anschrift des Zahlungspflichtigen:</label>
    <input class="form-control" type="text" value="<%$customer.strasse%> <%$customer.hausnr%>" disabled>
    <label>Postleitzahl und Ort:</label>
    <input value="<%$customer.plz%> <%$customer.ort%>" disabled> | <label>Postleitzahl und Ort:</label> <input value="<%$customer.country%>" disabled>
    <label>IBAN des Zahlungspflichtigen (max. 35 Stellen):</label>
    <input class="form-control" type="text" value="<%$customer.iban%>" disabled>
    <label>BIC (8 oder 11 Stellen):</label>
    <input class="form-control" type="text" value="<%$customer.bic%>" disabled>
    <label>Ort, Datum</label>
    <input class="form-control" type="text" value="" disabled>
    <label>Unterschrift(en) des Zahlungspflichtigen (Kontoinhaber)</label>
    <input class="form-control" type="text" value="" disabled>

    
</div>