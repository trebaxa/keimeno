<%include file="cb.panel.header.tpl" icon="fa-key" title="Server Passwort festlegen"%>
<div class="row">
<div class="col-md-6">
<form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="aktion" value="save">
	<input type="hidden" name="epage" value="<% $epage %>">

    <div class="form-group">
        <label>Login Name:</label>
        <input autocomplete="off" name="htauser" type="text" class="form-control" value="">
    </div>
    
    <div class="form-group">
        <label>Passwort:</label>
        <input autocomplete="off" name="htapassword" type="password" class="form-control" value="">
    </div>
    
    <div class="form-group">
        <label>Passwort Wiederholung:</label>
        <input autocomplete="off" name="htapassword2" type="password" class="form-control" value="">
    </div>
    
<%$subbtn%><br><br><br>
 
  </form>
</div>
<div class="col-md-6">
    <div class="alert alert-info">Sollten Sie diese Zugangsdaten vergessen, so wenden Sie sich bitte an Ihren Administrator. Er wird Ihren administrativen Bereich wieder freigeben.</div>  
 <% if ($HTAPASS.isprotected==1) %> 
    <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="aktion" value="reset">
	<input type="hidden" name="epage" value="<% $epage %>">
    <div >
    <fieldset>	
    <legend>Server Passwort aufheben</legend>
    <div class="subright"><%$execbtn%></div>
    </fieldset>	
    </div> 
  </form>  
<%/if%>
</div> 
</div>
<%include file="cb.panel.footer.tpl"%> 