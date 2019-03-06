<h3>Vorschau</h3>


<form method="post" action="<%$PHPSELF%>" class="jsonform" >
  <div class="row">
    <div class="col-md-6">
     <% if (count($DOCSEND.files)>0) %>
              <% include file="cb.panel.header.tpl" title="Zu versendende Dateien"%>
              <table class="table table-hover table-striped">
                <thead>
                  <tr>
                    <th>Datei</th>
                    <th class="col-md-1">Größe</th>
                    <th class="col-md-2">Datum</th>
                  </tr>
                </thead>
                <tbody>
              <% foreach from=$DOCSEND.files item=row %>
                  <tr>          
                      <td><a title="Download <%$row.file%>" href="<%$eurl%>cmd=ds_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>"><%$row.file%></a></td>
                      <td><%$row.size%></td>
                      <td><%$row.date%>
                       <input type="hidden" name="FILEIDS[]" value="<%$row.hash%>" />
                      </td>         
                  </tr>
              <%/foreach%>
              </tbody>
              </table>
               <% include file="cb.panel.footer.tpl"%>
              <%/if%>
    </div>
    <div class="col-md-6">
    <% include file="cb.panel.header.tpl" title="Kunde"%>
        <%$DOCSEND.customer.firma%><br>
        <%$DOCSEND.customer.vorname%> <%$DOCSEND.customer.nachname%><br>
        <%$DOCSEND.customer.strasse%> <%$DOCSEND.customer.hausnr%><br>
        <%$DOCSEND.customer.plz%> <%$DOCSEND.customer.ort%><br>
        <%$DOCSEND.customer.email%>
         <% include file="cb.panel.footer.tpl"%> 
    </div>
  
  </div>
  
  
<% include file="cb.panel.header.tpl" title="E-Mail"%>
   
        <input type="hidden" name="cmd" value="send_mail" /> 
        <input type="hidden" name="epage" value="<%$epage%>" />
        <input type="hidden" name="mailid" value="<%$POST.FORM.mailid%>" />
        <input type="hidden" name="kid" value="<%$POST.kid%>" />
  <div class="form-group">
    <label>Betreff:</label>
    <input type="test" class="form-control" required="" name="FORM[subject]" value="<%$DOCSEND.mail.subject|sthsc%>" />
  </div>
  
  <div class="form-group">
    <label>Text:</label>
    <textarea required="" name="FORM[content]" class="se-html"><%$DOCSEND.mail.content|sthsc%></textarea>
  </div>
  <% if ($POST.kid>0 && count($DOCSEND.files)>0) %>
  <div class="text-right">
            <button type="submit" class="btn btn-primary"><i class="fa fa-envelope"></i> Senden</button>
        </div>
   <%else%>
    <div class="alert alert-danger">Bitte Kunde bestimmen und/oder Dateien auswählen.</div>
   <%/if%>    
   <% include file="cb.panel.footer.tpl"%> 
</form>

