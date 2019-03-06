<div style="width:900px">
<form action="<%$PHPSELF%>" method="POST" class="jsonform form-inline">
<input type="hidden" name="cmd" value="packandsend">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="modident" value="<%$GET.modident%>">
<fieldset>
<h3><%$MODMAN.modul.module_name%></h3>

<ul>
<% foreach from=$MODMAN.modul item=value key=skey %>
    <li><%$skey%> => <%$value%></li>
<%/foreach%>    
</ul>

<input type="checkbox" name="FORM[verify]">Ja, meine App soll vom Keimeno Team verifiziert werden.
<p>Mit der Verifizierung erhält deine App ein entsprechendes Siegel und spricht für Qualität und Sicherheit. User
bevorzugen Apps, die verifiziert sind. Die Verifizierung kostet einmalig pro App Version <b>39,95EUR</b> inkl. MwSt.</p>
<input type="submit" class="btn btn-primary" value="App packen und an das Keimeno Team senden">
<button class="cancel keicancel">Abbruch</button>
</fieldset>
</form>
</div>

<script>
$( ".keicancel" ).click(function(event) {
    event.preventDefault();
    simple_load('apppool','<%$PHPSELF%>?epage=<%$epage%>&cmd=get_own_apps');
}); 

function load_kei_finish(modid) {
    simple_load('apppool','<%$PHPSELF%>?epage=<%$epage%>&cmd=get_own_apps&showuploaddone=1&modid='+modid);
}
</script>