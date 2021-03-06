<%include file="cb.panel.header.tpl" title="Datensatz Variablen" class="panel-featured-primary"%>
    <div class="btn-group">
        <a class="btn btn-secondary" href="#" onclick="reload_dataset_vars();"><i class="fa fa-table"></i> Alle anzeigen</a>
        <button class="btn btn-secondary" type="button" onclick="add_show_box_tpl('<%$eurl%>cmd=show_flxvar_editor&v_con=0&varid=0&flxid=<%$GET.id%>','Variable Editor')"><i class="fa fa-plus"></i> Neue Datensatz Variable</button>
    </div>
    
<div class="row">    
    <div class="col-md-2">
        <div class="list-group">
          <a class="list-group-item list-group-item-action" href="javascript:void(0)" onclick="simple_load('js-datasetvars','<%$eurl%>cmd=reload_dataset_vars&id=<%$GET.id%>&gid=0')">- alle -</a>
          <% foreach from=$FLEXTEMP.flextpl.groups item=group %>
           <a class="list-group-item list-group-item-action" href="javascript:void(0)" onclick="simple_load('js-datasetvars','<%$eurl%>cmd=reload_dataset_vars&id=<%$GET.id%>&gid=<%$group.id%>')"><%$group.g_name%></a>
          <%/foreach%> 
        </div>    
    </div>
    <div class="col-md-10">
        <div id="js-datasetvars">
            <%include file="flxtpl.datasetvars.table.tpl"%>
        </div>
    </div>
</div>


<%include file="cb.panel.footer.tpl"%>



<script>
    function reload_dataset_vars() {
        simple_load('js-datasetvars','<%$eurl%>cmd=reload_dataset_vars&id=<%$GET.id%>');
    }

    function reload_dataset_vars_by_gid(gid) {
        simple_load('js-datasetvars','<%$eurl%>cmd=reload_dataset_vars&id=<%$GET.id%>&gid='+gid);
    }    
   
</script>