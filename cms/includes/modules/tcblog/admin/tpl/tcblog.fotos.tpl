<% if (count($TCBLOG.fotos)>0) %>
<div class="row">
<% foreach from=$TCBLOG.fotos item=row %>
   <div class="col-md-2">
   <i class="fa fa-trash fotodel" data-fotoid="<%$row.id%>"></i> 
    <img src="<%$row.thumb%>?a=<%1|rand:1000%>" class="img-thumbnail">
   </div>
<%/foreach%>
</div>
<script>
$( ".fotodel" ).unbind('click');
$( ".fotodel" ).click(function() {

    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=delfoto&fotoid='+$(this).data('fotoid')+'&id=<%$GET.id%>');
    $(this).parent().fadeOut();
});
</script>
<%else%>
<div class="alert alert-info">Noch keine Fotos hochgeladen.</div>
<%/if%>