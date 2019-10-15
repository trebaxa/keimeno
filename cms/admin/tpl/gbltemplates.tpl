<% if ($cmd=='load_start') %>
    <% include file="gbltemplates.start.tpl" %>
<%/if%>

<%if ($cmd=='edit' || $cmd=='load_gbltpl_ax') %>
   <div id="js-gbltpl-editor"><% include file="gbltemplate.editor.tpl" %></div>
<%/if%>    

<script>
    function clear_gbltpl_form(tid) {
        $('#js-gbltpl-editor').remove();
        $('#gbltpltreeul').jstree(true).delete_node('gbltreenode-'+tid);
    }
    
    
</script> 