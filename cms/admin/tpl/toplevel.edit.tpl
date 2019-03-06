
<h3>Thema bearbeiten</h3>
    
    <% if ($GBLPAGE.access.language==TRUE)%>
   
    <div>
            <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data" class="jsonform form-inline">
                <input type="hidden" name="cmd" value="save_topl">
                <input type="hidden" id="toplid" name="id" value="<% $GET.id %>">
                <input type="hidden" id="toplcondid" name="conid" value="<% $toplobj.con.id%>">
                <input type="hidden" value="<%$epage%>" name="epage">
                <input type="hidden" name="FORM_CON[lang_id]" value="<% $GET.uselang %>">
                <input type="hidden" name="FORM_CON[tid]" value="<% $GET.id %>"> 
                
                <table class="table table-striped table-hover">
                    <tr>
                        <td class="tdlabel">{LBL_LANGUAGE}:</td>
                        <td><% $toplobj.build_lang_select %></td>
                    </tr>
                    
                    <% if ($toplobj.admin==0) %>
                        <tr>
                            <td class="tdlabel">Admin. {LBLA_DESCRIPTION}:</td>
                            <td><input id="topleveldescription" type="text" class="form-control" name="FORM[description]" size="30" value="<%$toplobj.description|sthsc%>"></td>
                        </tr>
                        <tr>
                            <td class="tdlabel">{LBL_ONLYURL_REDIRECT}</td>
                            <td><input type="text" class="form-control" name="FORM[url_redirect]" size="30" value="<%$toplobj.url_redirect|sthsc%>"></td>
                        </tr>
                        <tr>
                            <td class="tdlabel">{LBL_ONLYURL_TARGET}:</td>
                            <td><input type="text" class="form-control" name="FORM[url_redirect_target]" size="30" onclick="if (this.value=='') this.value='_self'" value="<%$toplobj.url_redirect_target|sthsc%>"></td>
                        </tr>
                    <%/if%>
                    
                    <tr>
                        <td class="tdlabel">{LBL_HEADER_THEME_IMAGE}:</td>
                        <td><input type="file" name="datei" size="30" value="" class="autosubmit"></td>
                    </tr> 
                    <tr>
                        <td class="tdlabel">Icon:</td>
                        <td><input type="file" name="icon_datei" size="30" value="" class="autosubmit"></td>
                    </tr>    
                    <tr>
                        <td class="tdlabel">{LBL_TITLE}:</td>
                        <td><input size="30" type="text" class="form-control" value="<%$toplobj.con.level_name|sthsc%>" name="FORM_CON[level_name]"></td>
                    </tr>
                    <tr>
                        <td class="tdlabel">{LBL_SUBTITLE}:</td>
                        <td><textarea class="form-control" rows="6" cols="60" name="FORM_CON[level_subtitle]"><%$toplobj.con.level_subtitle|sthsc%></textarea></td>
                    </tr>
                </table>
            
                <%$subbtn%>
            </form>
     
        
            <div id="theme_img" <%if ($toplobj.con.theme_image=="") %>class="hideelement"<%/if%>>
                <br><a target="_timg" href="../file_server/template/<% $toplobj.con.theme_image %>"><img class="bigimg" src="<% $toplobj.con.theme_image_thumb %>" ></a>
                <a title="{LBL_DELETE}" href="javascript:delete_theme_image();"><i class="fa fa-trash"></i></a>
            </div>
         
        
            <div id="topl_icon" <%if ($toplobj.con.tpl_icon=="") %>class="hideelement"<%/if%>>
                <br><a target="_timg" href="../file_server/template/<% $toplobj.con.tpl_icon %>"><img class="bigimg" src="<% $toplobj.con.tpl_icon_thumb %>" >
                </a><a title="{LBL_DELETE}" href="javascript:delete_topl_icon();"><i class="fa fa-trash"></i></a>
            </div>

    
    </div>
     
    <script>
        function set_topl_ids(id,conid) {
            $('#toplid').val(id);
            $('#toplcondid').val(conid);
            $.getJSON( "<%$PHPSELF%>?epage=<%$epage%>&id="+id+"&cmd=reloadimgs&lang_id=<%$GET.uselang%>", function( data ) {
                if (data.icon!="") {
                    $('#topl_icon ').find('.bigimg').attr('src',data.icon+'?a='+Math.random(1,10000));
                    $('#topl_icon').show();
                }

                if (data.theme!="") {
                    $('#theme_img ').find('.bigimg').attr('src',data.theme+'?a='+Math.random(1,10000));
                    $('#theme_img').show();
                }                
            });
        }
        function delete_topl_icon() {
            execrequest('<%$PHPSELF%>?epage=<%$epage%>&id=<% $GET.id %>&cmd=delete_topl_icon&lang_id=<%$GET.uselang%>');
            $('#topl_icon').fadeOut();
        }
        
        function delete_theme_image() {
            execrequest('<%$PHPSELF%>?epage=<%$epage%>&id=<% $GET.id %>&cmd=delete_theme_image&lang_id=<%$GET.uselang%>');
            $('#theme_img').fadeOut();
        }    
    </script>
    
    <%else %>
        <%include file="no_permissions.admin.tpl" %>
    <%/if%>