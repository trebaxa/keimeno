<div class="form-group"> 
    <label>Template</label> 
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="form-group"> 
    <label>Gruppe</label> 
    <select class="form-control custom-select" name="PLUGFORM[groupid]">
        <option value="0">- keine -</option>
        <% foreach from=$WEBSITE.PLUGIN.result.groups item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.groupid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="form-group"> 
    <label>E-Mail Betreff (Aktivierung)</label> 
    <input required="" placeholder="Newsletter Anmeldung - Bitte bestätigen" type="text" name="PLUGFORM[mailsubject]" value="<%$WEBSITE.node.tm_plugform.mailsubject|sthsc%>" class="form-control" />
</div>

<div class="form-group"> 
    <label>E-Mail Text (Aktivierung)</label> 
    <textarea required="" rows="10" name="PLUGFORM[mailtext]" class="form-control"><%$WEBSITE.node.tm_plugform.mailtext|hsc%></textarea>
</div>

<div class="well"><b>Beispiel Text:</b><br><br>
Hallo &lt;%$mail.FORM.vorname%&gt; &lt;%$mail.FORM.nachname%&gt;,<br>
<br>
Sie haben sich soeben an unserem Newsletter auf &lt;%$mail.domain%&gt; angemeldet.<br>
Bitte bestätigen Sie dies über diesen Link: <br>
<br>
&lt;%$mail.link%&gt;</div>

<div class="form-group"> 
    <label>Bestätigungsnachricht Homepage</label> 
    <input required="" placeholder="Sie haben eine Aktivierungsmail erhalten" type="text" name="PLUGFORM[okmsg]" value="<%$WEBSITE.node.tm_plugform.okmsg|sthsc%>" class="form-control" />
</div>
 