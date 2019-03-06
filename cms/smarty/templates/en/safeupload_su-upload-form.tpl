<% if ($customer.kid>0) %>
<script src="/includes/modules/safeupload/js/dropzone/dropzone.js"></script>
<link rel="stylesheet" href="/includes/modules/safeupload/js/dropzone/dropzone.css">
    <% if ($cmd=="") %>
    <section>
      <div class="container">
        <h3>Dateien hochladen</h3>
        
        <div class="dropzonecss" id="js-customer-dropzone" data-cont_matrix_id="<%$cont_matrix_id%>">
            Drag & Drop Dateien hier
        </div>
        <div id="dropzonefeedback"></div>
        <div id="js-customer-files"></div>

        <small>Upload für <%$customer.vorname%> <%$customer.nachname%>, KNR: <%$customer.kid%> | Maximale Datei Größe: <%$SAFEUPLOAD.upload_max_filesize%> |  Maximale Datei Post Größe: <%$SAFEUPLOAD.post_max_size%></small>
        
       <div id="js-su-files"></div>
      </div>
    </section>
    <%/if%>
    
    <% if ($cmd=="reload_customer_files") %>
     
      <% if (count($SAFEUPLOAD.files)>0) %>
      <h3>Ihre Dateien</h3>
      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th>Datei</th>
            <th>Größe</th>
            <th>Datum</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
      <% foreach from=$SAFEUPLOAD.files item=row %>
          <tr>
              <td><a title="Download <%$row.file%>" href="<%$eurl%>cmd=user_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>"><%$row.file%></a></td>
              <td><%$row.size%></td>
              <td><%$row.date%></td>
              <td class="text-right"><a title="Download <%$row.file%>" href="<%$eurl%>cmd=user_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>" class="btn btn-default btn-sm"><i class="fa fa-download"></i></a></td>
          </tr>
      <%/foreach%>
      </tbody>
      </table>
      <%/if%>
    <%/if%>



<%else%>
  <div class="alert alert-info">Bitte melden Sie sich an.</div>
<%/if%>