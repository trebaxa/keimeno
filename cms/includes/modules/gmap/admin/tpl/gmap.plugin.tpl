<div class="form-group">
    <label>Google Maps:</label>
    <select class="form-control" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="form-group">
    <label>Name:</label>
    <input class="form-control" type="text" name="PLUGFORM[name]" value="<%$WEBSITE.node.tm_plugform.name|sthsc%>"/>
</div>    

<div class="form-group">
    <label>Strasse + Hausnr:</label>
    <input class="form-control" type="text" name="PLUGFORM[str]" value="<%$WEBSITE.node.tm_plugform.str|sthsc%>"/>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label>PLZ:</label>
        <input class="form-control" type="text" name="PLUGFORM[plz]" value="<%$WEBSITE.node.tm_plugform.plz|sthsc%>"/>
    </div>
    
    <div class="form-group col-md-6">
        <label>Ort:</label>
        <input class="form-control" type="text" name="PLUGFORM[city]" value="<%$WEBSITE.node.tm_plugform.city|sthsc%>"/>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label>Breite:</label>
        <input class="form-control" type="text" name="PLUGFORM[width]" placeholder="z.B. 100%" value="<%$WEBSITE.node.tm_plugform.width|sthsc%>"/>
    </div>
    
    <div class="form-group col-md-6">
        <label>Höhe:</label>
        <input class="form-control" type="text" name="PLUGFORM[height]" placeholder="z.B. 390px" value="<%$WEBSITE.node.tm_plugform.height|sthsc%>"/>
    </div>
</div>