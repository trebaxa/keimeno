<% if ($customer.kid <= 0) %>
<form role="form" method="POST" action="<% $HTA_CMSSSLLINKS_CMS.EC_URL %>">
<input type="hidden" name="page" value="950">
<input type="hidden" name="cmd" value="login">
<table class="tab_std"  width="100%">
<tr class="header"><td>Login</td></tr>
<tr><td>
 <table><tr><td>{LBL_IHREEMAIL}:</td><td><input class="form-control" type="text" class="text" name="email" size="15"></td></tr>
<tr><td>{LBL_PASSWORT}:</td><td><input type="password" name="pass" class="text" size="15"></td></tr>
<tr><td colspan="2" align="right"><% html_subbtn class="btn btn-primary" value="{LBL_LOGIN}" %></td></tr>
</table>
</td></tr></table>
</form>
<% /if %>
