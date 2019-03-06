<h3><%$FLEXTEMP.flextpl.f_name%></h3>

<div class="tc-tabs-box" id="tplvartabs">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a data-ident="#tab-dataset" class="tc-link" href="javascript:void(0);" onclick="reload_dataset_vars();">Datensatz Variablen</a></li>
        <li><a data-ident="#tab-flexvar" class="tc-link" href="javascript:void(0);" onclick="reload_flexvars_vars();">Template Variablen</a></li>
        <li><a data-ident="#tab-htmltpl" class="tc-link" onclick="reload_htmltpl();" href="javascript:void(0);">HTML Vorlage</a></li>
        <li><a data-ident="#tab-group" onclick="$('#js-change-flex-htmlfilter-group').change()" class="tc-link" href="javascript:void(0);">Gruppen</a></li>
    </ul>
</div>

<div class="tabs">
    <div id="tab-dataset" class="tabvisi" style="display:block">
        <%include file="flxtpl.datavars.tpl"%>
    </div>
    <div id="tab-flexvar" class="tabvisi">
        <%include file="flxtpl.flexvars.tpl"%>
    </div>
    <div id="tab-htmltpl" class="tabvisi">
        <%include file="flxtpl.htmltpl.tpl"%>
    </div>    
    <div id="tab-group" class="tabvisi">
        <%include file="flxtpl.group.tpl"%>
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
</script>