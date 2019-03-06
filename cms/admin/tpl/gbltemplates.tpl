<% if ($cmd=='load_start') %>
    <% include file="gbltemplates.start.tpl" %>
<%/if%>

<%if ($cmd=='edit' || $cmd=='load_gbltpl_ax') %>
   <div id="js-gbltpl-editor"><% include file="gbltemplate.editor.tpl" %></div>
<%/if%>    

<script>
        function clear_gbltpl_form() {
            $('#js-gbltpl-editor').remove();
        }
</script> 