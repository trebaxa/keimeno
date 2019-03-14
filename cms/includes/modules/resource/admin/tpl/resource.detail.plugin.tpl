<div class="plugin">

    <div class="form-group">
        <label>Resource:</label>
        <select class="form-control" name="PLUGFORM[resrcid]">
            <% foreach from=$WEBSITE.PLUGIN.result.resources item=row %>
                <option <% if ($WEBSITE.node.tm_plugform.resrcid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
            <%/foreach%>
        </select>
    </div>
</div>

<div class="alert alert-info">
    Metabeschreibungen, "Bezeichnung-URL" werden automatisch aus Resource gesetzt.
</div>

<script>
    $('*[data-ident="#tab5"]').hide();
    $('*[data-ident="#tab6"]').hide();
    $('*[data-ident="#tab7"]').hide();
</script>