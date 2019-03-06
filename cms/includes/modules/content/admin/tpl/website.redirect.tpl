<!-- TAB Weiterleitung -->
<%include file="cb.panel.header.tpl" title="Weiterleitung"%>
        
        <form class="jsonform form-inline" method="post" action="<%$PHPSELF%>" role="form">
            <input type="hidden" name="tid" value="<% $GET.id %>">
            <input type="hidden" name="cmd" value="save_redirect">
            <input type="hidden" name="tabid" value="6">
            <input type="hidden" name="epage" value="<% $epage %>"> 
            
            <% if ($TPLOBJ.admin==0) %>
    
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="redirect">{LBL_ONLYURL_REDIRECT}</label>
                            <input id="redirect" type="text" class="form-control" name="FORM_TEMPLATE[url_redirect]" size="30" value="<% $TPLOBJ.url_redirect|hsc%>">
                        </div>
                    </div>
                    <div class="col-md-2 redirect-center">
                        <i class="fa fa-arrow-circle-right fa-2x"><!----></i>
                    </div><!-- /.col-md-2 .redirect-center -->
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="target">{LBL_ONLYURL_TARGET}</label>
                            <input id="target"type="text" class="form-control" name="FORM_TEMPLATE[url_redirect_target]" size="30" onclick="if (this.value=='') this.value='_self'" value="<% $TPLOBJ.url_redirect_target|hsc%>">
                        </div>
                    </div>
                </div>
    
            <%/if%>
    
            <div class="form-feet"><% $subbtn %></div>
        </form>
    <%include file="cb.panel.footer.tpl"%>