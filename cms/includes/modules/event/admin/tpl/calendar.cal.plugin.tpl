<div class="form-group">
    <label>Template:</label>
    <select class="form-control" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>
<div class="form-group">
        <label>Sektion Überschrift:</label>
        <input class="form-control js-num-field" required="" type="text" name="PLUGFORM[title]" placeholder="Neueste Events" value="<%$WEBSITE.node.tm_plugform.title|sthsc%>"/>
    </div>
<div class="row">   
    <div class="form-group col-md-4">
        <label>Thumb Breite:</label>
        <input class="form-control" type="text" required="" name="PLUGFORM[width]" placeholder="z.B. 320" value="<%$WEBSITE.node.tm_plugform.width|sthsc%>"/>
    </div>
    
    <div class="form-group col-md-4">
        <label>Thumb Höhe:</label>
        <input class="form-control" type="text" required="" name="PLUGFORM[height]" placeholder="z.B. 200" value="<%$WEBSITE.node.tm_plugform.height|sthsc%>"/>
    </div>
    <div class="form-group col-md-4">
        <label>Resize Method:</label>
        <select class="form-control" name="PLUGFORM[method]">
            <option <% if ($WEBSITE.node.tm_plugform.method=='crop') %>selected<%/if%> value="crop">crop</option>
            <option <% if ($WEBSITE.node.tm_plugform.method=='resize') %>selected<%/if%> value="resize">resize</option>
            <option <% if ($WEBSITE.node.tm_plugform.method=='boxed') %>selected<%/if%> value="boxed">boxed</option>
        </select>
    </div>
</div>
<div class="row">   
    <div class="form-group col-md-4">
        <label>Fullsize Image Breite:</label>
        <input class="form-control" type="text" required="" name="PLUGFORM[width_big]" placeholder="z.B. 320" value="<%$WEBSITE.node.tm_plugform.width_big|sthsc%>"/>
    </div>
    
    <div class="form-group col-md-4">
        <label>Fullsize Image Höhe:</label>
        <input class="form-control" type="text" required="" name="PLUGFORM[height_big]" placeholder="z.B. 200" value="<%$WEBSITE.node.tm_plugform.height_big|sthsc%>"/>
    </div>
    <div class="form-group col-md-4">
        <label>Resize Method:</label>
        <select class="form-control" name="PLUGFORM[method_big]">
            <option <% if ($WEBSITE.node.tm_plugform.method_big=='crop') %>selected<%/if%> value="crop">crop</option>
            <option <% if ($WEBSITE.node.tm_plugform.method_big=='resize') %>selected<%/if%> value="resize">resize</option>
            <option <% if ($WEBSITE.node.tm_plugform.method_big=='boxed') %>selected<%/if%> value="boxed">boxed</option>
        </select>
    </div>
</div>