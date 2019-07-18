<div class="plugin">

<div class="form-group">
    <label>Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="form-group">
    <label>Anzahl Posts:</label>
    <input required="" maxlength="4" type="text" class="form-control" name="PLUGFORM[count]" value="<% $WEBSITE.node.tm_plugform.count|hsc %>">
</div>

<div class="form-group">
    <label>Foto Thumb Breite:</label>
    <input required="" maxlength="4" type="text" class="form-control" name="PLUGFORM[foto_width]" value="<% $WEBSITE.node.tm_plugform.foto_width|hsc %>">
</div>

<div class="form-group">
    <label>Foto Thumb H&ouml;he:</label>
    <input required="" maxlength="4" type="text" class="form-control" name="PLUGFORM[foto_height]" value="<% $WEBSITE.node.tm_plugform.foto_height|hsc %>">
</div>

<div class="form-group">
    <label>Foto Resize Method:</label>
    <select class="form-control custom-select" name="PLUGFORM[foto_resize_method]">
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='none') %>selected<%/if%> value="none">none</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='crop') %>selected<%/if%> value="crop">crop</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='resize') %>selected<%/if%> value="resize">resize</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='resizetofit') %>selected<%/if%> value="resizetofit">resize (fit)</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='resizetofitpng') %>selected<%/if%> value="resizetofitpng">resize (fit PNG)</option>
    </select>
</div>


    
</div>