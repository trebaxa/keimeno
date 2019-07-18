<section class="plugin">
    <div class="form-group">
        <label>Features Tabelle:</label>
        <select class="form-control custom-select" name="PLUGFORM[feature_group_id]">
         <% foreach from=$WEBSITE.PLUGIN.result.groups item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.feature_group_id==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
         <%/foreach%>
        </select>
    </div>
    
    <div class="form-group">
        <label>Template:</label>
        <select class="form-control custom-select" name="PLUGFORM[tpl_name]">
            <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
                <option <% if ($WEBSITE.node.tm_plugform.tpl_name==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
            <%/foreach%>
        </select>
    </div>
      
    <div class="form-group">
        <label>Sort:</label>
        <select class="form-control custom-select" name="PLUGFORM[column]">
                <option <% if ($WEBSITE.node.tm_plugform.column=='f_title') %>selected<%/if%> value="f_title">Titel</option>                
                <option <% if ($WEBSITE.node.tm_plugform.column=='f_order') %>selected<%/if%> value="f_order">Manuelle Sortierung</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>Sort. Richtung:</label>
        <select class="form-control custom-select" name="PLUGFORM[sort]">
                <option <% if ($WEBSITE.node.tm_plugform.sort=='ASC') %>selected<%/if%> value="ASC">ASC</option>
                <option <% if ($WEBSITE.node.tm_plugform.sort=='DESC') %>selected<%/if%> value="DESC">DESC</option>
        </select>
    </div>    
    
    <div class="form-group">
        <label>Thumb Breite:</label>
        <input maxlength="3" type="text" class="form-control" name="PLUGFORM[thumb_width]" value="<% $WEBSITE.node.tm_plugform.thumb_width|sthsc %>">
    </div>    
    
    <div class="form-group">
        <label>Thumb Höhe:</label>
        <input maxlength="3" type="text" class="form-control" name="PLUGFORM[thumb_height]" value="<% $WEBSITE.node.tm_plugform.thumb_height|sthsc %>">
    </div> 
    
    <div class="form-group">
        <label>Methode:</label>
        <select class="form-control custom-select" name="PLUGFORM[thumb_type]">        
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='crop') %>selected<%/if%> value="crop">zuschneiden (crop)</option>        
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resize') %>selected<%/if%> value="resize">verkleinern (resize)</option>
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resizetofit') %>selected<%/if%> value="resizetofit">verkleinern (fit)</option>
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resizetofitpng') %>selected<%/if%> value="resizetofitpng">verkleinern (fit PNG)</option>
        </select>
    </div>    
    
    <div class="form-group">
        <label>Crop Position:</label>    
        <select class="form-control custom-select" name="PLUGFORM[g_croppos]" >
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='NorthWest') %>selected<%/if%> value="NorthWest">NorthWest</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='North') %>selected<%/if%> value="North">North</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='NorthEast') %>selected<%/if%> value="NorthEast">NorthEast</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='West') %>selected<%/if%> value="West">West</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='Center') %>selected<%/if%> value="Center">Center</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='East') %>selected<%/if%> value="East">East</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='SouthWest') %>selected<%/if%> value="SouthWest">SouthWest</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='South') %>selected<%/if%> value="South">South</option>
            <option <% if ($WEBSITE.node.tm_plugform.g_croppos=='SouthEast') %>selected<%/if%> value="SouthEast">SouthEast</option>
        </select>
    </div>            
          
    
</section>