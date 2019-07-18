<div class="plugin">

<div class="form-group">
    <label>Titel:</label>
    <input type="text" class="form-control" name="PLUGFORM[title]" value="<%$WEBSITE.node.tm_plugform.title|hsc%>"/>        
</div>

<div class="form-group">
    <label>Einleitung:</label>
    <textarea class="form-control" name="PLUGFORM[lead]"><%$WEBSITE.node.tm_plugform.lead|hsc%></textarea>
</div>

<div class="form-group">
    <label>Abschluss Text:</label>
    <textarea class="form-control" name="PLUGFORM[sublead]"><%$WEBSITE.node.tm_plugform.sublead|hsc%></textarea>
</div>


<div class="form-group">
    <label>Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="form-group">
    <label>PDF Template:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplidpdf]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplidpdf==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="form-group">
    <label>Anschreiben:</label>
    <select class="form-control custom-select" name="PLUGFORM[tplidletter]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplidletter==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>


<div class="form-group">
    <label>Absender E-Mail (Mail an Besucher):</label>
    <input type="email" class="form-control" name="PLUGFORM[email]" required="" value="<%$WEBSITE.node.tm_plugform.email|hsc%>"/>        
</div>

<div class="form-group">
    <label>Kopie der E-Mail an:</label>
    <input type="email" class="form-control" name="PLUGFORM[copy_email]" required="" value="<%$WEBSITE.node.tm_plugform.copy_email|hsc%>"/>        
</div>


<div class="form-group">
    <label>Mail-Subject:</label>
    <input type="text" class="form-control" name="PLUGFORM[subject]" value="<%$WEBSITE.node.tm_plugform.subject|hsc%>"/>        
</div>




    
</div>