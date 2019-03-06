<% if ($title=="") %>
    <% assign var=title value="Drag & Drop Dateien hier" %>
<%/if%>
  <div class="dropzonecss" id="drop-zone-files-<%$ident%>">
            <%$title%>
        </div>
        <div id="dropzonefeedback-<%$ident%>"></div>
        

        <script>
        $(document).ready(function() {
            var notice_dropdown = new Dropzone("#drop-zone-files-<%$ident%>", {
              paramName: "<%$paramName%>",
              clickable: true,
              <% if ($maxFiles!="") %>maxFiles: <%$maxFiles%>,
              <%/if%>
              acceptedFiles: "<%$acceptedFiles%>",
              url:"<%$eurl%>cmd=<%$cmd%>&<%$addon%>",
              maxFilesize: 5
            });
            notice_dropdown.on("success", function(file,responseText) {
                notice_dropdown.removeFile(file);
                var result = jQuery.parseJSON(responseText);
                if (result.status=='failed') {
                    $('#dropzonefeedback-<%$ident%>').append('<p class="text-danger"><i class="fa fa-times"></i> '+result.filename+'</p>');
                } else {
                    $('#dropzonefeedback-<%$ident%>').append('<p class="text-success"><i class="fa fa-check-circle-o"></i> '+result.filename+'</p>');
                }
            });
            notice_dropdown.on("drop", function() {
                 $('#drop-zone-files-<%$ident%>').html('');
                 $('#dropzonefeedback-<%$ident%>').show();
            });
            notice_dropdown.on("queuecomplete", function() {
                 $('#drop-zone-files-<%$ident%>').html('<%$title%>');
                 $('#dropzonefeedback-<%$ident%>').fadeOut();
                 <%$reloadFunction%>
            });
        });

        </script>