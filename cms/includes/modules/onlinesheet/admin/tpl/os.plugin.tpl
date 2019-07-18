<section class="plugin">
    <div class="form-group">
        <label>Antrag:</label>
        <select class="form-control custom-select" name="PLUGFORM[os_id]">
         <% foreach from=$WEBSITE.PLUGIN.result.sheets item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.feature_group_id==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
         <%/foreach%>
        </select>
    </div>        
</section>