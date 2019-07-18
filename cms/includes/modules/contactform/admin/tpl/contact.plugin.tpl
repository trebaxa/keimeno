   <div class="form-group"> 
    <label>Template</label> 
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
  </div>
  <div class="row">
      <div class="form-group col-md-6">  
        <label>Empfänger Email</label>
        <input type="email" class="form-control" name="PLUGFORM[email]" value="<%$WEBSITE.node.tm_plugform.email%>" required>
      </div>
      <div class="form-group col-md-6 ">  
        <label>Empfänger Email Auswahl</label><br>
        <div class="form-inline" id="js-email-form">
            <input placeholder="Name, Label, Beschriftung" type="text" class="form-control" name="EFORM[label]" value=""/>
            <div class="input-group">
                <input placeholder="E-Mail" type="email" class="form-control" name="EFORM[email]" value="" />
                <div class="input-group-btn">
                    <button type="button" onclick="save_contact_emaillist();" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                </div>
            </div>
        </div>
        <div id="js-elist"></div>
      </div>
  </div>  
  <script>
    function save_contact_emaillist() {      
      var url = 'run.php?epage=contact.inc&cmid=<% $WEBSITE.node.id %>&cmd=add_email_to_list&'+$('#js-email-form :input').serialize();
      jsonexec(url, true);
    }
    function reload_elist() {
       simple_load('js-elist', 'run.php?epage=contact.inc&cmid=<% $WEBSITE.node.id %>&cmd=reload_elist'); 
    }
    reload_elist();
  </script>  
  <div class="row">
      <div class="form-group col-md-6">  
        <label>Titel</label>
        <input type="text" class="form-control" name="PLUGFORM[cf_title]" value="<%$WEBSITE.node.tm_plugform.cf_title%>" required>
      </div> 
      <div class="form-group col-md-6">  
        <label>Untertitel</label>
        <input type="text" class="form-control" name="PLUGFORM[cf_lead]" value="<%$WEBSITE.node.tm_plugform.cf_lead%>">
      </div>
  </div>
  <hr>
  <div class="form-group">  
    <label>Gesendet Nachricht Titel</label>
    <input type="text" class="form-control" name="PLUGFORM[cf_thanks_title]" value="<%$WEBSITE.node.tm_plugform.cf_thanks_title|hsc%>" required>
  </div>   
  <div class="form-group">  
    <label>Gesendet Nachricht Text</label>
    <input type="text" class="form-control" name="PLUGFORM[cf_thanks]" value="<%$WEBSITE.node.tm_plugform.cf_thanks|hsc%>" required>
  </div>   
  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[cf_save]" value="1" <% if ($WEBSITE.node.tm_plugform.cf_save==1) %>checked<%/if%> />
        Kontaktdaten nicht in Datenbank speichern
    </label>
  </div>
  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[cf_notschapura]" value="1" <% if ($WEBSITE.node.tm_plugform.cf_notschapura==1) %>checked<%/if%> />
        Abesender E-Mail ist nicht pflicht
    </label>
  </div>
  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[cf_send_we]" value="1" <% if ($WEBSITE.node.tm_plugform.cf_send_we==1) %>checked<%/if%> />
        Widerruf-Email an Besucher/Absender senden
    </label>
  </div>
  <div class="form-group">  
    <label>Widerruf Email Text</label>
    <textarea placeholder="" name="PLUGFORM[cf_we_text]" class="form-control"><%$WEBSITE.node.tm_plugform.cf_we_text|sthsc%></textarea>
    <label>z.B.:</label>
    <p class="well">    
    Hallo,<br><br>Sie haben in die Verarbeitung Ihrer im Kontaktformular angegebenen Daten zum Zwecke der Bearbeitung Ihrer Anfrage eingewilligt. 
    Diese Einwilligung können Sie jederzeit durch Klick auf den nachfolgenden Link <br><%help%><%literal%><%$gbl_config.we_link%> <%/literal%><%/help%>, unter dem entsprechenden Link auf der Kontaktseite unserer Homepage, durch 
                gesonderte E-Mail (<%$gbl_config.adr_service_email%>), Telefax (<%$gbl_config.adr_fax%>) oder Brief 
                an die <%$gbl_config.adr_firma%>, <%$gbl_config.adr_street%>, <%$gbl_config.adr_plz%> <%$gbl_config.adr_town%> widerrufen.
    </p>
  </div>  
  <div class="form-group">  
    <label>Verbotene Wörter</label>
    <textarea placeholder="http://,https://,я,д,з" name="PLUGFORM[cf_forbiddenwords]" class="form-control"><%$WEBSITE.node.tm_plugform.cf_forbiddenwords|hsc%></textarea>
  </div>  
  
  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[cf_captcha]" value="1" <% if ($WEBSITE.node.tm_plugform.cf_captcha==1) %>checked<%/if%> />
        CAPTCHA aktivieren
    </label>
  </div>
  
  <% if ($gbl_config.smtp_use==0) %>
    <div class="alert alert-danger">Es wird empfohlen den Mailversand über SMTP einzurichten, um SPAM Erkennung zu vermeiden. Zusätzlich sollte Ihre 
    Domain den SPF Eintrag in den DNS Einstellungen haben.</div>
  <%/if%>
 