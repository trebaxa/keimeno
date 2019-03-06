<script>
function reload_website_settings() {
    $.getJSON( '<%$eurl%>cmd=reload_website_settings&id=<% $GET.id %>&uselang=<% $GET.uselang %>', function( data ) {
        if (data.t_icon!="") { 
            $('#page-icon').fadeIn();
            $('#page-icon img').attr('src','../file_data/menu/'+data.t_icon+'?'+Math.random(10000));
         } else {
            $('#page-icon').hide();
         }  
    });
}
</script>
<%include file="cb.panel.header.tpl" title="{LA_SETTINGS}"%>
   
    
    <form role="form" class="jsonform" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        
        <input type="hidden" name="tid" value="<% $GET.id %>">
        <input type="hidden" name="tl" value="<% $GET.tl %>">
        <input type="hidden" name="cmd" value="save_website_settings">
        <input type="hidden" name="uselang" value="<% $GET.uselang%>">
        <input type="hidden" name="starttree" value="<% $REQUEST.starttree%>">        
        <input type="hidden" name="lang_id" value="<% $GET.uselang %>">
        <input type="hidden" name="FORM[tid]" value="<% $GET.id %>">
        <input type="hidden" name="epage" value="<% $epage %>"> 
        <input type="hidden" name="tabid" value="2">
        <input type="hidden" name="is_json_form" value="1">        
        <input type="hidden" name="tmsid" value="<% $GET.tmsid %>">
        <input type="hidden" name="content_id" value="<% $TPLOBJ.formcontent.id %>">
        
        <div class="row">
            <div class="col-md-6">
        
                <div class="form-group">
                    <label for="path">Path</label>
                    <!-- <input type="text" id="path" class="form-control" readonly value="<%$TPLOBJ.formcontent.t_breadcrumb %>"> -->
                    <p class="form-control-static"><%$TPLOBJ.formcontent.t_breadcrumb %></p>
                </div><!-- /.form-group -->
                
                <div class="form-group">
                    <label for="entre">Einstiegseite</label>
                    <p class="form-control-static"><%$WSOBJ.entrypoint %></p>
                </div><!-- /.form-group -->
                
                <div class="form-group">
                    <label for="">Link Platzhalter</label>
                    <p class="form-control-static"><% $TPLOBJ.urltpl %></p>
                </div><!-- /.form-group -->
                
                <%foreach from=$WSOBJ.webopt key=wk item=wo %>
                    <div class="form-group">
                    <label for=""><%$wk%></label><%$wo %></div>
                <%/foreach%>
                
                <div class="form-group">
                <label for="mainpage">{LA_ISSTARTSITE}</label>
                <input <% if ($TPLOBJ.is_startsite==0) %>checked<%/if%> type="radio" name="FORM_TEMPLATE[is_startsite]"  value="0"> {LBL_NO}
                <input <% if ($TPLOBJ.is_startsite==1) %>checked<%/if%> type="radio" name="FORM_TEMPLATE[is_startsite]"  value="1"> {LBL_YES}
            </div><!-- /.form-group -->
                
                <div class="form-group">
                    <label for="xml-sitemap">XML Sitemap Export</label>
                    <input <% if ($TPLOBJ.xml_sitemap==1) %>checked<%/if%> type="checkbox" value="1" name="FORM_TEMPLATE[xml_sitemap]">
                </div><!-- /.form-group -->                
                
                
                <% if ($TPLOBJ.module_id=='newslist') %>
                    <div class="form-group">
                        <label for="">{LBL_RSSFEDSHOW}</label>
                        <input <% if ($TPLOBJ.show_rss_link==1) %>checked<%/if%> type="checkbox" name="FORM_TEMPLATE[show_rss_link]" value="1">
                    </div><!-- /.form-group -->
                <%/if%>
                
                <%if ($TPLOBJ.module_id=='sendform') %>
                    <div class="form-group">
                        <label for="">{LBL_HOST}</label>
                        <input value="<% $TPLOBJ.sf_host|hsc %>" name="FORM_TEMPLATE[sf_host]">
                    </div><!-- /.form-group -->
                <%/if%>
                
                
                
            </div><!-- /.col-md-6 -->
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <%if ($WEBSITE.permboxes!="") %>
                            <div class="form-group">
                                <legend>{LBL_WHOSEES}</legend>
                                <%$WEBSITE.permboxes%>
                            </div>
                        <%/if%>
                    </div>
                    <div class="col-md-6">
                    <%if ($WEBSITE.toplevel_box!="") %>
                        <div class="form-group">
                            <legend>Toplevel</legend>
                            <%$WEBSITE.toplevel_box%>
                        </div>
                    <%/if%>
                   </div>
                </div>    
                <div id="page-icon" <%if ($TPLOBJ.formcontent.t_icon=="") %>style="display:none"<%/if%>>    
                    <a target="_timg" href="../file_data/menu/<% $TPLOBJ.formcontent.t_icon %>"><img class="img-thumbnail" src="../file_data/menu/<% $TPLOBJ.formcontent.t_icon %>"></a>                    
                    <a class="btn btn-danger deljson" href="javascript:void(0);" title="löschen" data-ident="<% $TPLOBJ.id%>" data-toadd="&uselang=<%$GET.uselang%>" data-phpfile="<%$PHPSELF%>" data-confirm="0" data-epage="<%$epage%>" data-ctext="Sind Sie sicher?" data-cmd="delete_icon_image">   <i class="fa fa-trash"><!----></i></a>
                </div>
                
                <div class="form-group">
                    <label for="fw">Framework</label>
                    <select id="fw" class="form-control" name="FORM_TEMPLATE[use_framework]">
                        <% foreach from=$WEBSITE.frameworks item=row %>
                            <option value="<%$row.id%>" <% if ($row.id==$TPLOBJ.use_framework) %> selected <%/if%>><%$row.description%></option>
                        <%/foreach%>
                    </select>
                </div><!-- /.form-group -->
                <div class="form-group">
                    <label for="canonurl">Canonical URL</label>
                    <input id="cononurl" class="form-control" value="<% $TPLOBJ.canonical_url|sthsc %>" name="FORM_TEMPLATE[canonical_url]">
                </div><!-- /.form-group -->

                <div class="form-group">
                    <label for="bs-class">Class</label>
                    <input id="bs-class" class="form-control" value="<% $TPLOBJ.t_class|sthsc %>" name="FORM_TEMPLATE[t_class]">
                    <p class="help-block">Class Attribute des Links &lt;%$element.t_class%&gt;</p>
                </div><!-- /.form-group -->             

                <div class="form-group">
                    <label for="bs-attribute">Link Attribute</label>
                    <input id="bs-attribute" class="form-control" value="<% $TPLOBJ.t_attributes|sthsc %>" name="FORM_TEMPLATE[t_attributes]">
                    <p class="help-block">Link Attribute des Links &lt;%$element.t_attributes%&gt;</p>
                </div><!-- /.form-group -->                     

                <div class="form-group">
                    <label for="datei">Icon</label>
                    <div class="input-group">
                        <input type="text" name="" value="" class="form-control" readonly="" placeholder="Keine Datei ausgewählt">
                        <input id="datei" type="file" name="dateiicon" value="" class="xform-control autosubmit" onchange="this.previousElementSibling.value = this.value">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">Durchsuchen...</button>
                        </span>
                    </div><!-- /input-group -->
                </div><!-- /.form-group -->                
                
            </div><!-- /.col-md-6 -->
        </div><!-- /.row -->
        
        <div class="form-feet">
           <%* <a class="btn btn-default json-link" href="<%$eurl%>cmd=replicatelang&id=<%$GET.id%>&uselang=<% $GET.uselang %>"><i class="fa fa-language" aria-hidden="true"></i>
 Inhalt auf andere Sprachen replizieren</a>*%>
            <% $subbtn %>
        </div>
    </form>
<%include file="cb.panel.footer.tpl"%>

<%include file="gblvars.website.tpl"%>