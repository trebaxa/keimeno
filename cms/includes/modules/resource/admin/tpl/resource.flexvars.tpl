<%include file="cb.panel.header.tpl" title="Inhalt Felder" class="panel-featured-primary"%>
    <div class="btn-group">
        <a class="btn btn-secondary" href="#" onclick="reload_flexvars_vars();"><i class="fa fa-table"></i> Felder anzeigen</a>        
        <button class="btn btn-secondary" type="button" onclick="add_show_box_tpl('<%$eurl%>cmd=show_flxvar_editor&v_con=1&varid=0&flxid=<%$GET.id%>','Variable Editor')"><i class="fa fa-plus"></i> Neu</button>
    </div>
    
<div class="row">    
    <%*<div class="col-md-2">
        <ul class="nav nav-pills nav-stacked">
          <li><a href="javascript:void(0)" onclick="simple_load('js-flexvars','<%$eurl%>cmd=reload_flexvars_vars&id=<%$GET.id%>&gid=0')">- alle -</a></li>
          <% foreach from=$RESOURCE.flextpl.groups item=group %>
           <li><a href="javascript:void(0)" onclick="simple_load('js-flexvars','<%$eurl%>cmd=reload_flexvars_vars&id=<%$GET.id%>&gid=<%$group.id%>')"><%$group.g_name%></a></li>
          <%/foreach%> 
        </ul>    
    </div>
    *%>
    <div class="col-md-12">
            <div id="js-flexvars">
                <%include file="resource.flexvars.table.tpl"%>
            </div>
    </div>
</div>
<%include file="cb.panel.footer.tpl"%>

<script>
    function reload_flexvars_vars() {
        simple_load('js-flexvars','<%$eurl%>cmd=reload_flexvars_vars&id=<%$GET.id%>');
    }
   
</script>