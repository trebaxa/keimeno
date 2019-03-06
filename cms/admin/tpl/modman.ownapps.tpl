<% if ($GET.showuploaddone==1) %>
    <div class="bg-success">
    App "<%$MODMAN.modul.module_name%>" wurde erfolgreich an das Keimeno Team gesendet. Sie werden informiert, wenn Ihre App aufgenommen worden ist.
    </div>
<%/if%>
<h3>Installierte Apps</h3>
<% if count($MODMAN.ownapps)>0 %>


<!-- Modal -->
<div class="modal fade" id="js-unimod" tabindex="-1" role="dialog" aria-labelledby="js-unimodLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="js-unimodLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        <h3>Soll die App wirklich deinstalliert werden?</h3>
      </div>
      <div class="modal-footer">
        <button data-ident="" class="btn btn-default uninstallbtn">Ja</button>
        <button class="btn btn-danger">Abbruch</button>
      </div>
    </div>
  </div>
</div>



<form action="<%$PHPSELF%>" method="post" class="stdform form-inline">
<table class="table table-striped table-hover">
    <thead><tr>
     <th>App</th>
     <th>Version</th>
     <th></th>
     <th></th>
     <th></th>     
    </tr></thead> 
<% foreach from=$MODMAN.ownapps item=row %>
   <% if ($row.settings.iscore!='true') %>
    <tr id="mod-<%$row.settings.id%>" >
        <td><% $row.settings.module_name %></td>
        <td><% $row.settings.version %></td>
        <td><i class="fa fa-circle <% if ($row.settings.active=='true') %>fa-green<%else%>fa-red<%/if%>">&nbsp;</i></td>        
        <td><%$row.settings.description%></td>
        <td><a href="javascript:void(0);" onclick="simple_load('apppool','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_packer&modident=<% $row.settings.id %>');">einreichen</a></td>
    </tr>
    <%/if%>        
<%/foreach%>
</table>
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="cmd" value="activate_mod">
<%$subbtn%>
</form>

<script>
$( ".modactive" ).click(function() {
    if ($(this).data('active')==true) {
        $(this).data('active',false);    
        $(this).parent().prev().find("i").removeClass("fa-green").addClass('fa-red');
    } else {
        $(this).data('active',true);
        $(this).parent().prev().find("i").removeClass("fa-red").addClass('fa-green');
    }
});
</script>
<%/if%>

<script>
$( ".moduninst" ).click(function( event ) {
    event.preventDefault();
    $('#js-unimod').modal('show');
    $('.uninstallbtn').attr('data-ident',$(this).data('ident'));
});

$( ".redbutton" ).click(function( event ) {
    event.preventDefault();
    $('#js-unimod').modal('hide');
});



$( ".uninstallbtn" ).click(function( event ) {
    event.preventDefault();
    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=uninstall_mod&mod_id='+$(this).data('ident'));
    $('#mod-'+$(this).data('ident')).fadeTo(400, 0, function () { 
        $(this).remove();
    });
    $('#js-unimod').modal('hide');
});
</script>