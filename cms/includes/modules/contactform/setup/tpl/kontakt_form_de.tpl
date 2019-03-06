<div class="fmrcontactthx" style="display:none">
    <h2>{LBL_DANKENACHRICHT}!</h2>    
    <p>Wir werden umgehend Ihre Anfrage bearbeiten.</p>
</div>

<form action="<% $PHPSELF %>" method="post" enctype="multipart/form-data" class="jsonform fmrcontactform">
<h2>Kontakt</h2>
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
                    <input id="datei" class="xform-control" type="file" onchange="this.previousSibling.value = this.value" value="" name="datei"></input>
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
<% if ($gbl_config.captcha_active==1) %>
            <div class="form-group">
                <label for="tel"  class="sr-only">{LBL_SECODE}*</label>
                <img title="{LBL_SECODE}" alt=""  src="/captcha.php"> <br>
                {LBL_CODEENTER}:<input size="6" autocomplete="OFF" name="securecode" class="form-control" type="text"></td>
            </div>
<% /if %>            
        </div>
    </div><!-- row -->
    
    <div class="form-group">
        <label for="fmr-fed-nachricht"  class="sr-only">Nachricht*</label>
        <textarea class="form-control<% if ($kregform_err.tel!='') %> has-error<% /if %>" id="fmr-fed-nachricht" placeholder="Ihre Nachricht" required name="FORM_NOTEMPTY[nachricht]"><% $CONTACTF.values.nachricht|sthsc %></textarea>
    </div>
   

<br>
    <input type="submit" class="btn btn-primary" value="senden">
</form>

<div class="row">
    <div class="col-md-8">
        <h3>So finden Sie uns</h3>
        <div id="gmap"></div>
    </div>    
    <div class="col-md-4">
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
</div>


<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script>
function reset_contact_form() {
    $('.fmrcontactform').slideUp();
    $('.fmrcontactthx').slideDown();
}
$(document).ready(function() {
    
    // Google Maps
    $('#gmap').gMap({
        address:    '<% $gbl_config.adr_street %> <% $gbl_config.adr_hausnr %>, <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %>',
        maptype:    'ROADMAP',
        zoom:       14,
        markers:    [{address: '<% $gbl_config.adr_street %>, <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %>'}]
    });

});
</script>