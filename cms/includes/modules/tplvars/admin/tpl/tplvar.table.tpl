<% if (count($TPLVARS.vars)>0) %>
<h3>Angelegte Variablen</h3>
<table class="table table-striped table-hover">
<thead>
    <tr>
        <th>Variable</th>
        <th>Type</th>
        <th>Description</th>
        <th></th>
    </tr>
</thead>  
<% foreach from=$TPLVARS.vars item=row %>
<tr>
    <td><%$row.var_name%></td>
    <td><%$row.var_type%></td>
    <td><%$row.var_desc%></td>
    <td class="text-right">
    <a class="btn btn-default tplvar-var-edit-icon" data-id="<%$row.id%>" data-type="<%$row.var_type%>" title="{LBLA_EDIT}" href="javascript:void(0)"><i class="fa fa-pencil-square-o"></i></a>
        <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%>
    </td>
</tr>
<%/foreach%>
</table>
<script>
var firstkey="";
    $( ".tplvar-var-edit-icon" ).click(function() {
        $('#var-type').val($(this).data('type'));
        $('#var-type').trigger('change');
        $.getJSON('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_single_var&id='+$(this).data('id'), function(data) {
            $.each( data.FORM, function( key, val ) {            
                if ($('#tvar-'+key).length>0) {                
                    $('#tvar-'+key).val(val);
                }
            }); 
            $.each( data.VAROPT, function( key, val ) {            
               if ($.isPlainObject(val)) {
                firstkey=key;
                $.each( val, function( seckey, secval ) {
                    if ($('#tvaropt-'+firstkey+'-'+seckey).length>0) {                
                        $('#tvaropt-'+firstkey+'-'+seckey).val(secval);
                    }
                });
               
               
               } else {
                if ($('#tvaropt-'+key).length>0) {                
                    $('#tvaropt-'+key).val(val);
                }
               }
            });             
        });        
    });

    
</script>
<%/if%>