<% include file="website.modal.tpl" %>
<div id="webpagemanager">
    <%if ($cmd=='' || $cmd=='load_pages') %>        
        <% include file="website.sitetable.tpl" %>
    <%/if%>   

    <%if ($cmd=='edit' || $cmd=='page_axedit') %>
        <% include file="website.editor.tpl" %>
    <%/if%>
    
</div><!-- /#webpagemanager -->