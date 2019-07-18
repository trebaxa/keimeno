<fieldset class="plugin">
<div class="form-group">
   <label>Facebook Events Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
  </div>
  <div class="row">  
        <div class="form-group col-md-4">
            <label>Anzahl</label>
            <input type="text" class="form-control js-num-field" value="<%$WEBSITE.node.tm_plugform.limit|sthsc%>" name="PLUGFORM[limit]" />
        </div>
        
        <div class="form-group col-md-4">
            <label>Überschrift</label>
            <input type="text" class="form-control" value="<%$WEBSITE.node.tm_plugform.title|sthsc%>" name="PLUGFORM[title]" />
        </div>
        <div class="form-group col-md-4">
            <label>
                Termine liegen in der:
             </label>   
                <select name="PLUGFORM[time_filter]" class="form-control">
                    <option value="">- alle -</option>
                    <option <% if ($WEBSITE.node.tm_plugform.time_filter=='upcoming') %>selected<%/if%> value="upcoming">Zukunft</option>
                    <option <% if ($WEBSITE.node.tm_plugform.time_filter=='past') %>selected<%/if%> value="past">Vergangenheit</option>
                </select>
            
        </div>
    </div>
    
    
    
</fieldset>   