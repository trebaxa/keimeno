<div class="form-group">
<label>Redimero Verkaufsformular Auswahl:</label>
<select class="form-control" name="PLUGFORM[sellformid]">
 <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
    <option <% if ($WEBSITE.node.tm_plugform.sellformid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
 <%/foreach%>
</select>
</div>