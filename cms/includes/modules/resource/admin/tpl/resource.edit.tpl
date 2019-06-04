<h3><%$RESOURCE.flextpl.f_name%></h3>

<div class="tc-tabs-box" id="tplvartabs">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a data-ident="#tab-flexvar" class="tc-link" href="javascript:void(0);" onclick="reload_flexvars_vars();">Inhalt Felder</a></li>
        <li><a data-ident="#tab-dataset" class="tc-link" href="javascript:void(0);" onclick="reload_dataset_vars('<%$RESOURCE.flextpl.f_table%>');">Datenbanken</a></li>
        <li><a data-ident="#tab-htmltpl" class="tc-link" onclick="reload_htmltpl();" href="javascript:void(0);">HTML Vorlage</a></li>
    </ul>
</div>

<div class="tabs">
    <div id="tab-flexvar" class="tabvisi" style="display:block">
        <%include file="resource.flexvars.tpl"%>
    </div>
    <div id="tab-dataset" class="tabvisi">
        <%include file="resource.datavars.tpl"%>
    </div>    
   <div id="tab-htmltpl" class="tabvisi">
        <%include file="resource.htmltpl.tpl"%>
    </div>    
</div>   

<script>
<% if ($GET.settab!="")%>
    tab_visi_by_ident('tplvartabs','<%$GET.settab%>');
<%/if%> 
    function reload_htmltpl() {
        $('#new-flex-tpl-html').modal('hide');
        simple_load('js-tpledit','<%$eurl%>cmd=reload_htmltpl&id=<%$GET.id%>');
    }
    reload_flexvars_vars();
</script>