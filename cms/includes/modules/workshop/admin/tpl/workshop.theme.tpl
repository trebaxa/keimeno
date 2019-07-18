<% if ($WORKSHOP.ws.ws_theme!="") %>
    <img id="js-theme-image" src="../file_data/workshop/<%$WORKSHOP.ws.ws_theme%>" class="img-fluid" />
    <br><button class="btn btn-secondary" type="button" onclick="ws_delete_themeimg(<%$GET.id%>);"><i class="fa fa-trash"></i></button>
    <script>
        function ws_delete_themeimg(id) {
            execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=ws_delete_theme_image&id='+id);
            $('#js-theme-image').remove();
        }
    </script>
<%/if%>