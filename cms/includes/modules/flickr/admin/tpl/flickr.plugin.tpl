<div class="form-group">
<label>Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="form-group">
<label>Foto Anzahl:</label>
    <input size="3" maxlength="2" type="text" class="form-control" name="PLUGFORM[foto_count]" value="<% $WEBSITE.node.tm_plugform.foto_count|hsc %>">
</div>

<div class="form-group">
<label>Foto Thumb Breite:</label>
    <input size="3" maxlength="3" type="text" class="form-control" name="PLUGFORM[foto_width]" value="<% $WEBSITE.node.tm_plugform.foto_width|hsc %>">
</div>

<div class="form-group">
<label>Foto Thumb H&ouml;he:</label>
    <input size="3" maxlength="3" type="text" class="form-control" name="PLUGFORM[foto_height]" value="<% $WEBSITE.node.tm_plugform.foto_height|hsc %>">
</div>

<div class="form-group">
<label>Sortierung:</label>
      <select class="form-control custom-select" name="PLUGFORM[sortdirec]">
            <option <% if ($WEBSITE.node.tm_plugform.sortdirec='ASC') %>selected<%/if%> value="ASC">aufsteigend</option>
            <option <% if ($WEBSITE.node.tm_plugform.sortdirec='DESC') %>selected<%/if%> value="DESC">absteigend</option>
    </select>
</div>