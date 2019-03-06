<style>
.btnsr {
    background:red;
    color:#fff;
}
.btnsf {
    background:green;
    color:#fff;
}
label {
    display:block;
    font-weight:bold;
}
</style>
<h3>Was m&ouml;chten Sie anlegen?</h3>
<button class="btnsr">Suchergebis - Seite</button>
<button class="btnsf">Suchfeld Formular</button>

<input type="hidden" name="ftype" value="sr" class="ftype">
<input type="hidden" name="urltpl" value="<%$WEBSITE.node.urltpl%>">

<div class="sre">
<fieldset class="plugin">
<label>Anzahl pro Suchergebnis:</label>
    <input size="3" maxlength="2" type="text" class="form-control" name="PLUGFORM[itemcount]" value="<% $WEBSITE.node.tm_plugform.itemcount %>">

<label>Sucherergebnis Template:</label>
<select class="form-control" name="PLUGFORM[srtpl]">
 <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
    <option <% if ($WEBSITE.node.tm_plugform.srtpl==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
 <%/foreach%>
</select>
<div class="bg-info text-info">Bitte folgende URL Variable im Suchformular für "action" verwenden: <%$WEBSITE.node.urltpl%></div>
</fieldset>
</div>

<div class="sfe">
<fieldset class="plugin">
<label>Such-Formular Template:</label>
<select class="form-control" name="PLUGFORM[sformtpl]">
 <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
    <option <% if ($WEBSITE.node.tm_plugform.sformtpl==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
 <%/foreach%>
</select>
</fieldset>
</div>

<script>
$( ".btnsf" ).click(function(event) {
    event.preventDefault();
    $('.sre').hide();
    $('.sfe').show();
    $('.ftype').val('sf');
});
$( ".btnsr" ).click(function(event) {
    event.preventDefault();
    $('.sre').show();
    $('.sfe').hide();
    $('.ftype').val('sr');
});
<% if ($WEBSITE.node.tm_plugform.sformtpl>0) %>
$( ".btnsf" ).click();
<%/if%>
</script>
