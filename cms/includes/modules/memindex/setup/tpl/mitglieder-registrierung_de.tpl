<% if ($customer.kid>0) %>
 <h1>Mitgliedsdaten ändern</h1>
<% else %> 
 <h1>Registration</h1>
<% /if %>

<form role="form" action="<% $PHPSELF %>" method="post" id="orderform" name="orderform" onSubmit="return valform()" enctype="multipart/form-data">
    <input name="cmd" type="hidden" value="<% if ($CU_LOGGEDIN==false) %>insert<%else%>update<%/if%>">
    <input type="hidden" name="page" value="<% $page %>">
    <input type="hidden" name="token" value="<% $cms_token%>">
<div class="row">
<div class="col-md-4">
<h2>Kontaktdaten</h2>
    <div class="form-group">
        <label for="fmrgeschlecht" class="sr-only">{LBL_ANREDE}</label>
        <select class="form-control" id="fmrgeschlecht" name="FORM[geschlecht]"><% $kregform.salutselect %></select>
    </div>

    <div class="form-group">
        <label for="fmrfirma"  class="sr-only">{LBL_FIRMENNAME}</label>
        <input type="text" class="form-control <% if ($kregform_err.firma!='') %> has-error<% /if %>" id="fmrfirma" placeholder="{LBL_FIRMENNAME}" name="FORM[firma]" value="<% $kregform.firma %>" >
    </div>  

    <div class="form-group">
        <label for="vorname"  class="sr-only">{LBL_VORNAME}*</label>
        <input type="text" class="form-control<% if ($kregform_err.vorname!='') %> has-error<% /if %>" id="vorname" placeholder="Vorname" required name="FORM_NOTEMPTY[vorname]" value="<% $kregform.vorname %>" >
    </div> 
    
    <div class="form-group">
        <label for="fmrnachname"  class="sr-only">{LBL_NACHNAME}*</label>
        <input type="text" class="form-control<% if ($kregform_err.nachname!='') %> has-error<% /if %>" id="fmrnachname" placeholder="Nachname" required name="FORM_NOTEMPTY[nachname]" value="<% $kregform.nachname %>" >
    </div>   

    <div class="form-group">
        <label for="fmrnachname"  class="sr-only">{TMPL_CFL_1}*</label>
        {TMPL_CFI_1}
    </div>      
     

</div>

<div class="col-md-4">
    <h2>{LBL_ADDRESS}</h2>
    <div class="form-group">
        <div class="row">
            <div class="col-md-8">
                <label for="strasse"  class="sr-only">{LBL_STRASSE}*</label>
                <input type="text" class="form-control<% if ($kregform_err.strasse!='') %> has-error<% /if %>" id="strasse" placeholder="Strasse" required name="FORM_NOTEMPTY[strasse]" value="<% $kregform.strasse %>" >
            </div>
            <div class="col-md-4">    
                <label for="hausnr"  class="sr-only">HausNr*</label>
                <input type="text" class="form-control<% if ($kregform_err.hausnr!='') %> has-error<% /if %>" id="hausnr" placeholder="Hausnr." required name="FORM_NOTEMPTY[hausnr]" value="<% $kregform.hausnr %>" >
            </div>    
        </div>
    </div> 

    <div class="form-group">
        <label for="plz"  class="sr-only">{LBL_PLZ}*</label>
        <input type="text" class="form-control<% if ($kregform_err.plz!='') %> has-error<% /if %>" id="plz" placeholder="PLZ" required name="FORM_NOTEMPTY[plz]" value="<% $kregform.plz %>" >
    </div>  

    <div class="form-group">
        <label for="ort"  class="sr-only">{LBL_ORT}*</label>
        <input type="text" class="form-control<% if ($kregform_err.ort!='') %> has-error<% /if %>" id="ort" placeholder="Ort" required name="FORM_NOTEMPTY[ort]" value="<% $kregform.ort %>" >
    </div>   

    <div class="form-group">
        <label for="land"  class="sr-only">{LBL_LAND}*</label>
        <select class="form-control<% if ($kregform_err.land!='') %> has-error<% /if %>" id="land" name="FORM[land]"><% $kregform.countryselect %></select>
    </div>   

    <div class="form-group">
        <label for="tel"  class="sr-only">{LBL_TELEFON}*</label>
        <input type="text" class="form-control<% if ($kregform_err.tel!='') %> has-error<% /if %>" id="tel" placeholder="Telefon" required name="FORM_NOTEMPTY[tel]" value="<% $kregform.tel %>" >
    </div>    
    <div class="form-group">
        <label for="tel"  class="sr-only">{LBL_FAX}*</label>
        <input type="text" class="form-control<% if ($kregform_err.fax!='') %> has-error<% /if %>" id="fax" placeholder="{LBL_FAX}" required name="FORM[fax]" value="<% $kregform.fax %>" >
    </div>       

  
<% if ($gbl_config.newsletter_disable_unreg==0) %>
    <div class="form-group">
        <label for="mailactive"  class="sr-only">{LBL_NEWSLETTER}*</label>
        <input class="form-control" id="mailactive" <% if ($kregform.mailactive==1) %> checked <% /if %> type="checkbox" name="FORM[mailactive]" value="1">{LBL_NEWSACTIVE}
    </div>    
<%/if%> 

<% if ($gbl_config.captcha_active==1) %>
    <div class="form-group">
        <label for="capcha"  class="sr-only">{LBL_SECODE}*</label>
        <img title="{LBL_SECODE}" alt=""  src="<%$PATH_CMS%>captcha.php">
        {LBL_CODEENTER}:<br>
        <input type="text" class="form-control<% if ($kregform_err.securecode!='') %> has-error<% /if %>" id="securecode" placeholder="Capcha Text" required name="securecode" value="" >
    </div> 
 <% /if %>   
</div> 

<div class="col-md-4">   
<h2>Login Daten</h2>
    <div class="form-group">
        <label for="email"  class="sr-only">Email*</label>
        <input type="email" class="required form-control<% if ($kregform_err.email!='') %> has-error<% /if %>" id="email" placeholder="Email" required name="FORM[email]" value="<% $kregform.email %>" >
    </div>     

    <div class="form-group">
        <label for="passwort"  class="sr-only">{LBL_PASSWORT}*</label>
        <input type="password" class="<% if ($CU_LOGGEDIN==false) %>required<%/if%> form-control<% if ($kregform_err.passwort!='') %> has-error<% /if %>" id="passwort" placeholder="Passwort" <% if ($CU_LOGGEDIN==false) %>required<%/if%> name="FORM[passwort]" value="<% $kregform.passwort %>" >
    </div> 

<h2>Bankverbindung</h2>
    <div class="form-group">
        <label for="iban"  class="sr-only">IBAN*</label>
        <input type="text" class="form-control<% if ($kregform_err.iban!='') %> has-error<% /if %>" id="passwort" placeholder="IBAN" required name="FORM_NOTEMPTY[iban]" value="<% $kregform.iban %>" >
    </div> 

    <div class="form-group">
        <label for="bic"  class="sr-only">BIC*</label>
        <input type="text" class="form-control<% if ($kregform_err.bic!='') %> has-error<% /if %>" id="bic" placeholder="BIC" required name="FORM_NOTEMPTY[bic]" value="<% $kregform.bic %>" >
    </div>

    <div class="form-group">
        <label for="bank"  class="sr-only">Bank*</label>
        <input type="text" class="form-control<% if ($kregform_err.bank!='') %> has-error<% /if %>" id="bank" placeholder="Bank" required name="FORM_NOTEMPTY[bank]" value="<% $kregform.bank %>" >
        <% if ($kregform_err.bank!='') %><span class="glyphicon glyphicon-remove form-control-feedback"></span><% /if %>
    </div>    
  
</div>

</div>    <!-- ENDE ROW -->
<div class="row">
    <div class="col-md-6">  
        <h2>{LBL_YOURFOTO}</h2>
        <% include file="fileupload.tpl" %>
    </div>
    <div class="col-md-6">  
             <% if ($CU_LOGGEDIN==false) %>
        <h2>AGB</h2>
        <div class="checkbox">
            <label> 
                <input type="checkbox" required name="agbtrue" value="1">Die <a href="{URL_TPL_10045}">AGB</a> habe ich zur Kenntis genommen und mit ihrer Geltung bin ich einverstanden
            </label>    
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" required name="wr" value="1">Die <a href="{URL_TPL_10045}">Wiederrufsbelehrung</a> habe ich zur Kenntis genommen
            </label>    
        </div>    
  

    <%/if%>
    </div>
</div>    <!-- ENDE ROW -->


<h2>Themen</h2>                
<% foreach from=$member_collections item=colg name=cgloop %>
 <br> <h3><% $colg.col_name %></h3>
 <table class="table table-hover">
 <tbody>
 <tr><td>
 <% if ($colg.col_id==1) %>
   <% assign var=line_break value=8 %>
  <% else %>
  <% assign var=line_break value=11 %>
  <% /if %>
<% foreach from=$colg.groups item=group name=gloop %>
<input type="checkbox" <% $group.checked %> name="MEMBERGROUPSCOL[<% $group.gid %>_<% $colg.col_id%>]" value="<% $group.gid %>_<% $colg.col_id%>"> <% $group.groupname %><br>
 <% if $smarty.foreach.gloop.iteration % $line_break == 0 %></td><td valign="top"><% /if %>
<% /foreach %></td></tr>
</tbody>
</table>
<% /foreach %>
 
     
    <br>
<% if ($CU_LOGGEDIN==false) %>
<script type="text/javascript">
<!--
function valform() {
 if (document.orderform.agbtrue.checked) {
 return true;
} else {
 alert('{LBL_AGBNOTCHECKED}');
 return false;
}
}
// -->
</script>

    <br><% html_subbtn class="btn btn-primary" value="{LBL_REGISTER}" %>
<% /if %>
<% if ($CU_LOGGEDIN==true) %>
    <% html_subbtn class="btn btn-primary" value="{LBL_BTN_SAVE}" %>
<% /if %>
</form>