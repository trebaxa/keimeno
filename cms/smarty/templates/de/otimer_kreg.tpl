<div class="alert alert-info">
  <h2>Ihr Termin: <%$PROG.pr_title%></h2>
  am <%$appointment.timefrom.datime_ger%> um <%$appointment.timeto.time.time%>
</div>
<section>
  <h2>Kontaktdaten</h2>
  <form role="form" action="<%$PHPSELF%>" method="post" id="orderform" enctype="multipart/form-data">
      <input type="hidden" name="aktion" value="kreg"> 
      <input type="hidden" name="page" value="<% $otimer.page %>">
  
      <div class="form-group">
          <label>{LBL_ANREDE}:</label>
          <select class="form-control" name="FORM[geschlecht]"><%$anredeselect%></select>
      </div>
      <div class="form-group">
        <label>{LBL_NACHNAME}*:</label>
        <input name="FORM_NOTEMPTY[nachname]" required="" value="<% $kregform.nachname %>" class="form-control" type="text">
        <% if ($kregform_err.nachname!='') %><span class="important"><% $kregform_err.nachname %></span><% /if %>
      </div>
      <div class="form-group">
        <label>{LBL_VORNAME}*:</label>
        <input name="FORM_NOTEMPTY[vorname]" required="" value="<% $kregform.vorname %>" class="form-control" type="text">
        <% if ($kregform_err.vorname!='') %><span class="important"><% $kregform_err.vorname %></span><% /if %>
      </div>
      <div class="form-group">
                  <label>{LBL_STRASSE}*:</label>
                  <input name="FORM_NOTEMPTY[strasse]" required="" value="<% $kregform.strasse %>" class="form-control" type="text">
                  <% if ($kregform_err.strasse!='') %><span class="important"><% $kregform_err.strasse%></span><% /if %>
      </div>
      <div class="row">
        <div class="form-group col-md-4">
          <label>{LBL_PLZ}*:</label>
          <input id="plz"  name="FORM_NOTEMPTY[plz]" required="" value="<% $kregform.plz %>" class="form-control" type="text">
          <% if ($kregform_err.plz!='') %><span class="important"><% $kregform_err.plz %></span><% /if %>
        </div>
        <div class="form-group col-md-8">
          <label>{LBL_ORT}*:</label>
          <input id="ort"  name="FORM_NOTEMPTY[ort]" required="" value="<% $kregform.ort %>" class="form-control" type="text">
          <% if ($kregform_err.ort!='') %><span class="important"><% $kregform_err.ort%></span><% /if %>
        </div>
      </div>
      <div class="form-group">
          <label>{LBL_LAND}:</label>
          <select class="form-control" name="FORM[land]" size="-1"><%$countrys%></select>
      </div>
      <div class="form-group">
        <label>{LBL_TELEFON}*:</label>
        <input name="FORM_NOTEMPTY[tel]" required="" value="<% $kregform.tel %>" class="form-control" type="text">
        <% if ($kregform_err.tel !='') %><span class="important"><% $kregform_err.tel %></span><% /if %>
      </div>
      <div class="form-group">
        <label>Email*:</label>
        <input autocomplete="OFF" name="FORM[email]" value="<% $kregform.email %>" class="form-control" type="email">
        <% if ($kregform_err.email!='') %><span class="important"><% $kregform_err.email%></span><% /if %>
      </div>
      <div class="checkbox">
          <label>
          <input <% if ($kregform.mailactive==1) %> checked <% /if %> type="checkbox" name="FORM[mailactive]" value="1">
          {LBL_NEWSLETTER}
          </label>
      </div>
    <div class="checkbox">
      <label for="agbtrue">
        <input type="checkbox" required="" id="agbtrue" name="agbtrue" value="1">
        {LBL_AFBREAD}
      </label>
    </div>
  
  <button class="btn btn-primary" type="submit" >verbindlich {LBL_RESERV}</button> 
      
  </form>
</section>
