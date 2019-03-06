<div class="plugin">

    <div class="form-group">
        <label>Template:</label>
        <select class="form-control" name="PLUGFORM[tplid]">
            <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
                <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
            <%/foreach%>
        </select>
    </div>
    
    <div class="form-group">
        <label>Dokument wird an folgende E-Mail gesendet:</label>
        <input class="form-control" type="email" name="PLUGFORM[email]" value="<%$WEBSITE.node.tm_plugform.email|sthsc%>" placeholder="@" />
    </div>    
    
    <div class="checkbox">
        <label>
            <input <% if ($WEBSITE.node.tm_plugform.send_mail==1) %>checked<%/if%> type="checkbox" name="PLUGFORM[send_mail]" value="1"/>
            Bei Upload E-Mail an obige E-Mail versenden
        </label>
    </div>
    
    <div class="checkbox">
        <label>
            <input <% if ($WEBSITE.node.tm_plugform.send_mail_attach==1) %>checked<%/if%> type="checkbox" name="PLUGFORM[send_mail_attach]" value="1"/>
            Bei Upload E-Mail mit Anhang versehen
        </label>
    </div>
    
    <div class="checkbox">
        <label>
            <input <% if ($WEBSITE.node.tm_plugform.send_download_mail==1) %>checked<%/if%> type="checkbox" name="PLUGFORM[send_download_mail]" value="1"/>
            E-Mail Benachrichtigung, wenn Kunden Datei herunterlädt
        </label>
    </div>
    
    <div class="well">
       Maximale Datei Größe: <%$SAFEUPLOAD.upload_max_filesize%><br>
       Maximale Datei Server Übertragungs-Größe: <%$SAFEUPLOAD.post_max_size%><br>
    </div>
    
</div>