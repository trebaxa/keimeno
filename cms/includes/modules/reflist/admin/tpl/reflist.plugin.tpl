<section class="plugin">  
    
    <div class="form-group">
        <label>Template:</label>
        <select class="form-control" name="PLUGFORM[tpl_name]">
            <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
                <option <% if ($WEBSITE.node.tm_plugform.tpl_name==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
            <%/foreach%>
        </select>
    </div>
      
    <div class="form-group">
        <label>Sort:</label>
        <select class="form-control" name="PLUGFORM[column]">
                <option <% if ($WEBSITE.node.tm_plugform.column=='r_firma') %>selected<%/if%> value="r_firma">Firma</option>                
                <option <% if ($WEBSITE.node.tm_plugform.column=='r_plz') %>selected<%/if%> value="r_plz">PLZ</option>
                <option <% if ($WEBSITE.node.tm_plugform.column=='r_city') %>selected<%/if%> value="r_city">Ort</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>Sort. Richtung:</label>
        <select class="form-control" name="PLUGFORM[sort]">
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
        <select class="form-control" name="PLUGFORM[thumb_type]">        
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='crop') %>selected<%/if%> value="crop">zuschneiden (crop)</option>        
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resize') %>selected<%/if%> value="resize">verkleinern (resize)</option>
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resizetofit') %>selected<%/if%> value="resizetofit">verkleinern (fit)</option>
                <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resizetofitpng') %>selected<%/if%> value="resizetofitpng">verkleinern (fit PNG)</option>
        </select>
    </div>     
               
          
    
</section>