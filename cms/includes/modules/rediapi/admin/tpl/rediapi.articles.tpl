<h3>Sucheergebnis...</h3>

<%*$REDIAPI.table|echoarr*%>

<table class="table table-borderd table-hover">
  <thead>
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th class="col-md-1"></th>
    </tr>
  </thead>  
 <tbody>
    <% foreach from=$REDIAPI.table.products item=row %>
        <tr>
            <td><%$row.pname%></td>
            <td>Artikel</td>
            <td><button type="button" data-label="<%$row.pname|sthsc%>" data-id="<%$row.pid%>" class="btn btn-default btn-sm js-onj-click"><i class="fa fa-arrow-right"></i></button></td>
        </tr>
    <%/foreach%>
    <% foreach from=$REDIAPI.table.cats item=row %>
        <tr>
            <td><%$row.name%></td>
            <td>Warengruppe</td>
            <td><button type="button" data-label="<%$row.name|sthsc%>" data-id="<%$row.cid%>" class="btn btn-default btn-sm js-conj-click"><i class="fa fa-arrow-right"></i></button></td>
        </tr>
    <%/foreach%>
    </tbody>
</table>

<script>
$( ".js-onj-click" ).click(function() {
    $('#js-add-redi').append('<input type="hidden" class="js-pid-'+$(this).data('id')+'" name="PLUGFORM[awelements]['+$(this).data('id')+'][id]" value="'+$(this).data('id')+'">'+
    '<input type="hidden" class="js-pid-'+$(this).data('id')+'" name="PLUGFORM[awelements]['+$(this).data('id')+'][label]" value="'+$(this).data('label')+'">'+
    '<input type="hidden" class="js-pid-'+$(this).data('id')+'" name="PLUGFORM[awelements]['+$(this).data('id')+'][type]" value="PRO">'
    );
    $(this).closest('tr').fadeOut();
});

$( ".js-conj-click" ).click(function() {
    $('#js-add-redi').append('<input type="hidden" class="js-cid-'+$(this).data('id')+'" name="PLUGFORM[awelements]['+$(this).data('id')+'][id]" value="'+$(this).data('id')+'">' +
    '<input type="hidden" class="js-cid-'+$(this).data('id')+'" name="PLUGFORM[awelements]['+$(this).data('id')+'][label]" value="'+$(this).data('label')+'">'+
    '<input type="hidden" class="js-pid-'+$(this).data('id')+'" name="PLUGFORM[awelements]['+$(this).data('id')+'][type]" value="CAT">'
    
    );
    $(this).closest('tr').fadeOut();
});
</script>