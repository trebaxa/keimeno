<% if ($TPLOBJ.formcontent.id>0) %>

<%include file="cb.panel.header.tpl" title="Meta-Beschreibungen"%>
        
            <form class="form jsonform" method="post" action="<%$PHPSELF%>">
            
                <input type="hidden" name="tid" value="<% $GET.id %>">
                <input type="hidden" name="tl" value="<% $GET.tl %>">
                <input type="hidden" name="cmd" value="save_meta">
                <input type="hidden" name="tabid" value="5">
                <input type="hidden" name="uselang" value="<% $GET.uselang%>">
                <input type="hidden" name="FORM[lang_id]" value="<% $GET.uselang %>">
                <input type="hidden" name="FORM[tid]" value="<% $GET.id %>">
                <input type="hidden" name="id" value="<% $TPLOBJ.formcontent.id %>">
                <input type="hidden" name="epage" value="<% $epage %>">
                <input type="hidden" name="tmsid" value="<% $GET.tmsid %>">
                
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-5">
        
                            <div class="form-group">
                                <label for="mtitle">Meta-Title</label>
                                <div class="input-group">
                                    <input id="mtitle" type="text" class="form-control" value="<% $TPLOBJ.formcontent.meta_title|hsc %>" name="FORM[meta_title]">
                                    <span class="input-group-btn"><button class="btn btn-secondary" type="button" onClick="GetRequest2Value('mtitle','genmetatitle','run','&uselang=<%$GET.uselang%>&epage=<%$epage%>&id=<% $TPLOBJ.id%>&conid=<% $TPLOBJ.formcontent.id %>'); return false;"><i class="fa fa-refresh"></i> generieren</button></span>
                                </div><!-- /.input-group -->
                                <span class="help-block">Der Meta-Titel wird im Browserfester dargestellt.</span>
                            </div><!-- /.form-group -->
                            
                        </div><!-- /.col-md-5 -->
                        <div class="col-md-7">
                            
                            <div class="form-group">
                                <label for="mkeys">Meta-Keywords</label>
                                <div class="input-group">
                                    <input id="mkeys" type="text" class="form-control" name="FORM[meta_keywords]" value="<% $TPLOBJ.formcontent.meta_keywords|hsc %>">
                                    <span class="input-group-btn"><button class="btn btn-secondary" type="button" onClick="GetRequest2Value('mkeys','genkeys','run','&uselang=<%$GET.uselang%>&epage=<%$epage%>&id=<% $TPLOBJ.id%>&conid=<% $TPLOBJ.formcontent.id %>'); return false;"><i class="fa fa-refresh"></i> generieren</button></span>
                                </div><!-- /.input-group -->
                                <span class="help-block">Der Meta-Titel wird im Browserfester dargestellt.</span>
                            </div><!-- /.form-group -->
                            
                        </div><!-- /.col-md-7 -->
                    </div><!-- /.row -->
                    <div class="row">
                        <div class="col-md-12"><div class="form-group">
                            <label for="mdesc">Meta-Description</label>
                            <textarea class="form-control" id="mdesc" name="FORM[meta_desc]" onKeyPress="return taLimit(this,<% $gbl_config.metadesc_count%>,event)" onKeyUp="return taCount(this,'myCounter',<% $gbl_config.metadesc_count%>)"><% $TPLOBJ.formcontent.meta_desc|hsc %></textarea>
                            <span class="help-block">You have <b><span id="myCounter"><% $gbl_config.metadesc_count %></span></b> characters remaining for your description...</span>
                            <a class="btn btn-secondary" href="javascript:void(0);" onClick="GetRequest2Value('mdesc','genmeta','run','&uselang=<%$GET.uselang%>&epage=<%$epage%>&id=<% $TPLOBJ.id%>&conid=<% $TPLOBJ.formcontent.id %>'); return false;">Meta Description generieren</a>
                        </div><!-- /.form-group --></div>
                    </div>
                    <!-- /.row -->
                </div><!-- /.form-body -->
        
                <div class="form-feet">
                    <%$subbtn %>
                </div>
        
            </form>
        
  <%include file="cb.panel.footer.tpl"%>

<%/if%>