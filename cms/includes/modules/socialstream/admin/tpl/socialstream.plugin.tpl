<div class="plugin">

<div class="form-group">
    <label>Template:</label>
    <select class="form-control" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="form-group">
    <label>Elemente Anzahl:</label>
    <input maxlength="2" type="text" class="form-control" name="PLUGFORM[ele_count]" value="<% $WEBSITE.node.tm_plugform.ele_count|hsc %>">
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
    <label>Foto Resize Method:</label>
    <select class="form-control" name="PLUGFORM[foto_resize_method]">
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='crop') %>selected<%/if%> value="crop">crop</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='resize') %>selected<%/if%> value="resize">resize</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='resizetofit') %>selected<%/if%> value="resizetofit">resize (fit)</option>
        <option <% if ($WEBSITE.node.tm_plugform.foto_resize_method=='resizetofitpng') %>selected<%/if%> value="resizetofitpng">resize (fit PNG)</option>
    </select>
</div>

<div class="form-group">
    <label>Sortierung:</label>
      <select class="form-control" name="PLUGFORM[sortdirec]">
            <option <% if ($WEBSITE.node.tm_plugform.sortdirec=='ASC') %>selected<%/if%> value="ASC">absteigend</option>
            <option <% if ($WEBSITE.node.tm_plugform.sortdirec=='DESC') %>selected<%/if%> value="DESC">aufsteigend</option>
    </select>
</div>

  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[no_videos]" value="1" <% if ($WEBSITE.node.tm_plugform.no_videos==1) %>checked<%/if%> />
        Keine Videos laden
    </label>
  </div>   

  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[no_fb]" value="1" <% if ($WEBSITE.node.tm_plugform.no_fb==1) %>checked<%/if%> />
        Kein Facebook laden
    </label>
  </div>   
  
  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[no_blog]" value="1" <% if ($WEBSITE.node.tm_plugform.no_blog==1) %>checked<%/if%> />
        Kein Blog laden
    </label>
  </div> 

  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[no_tw]" value="1" <% if ($WEBSITE.node.tm_plugform.no_tw==1) %>checked<%/if%> />
        Kein Twitter laden
    </label>
  </div>    

  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[no_fl]" value="1" <% if ($WEBSITE.node.tm_plugform.no_fl==1) %>checked<%/if%> />
        Kein Flickr laden
    </label>
  </div>     
    
</div>