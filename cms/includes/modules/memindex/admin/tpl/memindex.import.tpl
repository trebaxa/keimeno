
        <%include file="cb.panel.header.tpl" title="{LA_KUNDENIMPORTIEREN}"%>
        <div class="alert alert-info">
            <p class="alert alert-info"- {LA_SPALTENREIHENFOLGE}: {LA_NACHNAME},{LA_VORNAME},{LA_SSTRASSE},{LA_PPLZ},{LA_OORT},{LA_TTELEFON},E-Mail,Firma,Fax,Land,{LA_GESCHLECHT} (m,w),Homepage,{LA_REGISTRIERUNGSDATUM},Passwort<br>
            - {LA_DASDATEIFORMATMUSSIMC}<br>
            - {LA_CSVTRENNZEICHEN}: ;<br>
            - {LA_KUNDENPASSWORTWIRDAUT}<br>
            - {LA_EXISTIERTBEREITSEINKU}.</p>
        </div><!-- /.bg-info -->

        <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
        
            <div class="form-group">
                <label for="csvupl">{LA_CSVDATEI}</label>
                <input id="csvupl" type="file" name="csvfile" class=file_btn value="durchsuchen">
            </div><!-- /.form-group -->
            <div class="form-group">
                <label for="targrp">{LA_ZIELKUNDENGRUPPE}</label>
                <select id="targrp" class="form-control" name="FORM[kundengruppe]" ><% $POBJ.targetgroup %></select>
            </div><!-- /.form-group -->
            
                
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="changegroup" value="">
                        {LA_JADATENSATZAKTUALISIE}
                    </label>
                </div><!-- /checkbox -->
            
            <div class="form-group">
                <label for="mitid">{LA_MITARBEITERZUORDNUNG}</label>
                <select id="mitid" class="form-control" name="FORM[mit_id]" size=1><% $POBJ.mitselect %></select>
            </div><!-- /.form-group -->
            
            <div class="form-group">
                <label for="">{LA_IDENTIFIZIERUNG} ({LA_DOPPELTEEINTRAGUNGENV})</label>
                <div class="radio">
                    <label>
                        <input checked="" type="radio" name="identy" value="email">
                        {LA_PEREMAIL}
                    </label>
                </div><!-- /.radio -->
                <div class="radio">
                    <label>
                        <input type="radio" name="identy" value="names"> 
                        {LA_PERNACHNAMEVORNAME}
                    </label>
                </div><!-- /.radio -->
            </div><!-- /.form-group -->
            <div class="form-group">
                <label for="">{LA_DATENSATZOHNEEMAILAUC}</label>
                <div class="radio">
                    <label>
                        <input checked="" type="radio" name="emailimport" value="1">
                        {LA_JA}
                    </label>
                </div><!-- /.radio -->
                <div class="radio">
                    <label>
                        <input type="radio" name="emailimport" value="0">
                        {LA_NEIN}
                    </label>
                </div><!-- /.radio -->
            </div><!-- /.form-group -->
            <div class="form-group">
                <label for="">{LA_CSVDATENLIEGENIMUTF8F}</label>
                <div class="radio">
                    <label>
                        <input checked="" type="radio" name="utf8convert" value="1">
                        {LA_JA}
                    </label>
                </div><!-- /.radio -->
                <div class="radio">
                    <label>
                        <input type="radio" name="utf8convert" value="0">
                        {LA_NEIN}
                    </label>
                </div><!-- /.radio -->
            </div><!-- /.form-group -->
            <div class="form-group">
                <label for="">{LA_PASSWORTISTMD5VERSCHL}</label>
                <div class="radio">
                    <label>
                        <input checked="" type="radio" name="md5pass" value="1">
                        {LA_JA}
                    </label>
                </div><!-- /.radio -->
                <div class="radio">
                    <label>
                        <input type="radio" name="md5pass" value="0">
                        {LA_NEIN}
                    </label>
                </div><!-- /.radio -->
            </div><!-- /.form-group -->
            <div class="form-group">
                <label for="csvdivider">{LA_CSVTRENNZEICHEN}</label>
                <input id="csvdivider" type="text" class="form-control" maxlength=1 name="trennzeichen" value=";">
            </div><!-- /.form-group -->
            <input type="hidden" name="cmd" value="a_import">
            <div class="form-feet"><% $importbtn %></div><!-- /.form-feet -->
        </form>
            <%include file="cb.panel.footer.tpl"%>
    <% $POBJ.CSV_IMPORT %>