<div class="form-group">
<label>Inlay:</label>
<select class="form-control" name="PLUGFORM[templateid]">
 <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
    <option <% if ($WEBSITE.node.tm_plugform.templateid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
 <%/foreach%>
</select>
</div>