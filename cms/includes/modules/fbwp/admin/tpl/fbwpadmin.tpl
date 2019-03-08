<div class="page-header"><h1>Facebook iFrame Pages</h1></div>

<div class="tab-content">

<!-- Modal -->
<div class="modal fade" id="fbwp_page_create" tabindex="-1" role="dialog" aria-labelledby="fbwp_page_createLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form class="jsonform" action="<%$PHPSELF%>" method="POST">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="cmd" value="addpage">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="fbwp_page_createLabel">Fanpage anlegen</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>Titel</label>
            <input type="text" class="form-control" name="FORM[fb_title]">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">{LA_SAVE}</button>
      </div>
      </form>  
    </div>
  </div>
</div>    
    
    
    
<% if ($section=='start') %>
    
    <div class="btn-group form-inline mb-lg">
    
     <div class="btn-group">
     <% if (count($FBWP.sites)>0) %>
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
          Seite
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
           <%foreach from=$FBWP.sites item=row %>
                <li <% if ($GET.id==$row.id) %>selected<%/if%>><a href="<%$PHPSELF%>?section=start&epage=<%$epage%>&id=<%$row.id%>"><%$row.fb_title%></a></li>
            <%/foreach%>
        </ul>
     <%/if%>   
      </div>
        
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#fbwp_page_create"><i class="fa fa-plus"></i> Neu</button>  
        <% if ($FBWP.WP.id>0) %>
            <a class="btn btn-default" href="javascript:void(0);" onclick="add_show_box('<%$cms_url%>/includes/modules/fbwp/index.php?id=<%$FBWP.WP.id%>',900,800,1);"><i class="fa fa-eye"></i> Vorschau</a>
            <%$FBWP.WP.delicon%>
        <%/if%>   
    </div>
    
    <script>
        function loadpage(id) {
            window.location.href='<%$PHPSELF%>?section=start&epage=<%$epage%>&id='+id;
        }
    </script>
    
    
<div class="row">
    <div class="col-md-6">
        
        <%include file="cb.panel.header.tpl" title="Template - " title_addon=$FBWP.WP.fb_title%>
        
        <form class="stdform" action="<%$PHPSELF%>" method="POST">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="cmd" value="savewp">
        <input type="hidden" name="id" value="<%$FBWP.WP.id%>">
        
        <textarea name="FORM[fb_content]" class="se-html form-control" style="width:100%;height:600px"><% $FBWP.WP.fb_content|hsc %></textarea>
        <h3>Facebook Anbindung zur App</h3>
            <div class="form-group">
                <label>AppID</label>
                <input type="text" class="form-control" value="<% $FBWP.WP.fb_appid|hsc %>" name="FORM[fb_appid]">
            </div>
            <div class="form-group">
                <label>Secret</label>
                <input type="text" class="form-control" value="<% $FBWP.WP.fb_secret|hsc %>" name="FORM[fb_secret]">
            </div>
      
            <div class="form-group">
                <label>Facebook Group ID</label>
                <input type="text" class="form-control" value="<% $FBWP.WP.fb_groupid|hsc %>" name="FORM[fb_groupid]">
            </div> <%*  
            <div class="form-group">
                <label>Token</label>
                <textarea disabled="true" name="FORM[fb_token]" class="form-control"><% $FBWP.WP.fb_token|hsc %></textarea>
            </div>    *%>            
        
        
        <%$subbtn%>
        </form>
        <%include file="cb.panel.footer.tpl"%>
    </div>
    
    <div class="col-md-6">
    <%include file="cb.panel.header.tpl" class="panel-featured-primary" title="Facebook Settings"%>
    

    <% if ($FBWP.WP.fb_token=="") %>
        <div class="alert alert-warning">
            Es wurde noch kein Token angefordert.
        </div>
    <%/if%>    
            
            <div class="btn-group">
                <a class="btn btn-default" href="https://www.facebook.com/dialog/pagetab?app_id=<%$FBWP.WP.fb_appid%>&redirect_uri=<%$THISURL|uen%>"><i class="fa fa-plus"></i> App auf Fanpage hinzuf&uuml;gen</a>
                <%*<a class="btn btn-default" href="javascript:void(0)" onclick="dc_show('searchpro',900);" >Produkte hinzuf&uuml;gen</a>*%>
                <% if ($FBWP.WP.fb_groupid!="") %><a class="btn btn-default" href="javascript:void(0)" onclick="simple_load('js-fbgroup','<%$PHPSELF%>?epage=<%$epage%>&cmd=update_group_stream&id=<%$FBWP.WP.id%>');" ><i class="fa fa-refresh"></i> Sync Gruppe</a><%/if%>
                <a class="btn btn-default" href="<%$FBWP.loginUrl %>"><i class="fa fa-download"></i> Get Facebook Token</a>
                <% if ($FBWP.WP.fb_token!="")%>
                    <a class="btn btn-default ajax-link" data-target="js-tokentarget" href="<%$eurl %>cmd=getpermatoken&fbwpid=<%$FBWP.WP.id%>"><i class="fa fa-refresh"></i> convert to permanent token</a>
                <%/if%> 
            </div>
    <% if ($FBWP.WP.fb_token=="") %>
        <div class="alert alert-warning">Token muss angefordert werden.</div>
    <%/if%>    
    <div id="js-tokentarget"></div>        
    <div class="alert alert-info mt-lg">    
        <p>URL der Welcome-Page ("Secure Canvas URL"): <%$SSLSERVER%>includes/modules/fbwp/index.php?id=<%$FBWP.WP.id%></p>
        <p>Facebook erlaubt ausschließlich SSL Links.</p>
        <p>App Domain: <%$domain%></p>
        <p>Redirect URL: <%$FBWP.redirect_url|hsc %></p>
        <p>Die Zugangsdaten der ersten Seiten (ID=1) werden im Socialstream verwendet, um die Fanpage zu laden. Die Fanpage ID wird in der "Konfiguration" festgelegt.</p>
        <p>Access Token Validator: <a href="https://developers.facebook.com/tools/debug/accesstoken?q=<%$FBWP.WP.fb_token%>&version=v2.10" target="_blank">https://developers.facebook.com/tools/debug/accesstoken</a></p>
        <p><a href="https://www.facebook.com/<% $gbl_config.fb_fanpagename %>/insights/?section=navAPI" target="_blank">check you current rate</a></p>
        <p><a href="https://developers.facebook.com/apps/<%$FBWP.WP.fb_appid%>/dashboard/" target="_blank">App dashboard</a></p>
        <p><a href="https://developers.facebook.com/docs/apps/review/server-to-server-apps/" target="_blank">Server-to-Server App Review</a></p>
        <p><a href="https://developers.facebook.com/tools/accesstoken/" target="_blank">Deine Access Token</a></p>
            
    </div>
    
    <div id="js-fbgroup"></div>
    <%include file="cb.panel.footer.tpl"%>
    </div>
  </div>  
    
    <%/if%>
    
    <% if ($section=='modstylefiles') %>
     <% include file="modstylefiles.tpl"%>
    <%/if%>
    
    <% if ($section=='conf') %>
     <% $FBWP.CONFTAB %>
    <%/if%>
</div>    