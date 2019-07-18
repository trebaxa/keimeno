<%include file="cb.panel.header.tpl" title="Themen Bild"%>
    <div class="row">
        <div class="col-md-6">
        
          
                <script>
                function reload_theme_image(content_id) {
                    $.getJSON( "<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_theme_image&content_id="+content_id, function( data ) {
                      $('#js-theme-img').attr('src','../file_data/themeimg/'+data.theme_image);
                      $('#themeimage').show();  
                    });
                }
                </script>
                
                <form role="form" class="jsonform" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">

                    <input type="hidden" name="tid" value="<% $GET.id %>">
                    <input type="hidden" name="tl" value="<% $GET.tl %>">
                    <input type="hidden" name="cmd" value="save_theme">
                    <input type="hidden" name="uselang" value="<% $GET.uselang%>">
                    <input type="hidden" name="lang_id" value="<% $GET.uselang %>">
                    <input type="hidden" name="FORM[tid]" value="<% $GET.id %>">
                    <input type="hidden" name="tabid" value="3">
                    <input type="hidden" name="epage" value="<% $epage %>"> 
                    <input type="hidden" name="tmsid" value="<% $GET.tmsid %>">
                    <input type="hidden" name="content_id" value="<% $TPLOBJ.formcontent.id %>">
                    
                    <fieldset>
                        <legend><i class="far fa-images"><!----></i> Bild / Bildbeschreibung</legend>
                        
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group">
                                    <label for="datei">{LBL_HEADER_THEME_IMAGE}</label>
                                    <div class="input-group">
                                        <input type="text" name="" value="" class="form-control" readonly="" placeholder="Keine Datei ausgewählt">
                                        <input id="datei" type="file" name="datei" value="" class="xform-control" onchange="this.previousElementSibling.value = this.value">
                                        <span class="input-group-btn">
                                            <button class="btn btn-secondary" type="button">Durchsuchen...</button>
                                  
                                        </span>
                                    </div><!-- /input-group -->
                                </div><!-- /.form-group -->
                            </div>
                            <div class="col-md-1">
                                <label class="">&nbsp;</label>
                                <button class="btn btn-primary pull-right" type="submit"><i class="fa fa-save"></i></button>
                            </div>
                        </div>
                           
                        
                        <div id="themeimage" <%if ($TPLOBJ.formcontent.theme_image=="") %>style="display:none"<%/if%>>
                            <a href="javascript:void(0);" onclick="delete_theme_image()"><i class="fa fa-trash"></i></a>
                            <a target="_timg" href="../file_data/themeimg/<% $TPLOBJ.formcontent.theme_image %>">
                            <img id="js-theme-img" class="img-thumbnail img-rounded img-fullwidth" src="../file_data/themeimg/<% $TPLOBJ.formcontent.theme_image %>"></a>
                            <span class="help-block">&lt;% $PAGEOBJ.theme_image%&gt; | <a target="_blank" href="https://cloud.google.com/vision/?hl=de">Check on Google KI</a></span>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alttag">Image ALT Tag:</label>
                                    <input id="alttag" type="text" class="form-control" value="<% $TPLOBJ.formcontent.t_imgthemealt|sthsc %>" name="FORM[t_imgthemealt]">
                                    <span class="help-block">&lt;% $PAGEOBJ.t_imgthemealt%&gt;</span>
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-6 -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titletag">Image TITLE Tag:</label>
                                    <input id="titletag" type="text" class="form-control" value="<% $TPLOBJ.formcontent.t_imgthemetitle|sthsc %>" name="FORM[t_imgthemetitle]">
                                    <span class="help-block">&lt;% $PAGEOBJ.t_imgthemetitle%&gt;</span>
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-6 -->
                        </div><!-- /.row -->
                    </fieldset>
                    
                    <fieldset>
                        <legend><i class="fa fa-crop"><!----></i> Bildgrösse und Zuschnitt</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cwidth">Zuschnitt Breite:</label>
                                    <div class="input-group">
                                        <input id="cwidth" type="text" class="form-control" size="5" value="<% $TPLOBJ.formcontent.t_tiwidth|hsc %>" name="FORM[t_tiwidth]">
                                        <span class="input-group-addon">Pixel</span>
                                    </div><!-- /.input-group -->
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-6 -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cheight">Zuschnitt H&ouml;he:</label>
                                    <div class="input-group">
                                        <input id="cheight" type="text" class="form-control" size="5" value="<% $TPLOBJ.formcontent.t_tiheight|hsc %>" name="FORM[t_tiheight]">
                                        <span class="input-group-addon">Pixel</span>
                                    </div><!-- /.input-group -->
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-6 -->
                        </div><!-- /.row -->
                        <div class="form-group">
                            <label for="cpos">Crop Position:</label>
                            <select id="cpos" class="form-control custom-select" name="FORM[t_ticroppos]"><% foreach from=$WEBSITE.croppositions item=pos %><option <% if ($pos==$TPLOBJ.formcontent.t_ticroppos) %>selected<%/if%> value="<%$pos%>"><%$pos%></option><%/foreach%></select>
                        </div><!-- /.form-group -->
                        <div class="form-group">
                            <label for="tdesc">Theme Description</label>
                            <textarea data-theme="<%$gbl_config.ace_theme%>" id="tdesc" class="form-control se-html" rows="21" name="FORM[t_themedescription]"><% $TPLOBJ.formcontent.t_themedescription|hsc %></textarea>
                            <span class="help-block">&lt;% $PAGEOBJ.t_themedescription%&gt;</span>
                        </div><!-- /.form-group -->
                    </fieldset>
    
                    <div class="form-feet"><%$subbtn%></div>
                </form>
           
        
        </div><!-- /.col-md-6 -->       
    </div><!-- /.row -->

    
<%include file="cb.panel.footer.tpl"%>

<script>
    function delete_theme_image() {
       console.log('<%$PHPSELF%>?epage=<%$epage%>&toplevel=<% $GET.toplevel%>&cmd=themepicdelete&id=<% $TPLOBJ.id%>&uselang=<%$GET.uselang%>');
       execrequest('<%$PHPSELF%>?epage=<%$epage%>&toplevel=<% $GET.toplevel%>&cmd=themepicdelete&id=<% $TPLOBJ.id%>&uselang=<%$GET.uselang%>');
       $('#themeimage').fadeOut(200);       
    }

</script>

