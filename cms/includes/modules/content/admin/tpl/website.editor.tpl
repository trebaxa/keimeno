<% include file="cb.panel.header.tpl" icon="fa-file-o" title=$TPLOBJ.description title_addon="Page [`$GET.id`]" %>
   


<% if ($GET.id>1) %>
    <div id="fix_box">
        <ul>
            <%foreach from=$TPLOBJ.icons key=iconkey item=picon name=cicons %>
                <%if ($iconkey!='edit') %>
                    <li><% $picon %></li>
                <%/if%>
            <%/foreach%>
        </ul>
    </div>
<%/if%>
    
    
    <div class="tc-tabs-box" id="webmtabs">
        <ul class="nav nav-tabs bar_tabs" role="tablist">
            <li class="active"><a data-ident="#tab1" class="tc-link active" href="javascript:void(0);">Inhalt</a></li>
            <% if ($GET.id>1) %>
            <li><a data-ident="#tab3" class="tc-link" href="javascript:void(0);">Theme Title</a></li>
            <% if ($PERM.core_acc_settings_pageditor==true) %>
                <li><a data-ident="#tab2" class="tc-link" href="javascript:void(0);">Settings</a></li>
            <%/if%>    
            <li><a data-ident="#tab5" class="tc-link" href="javascript:void(0);">Meta</a></li>
            <%if ($TPLOBJ.admin==0) %><li><a data-ident="#tab6" class="tc-link" href="javascript:void(0);">{LA_REDIRECT}</a></li><%/if%>
                <li><a data-ident="#tab7" class="tc-link" href="javascript:void(0);" data-function="seo_click">SEO</a></li>
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