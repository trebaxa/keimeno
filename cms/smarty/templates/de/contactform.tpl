<section id="contact">
  <div class="container">
     <h2><%$contact.cf_title%></h2>
     <p class="lead"><%$contact.cf_lead%></p>
    <div class="fmrcontactthx" style="display:none">
        <h2><%$contact.cf_thanks_title%></h2>    
        <p><%$contact.cf_thanks%></p>
    </div>

<div class="row">
  <div class="col-md-8">
    <form action="<% $PHPSELF %>" method="post" enctype="multipart/form-data" class="jsonform fmrcontactform">
    <input name="token" type="hidden" value="<% $cms_token %>">   
        <input type="hidden" name="page" value="<% $page %>">
        <input type="hidden" name="cmd" value="sendmsg">
        <input type="hidden" name="ajaxsubmit" value="1">
        <input type="hidden" name="cont_matrix_id" value="<%$cont_matrix_id%>">
        <input type="hidden" value="" name="email" class="hidden">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="vorname"  class="sr-only">{LBL_VORNAME}*</label>
                    <input type="text" <% if ($CU_LOGGEDIN==true) %>disabled<%/if%> class="form-control<% if ($kregform_err.vorname!='') %> has-error<% /if %>" id="vorname" placeholder="Vorname" required name="FORM_NOTEMPTY[vorname]" value="<% if ($CU_LOGGEDIN==true) %><%$customer.vorname%><%else%><% $CONTACTF.values.vorname|sthsc %><%/if%>" >
                </div> 
                <div class="form-group">
                    <label for="nachname"  class="sr-only">{LBL_NACHNAME}*</label>
                    <input type="text" <% if ($CU_LOGGEDIN==true) %>disabled<%/if%> class="form-control<% if ($kregform_err.nachname!='') %> has-error<% /if %>" id="nachname" placeholder="Nachname" required name="FORM_NOTEMPTY[nachname]" value="<% if ($CU_LOGGEDIN==true) %><%$customer.nachname%><%else%><% $CONTACTF.values.nachname|sthsc %><%/if%>" >
                </div>
                <div class="form-group">
                    <label for="datei">Datei</label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""></input>
                        <input id="datei" class="xform-control" type="file" onchange="this.previousElementSibling.value = this.value" multiple value="" name="datei[]"></input>
                        <div class="input-group-btn"><button class="btn btn-default" type="button">Durchsuchen...</button></div>
                    </div>
                </div>
            </div>   
        
            <div class="col-md-6">
                <div class="form-group">
                    <label for="tschapura"  class="sr-only">Email*</label>
                    <input autocomplete="OFF" type="email" class="required form-control<% if ($kregform_err.nachname!='') %> has-error<% /if %>" id="tschapura" placeholder="Email" required name="FORM[tschapura]" value="<% if ($CU_LOGGEDIN==true) %><%$customer.email%><%else%><% $CONTACTF.values.tschapura|sthsc %><%/if%>" >
                </div>    
                <div class="form-group">
                    <label for="tel"  class="sr-only">Rückrufnummer*</label>
                    <input autocomplete="OFF" type="text" class="form-control<% if ($kregform_err.tel!='') %> has-error<% /if %>" id="tel" placeholder="Ihre Rückrufnummer" required name="FORM[tel]" value="<% $CONTACTF.values.tel|sthsc %>" >
                </div>    
                
    <% if ($contact.cf_captcha==1) %>
                <div class="form-group">
                    <label for="tel"  class="sr-only">{LBL_SECODE}*</label>
                    <img title="{LBL_SECODE}" alt=""  src="<%$PATH_CMS%>includes/modules/contactform/contact.captcha.php"> <br>
                    {LBL_CODEENTER}:<input size="6" autocomplete="OFF" name="securecode" class="form-control" type="text"></td>
                </div>
    <% /if %>            
            </div>
            
        </div><!-- row -->
        
        <div class="form-group">
            <label for="fmr-fed-nachricht"  class="sr-only">Nachricht*</label>
            <textarea class="form-control<% if ($kregform_err.tel!='') %> has-error<% /if %>" id="fmr-fed-nachricht" placeholder="Ihre Nachricht" required rows="6" name="FORM_NOTEMPTY[nachricht]"><% $CONTACTF.values.nachricht|sthsc %></textarea>
     </div>
              <div class="checkbox">
                <label for="bt-disclaimer-1">
                  <input type="checkbox" name="disclaimer_check-1" class="js-disclaimer-check" value="1" required="" id="bt-disclaimer-1" onclick="if ($('.js-disclaimer-check:checked').length==3){$('#js-btn-send').prop('disabled',false)}else{$('#js-btn-send').prop('disabled',true)}">
                  Ich bin mit der Verarbeitung meiner angegebenen Daten zum Zwecke der Bearbeitung meiner Anfrage einverstanden
                </label>
              </div>
              
              <div class="checkbox">
                <label for="bt-disclaimer-2">
                  <input type="checkbox" name="disclaimer_check-2" value="1" class="js-disclaimer-check" required="" id="bt-disclaimer-2" onclick="if ($('.js-disclaimer-check:checked').length==3){$('#js-btn-send').prop('disabled',false)}else{$('#js-btn-send').prop('disabled',true)}">
                  Ich habe die <a href="#" title="Datenschutzerklärung">Datenschutzerklärung</a> von <% $gbl_config.adr_general_firmname %> zur Kenntnis genommen.
                </label>
              </div>
              
              <div class="checkbox">
                  <label for="bt-disclaimer-3">
                  <input type="checkbox" name="disclaimer_check-3" value="1" class="js-disclaimer-check" required="" id="bt-disclaimer-3" onclick="if ($('.js-disclaimer-check:checked').length==3){$('#js-btn-send').prop('disabled',false)}else{$('#js-btn-send').prop('disabled',true)}">
                  Ich bin darüber belehrt worden, dass ich meine vorstehende Einwilligung in die Verarbeitung meiner Daten jederzeit unter dem unten angegebenen Link auf der 
                  Kontaktseite dieser Homepage, durch Klick auf den entsprechenden Link in der Bestätigungsmail zu meiner Anfrage, durch gesonderte E-Mail (<% $gbl_config.adr_service_email %>), Telefax (<% $gbl_config.adr_fax %>) 
                  oder Brief an die <% $gbl_config.adr_firma %>, <% $gbl_config.adr_street %>, <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %> widerrufen kann.
                  </label>
                </div>
        <input type="submit" class="btn btn-primary mt-lg" value="senden" id="js-btn-send" disabled="">
    </form>
    <p class="mt-lg">Sie können hier die Einwilligungserklärung widerrufen:<a href="javascript:void(0)" onclick="$('#dsgvo-wider-form').slideToggle()"> jetzt widerrufen</a></p>
    <div id="dsgvo-wider-form">
        <h3>Einwilligungserklärung widerrufen</h3>
       <form action="<% $PHPSELF %>" method="post" enctype="multipart/form-data" class="jsonform">
          <input name="token" type="hidden" value="<% $cms_token %>">   
          <input type="hidden" name="page" value="<% $page %>">
          <input type="hidden" name="cmd" value="send_disclaim_reject">
          <input type="hidden" name="ajaxsubmit" value="1">
          <input type="hidden" name="cont_matrix_id" value="<%$cont_matrix_id%>">
          <input type="hidden" value="" name="email" class="hidden">
          <div class="form-group">
            <label for="tschapura-disclaim">Ihre E-Mail</label>
              <input autocomplete="OFF" type="email" class="required form-control" id="tschapura-disclaim" placeholder="Email" required="" name="FORM[tschapura]" value="<% if ($CU_LOGGEDIN==true) %><%$customer.email%><%else%><% $CONTACTF.values.tschapura|sthsc %><%/if%>" >
          </div>
          <button type="submit" class="btn btn-default">jetzt widerrufen</button>
        
      </form>
    </div>
    <div class="fmrdisclaimthx" style="display:none">
      <div class="alert alert-info">Sie haben eine E-Mail erhalten</div>
    </div>
    
 </div><!--col-->
 <div class="col-md-4 text-right">
    
            <h3>Bürozeiten</h3>
            Montags bis Freitags 09:00 - 18:00 Uhr<br>
    
            <h3>Anschrift</h3>
            <% $gbl_config.adr_firma %><br>
            <% $gbl_config.adr_street %><br>
            <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %><br>
        
            <h3>Kontaktmöglichkeiten</h3>
            Telefon: <% $gbl_config.adr_telefon  %><br>
            Fax: <% $gbl_config.adr_fax  %><br>
            Email: <% $gbl_config.adr_service_email %><br>
    
 </div>
</div><!--row-->

    
  </div>
</section>