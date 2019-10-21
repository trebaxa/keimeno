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
    <label>Elemente Anzahl:</label>
    <input maxlength="2" type="text" class="form-control" name="PLUGFORM[ele_count]" value="<% $WEBSITE.node.tm_plugform.ele_count|hsc %>">
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label>Foto Thumb Breite:</label>
        <input maxlength="3" type="text" class="form-control" name="PLUGFORM[foto_width]" value="<% $WEBSITE.node.tm_plugform.foto_width|hsc %>">
    </div>
    
    <div class="form-group col-md-6">
        <label>Foto Thumb H&ouml;he:</label>
        <input maxlength="3" type="text" class="form-control" name="PLUGFORM[foto_height]" value="<% $WEBSITE.node.tm_plugform.foto_height|hsc %>">
    </div>
    
    <div class="form-group col-md-6">
        <label>Foto Thumb-Small Breite:</label>
        <input maxlength="3" type="text" class="form-control" name="PLUGFORM[foto_small_width]" value="<% $WEBSITE.node.tm_plugform.foto_small_width|hsc %>">
    </div>
    
    <div class="form-group col-md-6">
        <label>Foto Thumb-Small H&ouml;he:</label>
        <input maxlength="3" type="text" class="form-control" name="PLUGFORM[foto_small_height]" value="<% $WEBSITE.node.tm_plugform.foto_small_height|hsc %>">
    </div>
    
</div>    

<div class="form-group">
    <label>Foto Resize Method:</label>
    <select class="form-control custom-select" name="PLUGFORM[foto_resize_method]">
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='crop') %>selected<%/if%> value="crop">crop</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='resize') %>selected<%/if%> value="resize">resize</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='boxed') %>selected<%/if%> value="boxed">boxed</option>        
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='resizetofit') %>selected<%/if%> value="resizetofit">resize (fit)</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='resizetofitpng') %>selected<%/if%> value="resizetofitpng">resize (fit PNG)</option>
    </select>
</div>

<div class="form-group">
    <label for="<%$row.v_col%>-v_settings">Individual Crop Position</label>
    <select class="form-control custom-select" id="<%$row.v_col%>-v_settings" name="PLUGFORM[foto_crop_pos]" >           
            <option <% if ($WEBSITE.node.tm_plugform.foto_crop_pos=='Center')%>selected<%/if%> value="Center">Center</option>
            <option <% if ($WEBSITE.node.tm_plugform.foto_crop_pos=='North')%>selected<%/if%> value="North">North</option>
            <option <% if ($WEBSITE.node.tm_plugform.foto_crop_pos=='NorthEast')%>selected<%/if%> value="NorthEast">NorthEast</option>
            <option <% if ($WEBSITE.node.tm_plugform.foto_crop_pos=='NorthWest')%>selected<%/if%> value="NorthWest">NorthWest</option>
            <option <% if ($WEBSITE.node.tm_plugform.foto_crop_pos=='South')%>selected<%/if%> value="South">South</option>
            <option <% if ($WEBSITE.node.tm_plugform.foto_crop_pos=='SouthEast')%>selected<%/if%> value="SouthEast">SouthEast</option>
            <option <% if ($WEBSITE.node.tm_plugform.foto_crop_pos=='SouthWest')%>selected<%/if%> value="SouthWest">SouthWest</option>
            <option <% if ($WEBSITE.node.tm_plugform.foto_crop_pos=='West')%>selected<%/if%> value="West">West</option>
            <option <% if ($WEBSITE.node.tm_plugform.foto_crop_pos=='East')%>selected<%/if%> value="East">East</option>
    </select>    
</div>  

  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[no_videos]" value="1" <% if ($WEBSITE.node.tm_plugform.no_videos==1) %>checked<%/if%> />
        Keine Videos laden
    </label>
  </div>
  
</div>