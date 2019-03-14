<div class="row">
      <div class="col-md-6">
        <%include file="resource.htmltpl.edit.tpl"%>
      </div>
      <div class="col-md-6">          
           <div id="js-htmledit-help">     
           </div>       
      </div>      
</div>     

<script>
    simple_load('js-htmledit-help','<%$eurl%>cmd=reload_html_help&flxid=<%$GET.flxid%>&id=<%$GET.id%>&gid=0');
</script>   