<div class="row">
  <div class="col-12">
    <div class="page-header">
      <h2><%$TPLOBJ.description%></h2>
      <div class="quick-menu">
        <% if ($GET.id>1) %>
        <div class="btn-group">
            <a class="btn btn-secondary" href="javascript:void(0);" data-toggle="modal" data-target="#addpage" title="{LBL_NEW}"><i class="fa fa-plus"></i></a>
            <a class="btn btn-danger" onclick="return confirm('Sind Sie sicher?')" href="/admin/run.php?epage=websitemanager.inc&&tarttree=0&cmd=a_delete&aktion=a_delete&id=<%$GET.id%>"><i class="fa fa-trash"></i></a>
            <a class="btn btn-secondary" target="_blank" href="../index.php?page=<%$GET.id%>&preview=1"><i class="far fa-eye"></i></a>
            <a class="btn btn-secondary axapprove" href="javascript:void(0);" title="{LBLA_APPROVED}" id="axapprove-<%$GET.id%>" data-ident="<%$GET.id%>" data-value="<% if ($TPLOBJ.approval==1) %>0<%else%>1<%/if%>" data-cmd="axapprove_item" data-toadd="" data-epage="<%$epage%>" data-phpself="<%$PHPSELF%>"><i class="fa fa-circle <% if ($TPLOBJ.approval==0) %>fa-red<%else%>fa-green<%/if%>"><!----></i></a>
        </div>
        <%/if%>
      </div>
    </div>
  </div>
</div>
<% include file="website.modal.tpl" %>
<% include file="cb.panel.header.tpl" icon="far fa-file-alt" title=$TPLOBJ.description title_addon="Page [`$GET.id`]" %>
    <div class="tc-tabs-box" id="webmtabs">
        <ul class="nav" role="tablist">
            <li class="nav-item active"><a data-ident="#tab1" class="tc-link active" href="javascript:void(0);">Inhalt</a></li>
            <% if ($GET.id>1) %>
            <li class="nav-item"><a data-ident="#tab3" class="tc-link" href="javascript:void(0);">Theme Title</a></li>
            <% if ($PERM.core_acc_settings_pageditor==true) %>
                <li class="nav-item"><a data-ident="#tab2" class="tc-link" href="javascript:void(0);">Settings</a></li>
            <%/if%>
            <li class="nav-item"><a data-ident="#tab5" class="tc-link" href="javascript:void(0);">Meta</a></li>
            <%if ($TPLOBJ.admin==0) %><li><a data-ident="#tab6" class="tc-link" href="javascript:void(0);">{LA_REDIRECT}</a></li><%/if%>
                <li class="nav-item"><a data-ident="#tab7" class="tc-link" href="javascript:void(0);" data-function="seo_click">SEO</a></li>
            <%/if%>
        </ul>
    </div>


<% if ($GBLPAGE.access.language==TRUE)%>
    <div class="tabs tab-content">
        <div id="tab1" class="tabvisi">
            <%include file="website.edit.tpl" %>
        </div>
        <% if ($PERM.core_acc_settings_pageditor==true) %>
            <div id="tab2" class="tabvisi">
                <%include file="website.settings.tpl" %>
            </div>
        <%/if%>
        <div id="tab3" class="tabvisi">
            <%include file="website.theme.tpl" %>
        </div>
        <div id="tab5" class="tabvisi">
            <%include file="website.meta.tpl" %>
        </div>
        <div id="tab6" class="tabvisi">
            <%include file="website.redirect.tpl" %>
        </div>
        <div id="tab7" class="tabvisi">
            <%include file="website.seo.tpl" %>
        </div>
    </div><!-- /.tabs -->


<%else %>
    <%include file="no_permissions.admin.tpl" %>
<%/if%>

<% include file="cb.panel.footer.tpl"%>

<script>
  <% if ($GET.tabid>0) %>
    tab_visi('webmtabs','<%$GET.tabid%>');
  <%/if%>

function importtpl(tid) {
    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=importtpl&id='+tid);
    simple_load('tplcontent','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_cont_table&id=<% $TPLOBJ.formcontent.id %>');
}
function seo_click() {
    tab_visi('webmtabs','7');
    startseo();
}
window.setTimeout("tab_visi('webmtabs',1);",100);
</script>
