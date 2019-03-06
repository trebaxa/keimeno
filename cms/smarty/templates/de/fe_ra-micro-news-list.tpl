<h3>RA-Micro News</h3>
<div class="row">
    <% foreach from=$RAMICRONEWS.news item=row name=gloop %>
        <div class="col-md-4">
            <div class="pull-right">
                <small><b><%$row.category%> <%$row.publishingdate%></b></small>
            </div>
            <h3><%$row.title%></h3>
            <% if ($row.subtitle!="") %><h5><%$row.subtitle%></h5><%/if%>            
            <p><%$row.introduction|st%></p>
            <br><i><%$row.reference%></i>
            <a href="javascript:void(0)" title="<%$row.title|sthsc%>" class="btn btn-default js-load-ranews pull-right" data-id="<%$row.id%>" data-title="<%$row.title|sthsc%>">lesen</a>
        </div>    
        <% if ($smarty.foreach.gloop.iteration % 3 == 0  )%></div><hr><div class="row"><%/if%>
    <%/foreach%>
</div>


<!-- Modal -->
<div class="modal fade" id="ramicromodal" role="dialog" aria-labelledby="ramicroLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="ramicroLabel">Modal title</h4>
      </div>
      <div class="modal-body" id="js-ramicronews-content">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Schließen</button>
        <%*<button type="button" class="btn btn-primary">Save changes</button>*%>
      </div>
    </div>
  </div>
</div>

<script>
  $( ".js-load-ranews" ).click(function() {
  simple_load('js-ramicronews-content','<%$PHPSELF%>?page=<%$page%>&cmd=load_news&id='+$(this).data('id'));
  $('#ramicroLabel').html($(this).data('title'));
  $('#ramicromodal').modal('show');
  $('.modal-backdrop').css('z-index', 0);
});
</script>