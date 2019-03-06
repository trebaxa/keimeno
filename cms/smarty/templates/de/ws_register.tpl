<section id="js-reg-form">
   <h2>Workshop buchen</h2>
  
  <form role="form" action="<% $PHPSELF %>" method="post" enctype="multipart/form-data" class="jsonform">
      <input name="cmd" type="hidden" value="register_customer">
      <input type="hidden" name="page" value="<% $page %>">
      <input type="hidden" name="token" value="<% $cms_token%>">
  <div class="row">
  <div class="col-md-4">
  <h2>{LBL_ADDRESS}</h2>
      <div class="form-group">
          <label for="fmrgeschlecht" class="sr-only">{LBL_ANREDE}</label>
          <select class="form-control" id="fmrgeschlecht" name="FORM[geschlecht]">
            <option value="m">Herr</option>
            <option value="w">Frau</option>
          </select>
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
     
     <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                    <label for="plz"  class="sr-only">{LBL_PLZ}*</label>
                    <input type="text" class="form-control<% if ($kregform_err.plz!='') %> has-error<% /if %>" id="plz" placeholder="PLZ" required name="FORM_NOTEMPTY[plz]" value="<% $kregform.plz %>" >
                </div>  
              </div>
              <div class="col-md-8">
              <div class="form-group">
                  <label for="ort"  class="sr-only">{LBL_ORT}*</label>
                  <input type="text" class="form-control<% if ($kregform_err.ort!='') %> has-error<% /if %>" id="ort" placeholder="Ort" required name="FORM_NOTEMPTY[ort]" value="<% $kregform.ort %>" >
              </div>   
        </div>         
  </div>   
      <div class="form-group">
          <label for="land"  class="sr-only">{LBL_LAND}*</label>
          <select class="form-control<% if ($kregform_err.land!='') %> has-error<% /if %>" id="land" name="FORM[land]"><% $WORKSHOP.land_select %></select>
      </div>   
  
      <div class="form-group">
          <label for="tel"  class="sr-only">{LBL_TELEFON}*</label>
          <input type="text" class="form-control<% if ($kregform_err.tel!='') %> has-error<% /if %>" id="tel" placeholder="Telefon" required name="FORM_NOTEMPTY[tel]" value="<% $kregform.tel %>" >
      </div>    
             
  
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
  
  <% if ($contact.cf_cpatcha==1) %>
      <div class="form-group">
          <label for="capcha"  class="sr-only">{LBL_SECODE}*</label>
          <img title="{LBL_SECODE}" alt=""  src="<%$PATH_CMS%>captcha.php">
          {LBL_CODEENTER}:<br>
          <input type="text" class="form-control<% if ($kregform_err.securecode!='') %> has-error<% /if %>" id="securecode" placeholder="Capcha Text" required name="securecode" value="" >
      </div> 
   <% /if %>   
  </div> 
  
  <div class="col-md-4">   
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
  <button type="submit" class="btn btn-primary">kostenpflichtig bestellen</button>
    
  </div>
</div>    <!-- ENDE ROW -->
</form>
</section>
