<html>
<head>
<style>
h3, input {}
h3 {
    display:block;
}
#sepa-container {
    
}

#sepa-container .sepa-field {
    background-color: #F1F1F1;
    padding: 6px;
    border: 1px solid #777;
    min-height: 15px;
    font-weight: bold;
    margin: 3px 0px;
    display: inline-block;
    min-width: 211px;
-moz-border-radius:3px;
-webkit-border-radius:3px;
-khtml-border-radius:3px;
border-radius:3px;*    
}

#sepa-container .sepa-s1 {
    min-width: 300px;
}

#sepa-container .sepa-s2 {
    min-width: 300px;
    height:25px;
}

#sepa-container h3 {
    display:block;
    margin:10px 0px 0px 0px;
    font-size:13px;
}
#sepa-container h1 {
    font-size:14px;
}

#sepa-container .box {
    float:left;
    width:50%;
}

#sepa-container .row {
    clear:both;
}


#sepa-container p {
    font-size:12px;
}

input {
float: left;
margin-right: 10px;
}

</style>
</head>
<body>

<div id="sepa-container">
    <h1>SEPA - Lastschriftmandat</h1>
    <div class="row">
        <div class="box">
            <h3>Name des Zahlungsempfängers:</h3>
            <div class="sepa-field"   ><% $gbl_config.adr_firma %></div>
        </div>
        <div class="box">       
            <h3>Anschrift des Zahlungsempfängers:</h3>
            <div class="sepa-field"   ><% $gbl_config.adr_street %></div>
      </div>
    </div>             
    <div class="row">
        <div class="box">
        <h3>Postleitzahl und Ort:</h3>
        <div class="sepa-field"   ><% $gbl_config.adr_plz %> <% $gbl_config.adr_town %></div>
        </div>
        <div class="box">       
        <h3>Land:</h3> <div class="sepa-field"   >Deutschland</div>
      </div>
    </div> 
    <div class="row">
        <div class="box">
            <h3>Mandatsreferenz:</h3>
            <div class="sepa-field"   ><%$customer.mandat_ref%></div>
        </div> 
        <div class="box">
            <h3>Gläubiger-Identifikationsnummer:</h3>
            <div class="sepa-field"   ><% $gbl_config_shop.bank_sepaglid %></div>
        </div> 
    </div>    
    <p>Ich ermächtige / Wir ermächtigen (A) den Zahlungsempfänger FitMitReha GmbH, Zahlungen von meinem / unserem Konto mittels Lastschrift einzuziehen. Zugleich (B) weise ich mein / weisen wir unser Kreditinstitut an,
        die vom Zahlungsempfänger FitMItReha GmbH auf mein / unser Konto gezogenen Lastschriften einzulösen.
    </p>
    <p>Heinweis: Ich kann / Wir können innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem / userem Kreditinstitut vereinbarten Bedingungen.</p>
    <h3>Zahlungsart:</h3>
    <div class="sepa-field"><input type="checkbox" checked>Wiederkehrende Zahlung</div>
    <div class="row">
        <div class="box">
            <h3>Name des Zahlungspflichtigen:</h3>
            <div class="sepa-field"   ><%$customer.vorname%> <%$customer.nachname%></div>
         </div>
        <div class="box">   
            <h3>Anschrift des Zahlungspflichtigen:</h3>
            <div class="sepa-field"   ><%$customer.strasse%> <%$customer.hausnr%></div>
        </div>
    </div>        
    <div class="row">
        <div class="box">
            <h3>Postleitzahl und Ort:</h3>
            <div class="sepa-field"   ><%$customer.plz%> <%$customer.ort%></div>
        </div>
        <div class="box">
            <h3>Land:</h3> <div class="sepa-field"  ><%$customer.COUNTRY%></div>
        </div>    
    </div>
    <div class="row">
        <div class="box">
            <h3>IBAN des Zahlungspflichtigen:</h3>
            <div class="sepa-field"    ><%$customer.iban%></div>
        </div>
        <div class="box">    
            <h3>BIC:</h3>
            <div class="sepa-field"   ><%$customer.bic%></div>
       </div>    
    </div> 
    <div class="row"><br>     <br>     <br>     
    <h3>Ort, Datum</h3>
    <div class="sepa-field sepa-s2"   ></div>
    <h3>Unterschrift(en) des Zahlungspflichtigen (Kontoinhaber)</h3>
    <div class="sepa-field sepa-s2"   ></div>
    </div>

    
</div>
</body>
</html>
