<% if ($MODMAN.keideveloper.kid==0) %>
<form action="<%$PHPSELF%>" method="POST" class="jsonform form-inline" id="keidevloginform">
<input type="hidden" name="cmd" value="kelogin">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="text" class="form-control" value="" placeholder="EMail" name="email">
<input type="password" class="form-control" value="" placeholder="Passwort"  name="pwd">
<input type="submit" class="btn btn-primary" value="login">
</form>
<%else%>
<div class="text-info">
Angemeldet als: <%$MODMAN.keideveloper.vorname%> <%$MODMAN.keideveloper.nachname%>
</div>
<script>
 simple_load('apppool','<%$PHPSELF%>?epage=<%$epage%>&cmd=get_own_apps');
</script> 
<%/if%>

<div id="apppool"></div>

<script>
function startsappsel() {
    simple_load('apppool','<%$PHPSELF%>?epage=<%$epage%>&cmd=get_own_apps');
    $('#keidevloginform').fadeout();
}

</script>