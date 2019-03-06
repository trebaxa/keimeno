<fieldset class="plugin">
<div class="form-group">
   <label>Facebook Rating Template:</label>
    <select class="form-control" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
  </div>  
    <div class="form-group">
        <label>Anzahl</label>
        <input type="text" class="form-control" value="<%$WEBSITE.node.tm_plugform.limit|sthsc%>" name="PLUGFORM[limit]" />
    </div>
     
</fieldset>   