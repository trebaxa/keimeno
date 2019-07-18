<fieldset class="plugin">
<label>Feedback Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>

<label>Anzahl:</label>  
<input size="4" maxlength="3" type="text" class="form-control" name="PLUGFORM[limit]" value="<% $WEBSITE.node.tm_plugform.limit %>">

<label>Thumbnail Breite (Profilfoto)):</label>  
<input size="4" maxlength="3" type="text" class="form-control" name="PLUGFORM[thb_width]" value="<% $WEBSITE.node.tm_plugform.thb_width|sthsc %>">

<label>Thumbnail Höhe (Profilfoto)):</label>  
<input size="4" maxlength="3" type="text" class="form-control" name="PLUGFORM[thb_height]" value="<% $WEBSITE.node.tm_plugform.thb_height|sthsc %>">


<label>Sortierung</label>
    <select class="form-control custom-select" name="PLUGFORM[sort]">       
            <option <% if ($WEBSITE.node.tm_plugform.tplid=='sort') %>selected<%/if%> value="ASC">ascending</option>
            <option <% if ($WEBSITE.node.tm_plugform.tplid=='sort') %>selected<%/if%> value="DESC">descending</option>

    </select>     
</fieldset>   