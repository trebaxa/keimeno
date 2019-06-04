<div class="form-group">
    <label>Datenbank</label>
    <div class="input-group">
        <select name="FORM[table]" class="form-control js-db-change">
                <% foreach from=$RESOURCE.tables item=row%>
                    <option <% if ($row.selected==true) %>selected<%/if%> value="<%$row.f_table%>"><%$row.f_name%></option>
                <%/foreach%>    
        </select>
        <div class="input-group-btn"><button class="btn btn-primary" onclick="new_table()" type="button"><i class="fa fa-plus"></i></button></div>
    </div>
</div>


<%include file="cb.panel.header.tpl" title="Datensatz Variablen" class="panel-featured-primary"%>

    
<div class="row"> 
    <div class="col-md-12">
        <div id="js-datasetvars">
            <%include file="resource.datasetvars.table.tpl"%>
        </div>
    </div>
</div>


<%include file="cb.panel.footer.tpl"%>



<script>
    $( ".js-db-change" ).change(function() {
      reload_dataset_vars($(this).val());
    });

    function reload_dataset_vars(table) {
        simple_load('js-datasetvars','<%$eurl%>cmd=reload_dataset_vars&id=<%$GET.id%>&table='+table);
    }
    
    function set_resrc_table(table,label) {
        $(".js-db-change").append(new Option(label, table));
        $( ".js-db-change" ).val(table);
        $( ".js-db-change" ).trigger('change');
    }
    
    function new_table() {
        var table = prompt("Datenbank Name", "DB Name");
        if (table != null) {
            var url = '<%$eurl%>cmd=add_new_table&table='+table+'&id=<%$GET.id%>';
            jsonexec(url,true);
        }    
    }
    
    function del_table(table) {
        var r = confirm("Sicher?");
        if (r == true) {
            var url = '<%$eurl%>cmd=del_table&table='+table+'&id=<%$GET.id%>';
            jsonexec(url,true);
            $(".js-db-change option[value='"+table+"']").remove();
            $( ".js-db-change" ).val('<%$RESOURCE.flextpl.f_table%>');            
         }   
    }
</script>