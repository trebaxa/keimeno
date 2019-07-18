<div class="form-group">
    <label>Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="row">
<div class="form-group col-md-4">
        <label>Sektion Überschrift:</label>
        <input class="form-control js-num-field" required="" type="text" name="PLUGFORM[title]" placeholder="Neueste Events" value="<%$WEBSITE.node.tm_plugform.title|sthsc%>"/>
    </div>
    <div class="form-group col-md-4">
        <label>Theme:</label>
        <select class="form-control custom-select" name="PLUGFORM[groupid]">
            <option <% if ($WEBSITE.node.tm_plugform.groupid==0) %>selected<%/if%> value="0">- alle -</option>
           <% foreach from=$event.themes item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.groupid==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.groupname%></option>
           <%/foreach%> 
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>Jahr:</label>
        <select class="form-control custom-select" name="PLUGFORM[year]">
            <option <% if ($WEBSITE.node.tm_plugform.year==0) %>selected<%/if%> value="0">- egal -</option>
           <%section name=chk start=$event.first_event.JAHR max=$event.last_event.JAHR+1 loop=$event.last_event.JAHR+1 step=1%>
            <option <% if ($WEBSITE.node.tm_plugform.year==$smarty.section.chk.index) %>selected<%/if%> value="<%$smarty.section.chk.index%>"><%$smarty.section.chk.index%></option>
           <%/section%> 
        </select>
    </div>
</div>    
    
<div class="row">
    <div class="form-group col-md-3">
        <label>Anzahl:</label>
        <input class="form-control js-num-field" required="" type="text" name="PLUGFORM[limit]" placeholder="z.B. 6" value="<%$WEBSITE.node.tm_plugform.limit|sthsc%>"/>
    </div>

    <div class="form-group col-md-3">
        <label>Thumb Breite:</label>
        <input class="form-control" type="text" name="PLUGFORM[width]" placeholder="z.B. 320" value="<%$WEBSITE.node.tm_plugform.width|sthsc%>"/>
    </div>
    
    <div class="form-group col-md-3">
        <label>Thumb Höhe:</label>
        <input class="form-control" type="text" name="PLUGFORM[height]" placeholder="z.B. 200" value="<%$WEBSITE.node.tm_plugform.height|sthsc%>"/>
    </div>
    <div class="form-group col-md-3">
        <label>Thumb Resize Method:</label>
        <select class="form-control custom-select" name="PLUGFORM[method]">
            <option <% if ($WEBSITE.node.tm_plugform.method=='crop') %>selected<%/if%> value="crop">crop</option>
            <option <% if ($WEBSITE.node.tm_plugform.method=='resize') %>selected<%/if%> value="resize">resize</option>
            <option <% if ($WEBSITE.node.tm_plugform.method=='boxed') %>selected<%/if%> value="boxed">boxed</option>
        </select>
    </div>
</div>

<div class="row">   
    <div class="form-group col-md-4">
        <label>Fullsize Image Breite:</label>
        <input class="form-control" type="text" name="PLUGFORM[width_big]" placeholder="z.B. 320" value="<%$WEBSITE.node.tm_plugform.width_big|sthsc%>"/>
    </div>
    
    <div class="form-group col-md-4">
        <label>Fullsize Image Höhe:</label>
        <input class="form-control" type="text" name="PLUGFORM[height_big]" placeholder="z.B. 200" value="<%$WEBSITE.node.tm_plugform.height_big|sthsc%>"/>
    </div>
    <div class="form-group col-md-4">
        <label>Resize Method:</label>
        <select class="form-control custom-select" name="PLUGFORM[method_big]">
            <option <% if ($WEBSITE.node.tm_plugform.method_big=='crop') %>selected<%/if%> value="crop">crop</option>
            <option <% if ($WEBSITE.node.tm_plugform.method_big=='resize') %>selected<%/if%> value="resize">resize</option>
            <option <% if ($WEBSITE.node.tm_plugform.method_big=='boxed') %>selected<%/if%> value="boxed">boxed</option>
        </select>
    </div>
</div>

    <div class="form-group col-md-6">
        <label>Sortierung:</label>
        <select class="form-control custom-select" name="PLUGFORM[sort]">
            <option <% if ($WEBSITE.node.tm_plugform.sort=='ndate') %>selected<%/if%> value="ndate">Datum</option>
            <option <% if ($WEBSITE.node.tm_plugform.sort=='rnd') %>selected<%/if%> value="rnd">zufällig</option>
            <option <% if ($WEBSITE.node.tm_plugform.sort=='EID') %>selected<%/if%> value="EID">id</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>Sortierung Richtung:</label>
        <select class="form-control custom-select" name="PLUGFORM[sort_direc]">
            <option <% if ($WEBSITE.node.tm_plugform.sort_direc=='ASC') %>selected<%/if%> value="ASC">aufsteigend</option>
            <option <% if ($WEBSITE.node.tm_plugform.sort_direc=='DESC') %>selected<%/if%> value="DESC">absteigend</option>
        </select>
    </div>