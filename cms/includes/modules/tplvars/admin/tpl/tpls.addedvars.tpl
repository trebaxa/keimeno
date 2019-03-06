<% if (count($TPLVARS.addedvars)>0) %>
    
<h3>Verbundene Variablen</h3>
<form action="<%$PHPSELF%>" class="form jsonform">
<input type="hidden" name="cmd" value="save_added_vars_table">
<input type="hidden" name="epage" value="<%$epage%>">
<table class="table table-striped table-hover">
<% foreach from=$TPLVARS.addedvars item=row %>
<tr  <% if ($row.VID==0) %>style="background-color:#F23437"<%/if%>>
    <td><%$row.var_name%><% if ($row.VID==0) %>INVALID VAR.<%/if%></td>
    <td><%$row.var_type%></td>
    <td><code>{<%$row.m_placeholder%>}</code></td>
    <td><input type="text" class="form-control" name="FORM[<%$row.MID%>][m_hint]" value="<%$row.m_hint|sthsc%>" placeholder="Beschreibung">
    <td><input type="text" class="form-control" name="FORM[<%$row.MID%>][m_order]" value="<%$row.m_order%>">
    <input type="hidden" name="FORM[<%$row.MID%>][id]" value="<%$row.MID%>"></td>
    <td class="text-right"><% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
</tr>
<%/foreach%>
</table>
<%$subbtn%>
</form>

<%/if%>