
 <div class="row">
            <div class="col-md-3">
                
                <% include file="memindex.editor.foto.tpl" %>
                
                <% if ($CUSTOMER.kid>0) %>
                    <%* 'Anfang: Anschrift' *%>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">{LA_ANSCHRIFT}</h3><!-- /.panel-title -->
                        </div><!-- /.panel-heading -->
                    
                        <div class="panel-body">
                            <address>
                                <% if ($CUSTOMER.firma!="") %>
                                    <% $CUSTOMER.firma %><br>
                                <%/if%>
                                <% $CUSTOMER.vorname %> <% $CUSTOMER.nachname %><br>
                                <% $CUSTOMER.strasse %> <% $CUSTOMER.hausnr %><br>
                                <% if ($CUSTOMER.strasse_zusatz!="") %><% $CUSTOMER.strasse_zusatz %><%/if%>
                                <% $CUSTOMER.plz %> <% $CUSTOMER.ort %><br>
                                <% $CUSTOMER.province %> <% $CUSTOMER.country.land %><br>
                                <% $POBJ.custemailto%><br>
                            </address>
                        </div><!-- /.panel-body -->
                    </div><!-- /.panel panel-default -->
                    <%* 'Ende: Anschrift' *%>
                <%/if%>
                
                <% if ($CUSTOMER.kid > 0)%>
                <%* 'Panel Anfang: Email an Kunden' *%>
               <%* <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">{LA_EMAILANKUNDEN}</h3><!-- /.panel-title -->
                    </div><!-- /.panel-heading -->
                <div class="panel-body">
                    <form action="small_tasks.php" method="post">
                       <div class="form-group">
                        <label>E-Mail Vorlage:</label> 
                        <select class="form-control custom-select" name="emt" size="-1"><%$POBJ.mailtemps%></select>
                        </div>
                        <input autocomplete="off" type="hidden" name="kid" value="<%$CUSTOMER.kid%>">
                        <input autocomplete="off" type="hidden" name="aktion" value="email_form_show">
                        <%$POBJ.mailsendbtn%>
                    </form>
                    </div>
                </div><!-- /.panel panel-default -->*%>
                <%* 'Panel Ende: Email an Kunden' *%>
    
                <%* 'Panel Anfang: Aufgaben' *%>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">{LA_AUFGABEN}</h3><!-- /.panel-title -->
                    </div><!-- /.panel-heading -->
                    
                    <div class="panel-body">
                       <div class="btn-group btn-group-sm">
                        <a class="btn btn-secondary json-link" href="<% $PHPSELF %>?cmd=a_sendpassword&kid=<% $GET.kid %>">{LA_NEUESPASSWORTZUSENDEN}</a>
                        <a class="btn btn-secondary json-link" href="<% $PHPSELF %>?cmd=sendaktlink&kid=<% $GET.kid %>">Aktivierungslink senden</a>
                        <%$POBJ.delcusticon%>
                        </div>
                        
                    </div><!-- /.panel-body -->
                </div><!-- /.panel panel-default -->
                
                <%include file="cb.panel.header.tpl" type="info" title="GEO"%>
                 <iframe style="border:1px solid #eee" src="https://www.trebaxa.com/gmgen.php?width=100%&address=<%$CUSTOMER.plz%> <%$CUSTOMER.strasse%> <%$CUSTOMER.hausnr%> <%$CUSTOMER.ort%> <%$CUSTOMER.COUNTRY%>" frame width="100%" scrolling="no" height="300"></iframe>
                <%include file="cb.panel.footer.tpl"%>
                
                <%* 'Panel Ende: Aufgaben' *%>
            <%/if%>
                
            </div><!-- /.col-md-3 -->
            <div class="col-md-9">
                <% include file="memindex.editor.data.right.tpl" %>
            </div><!-- /.col-md-9 -->
        </div><!-- /.row -->

        <script>
            function reload_item() {
                $.getJSON( "<%$PHPSELF%>?epage=<%$epage%>&cmd=load_cust_json&kid=<% $CUSTOMER.kid %>", function( data ) {
                    $('#kreg-theme-img').attr('src', data.picture+'?a='+Math.random());
                    $('#kreg-theme-img').fadeIn();
                    $('.del_cust_img').show();
                }); 
            }
            
            
            <% if ($CUSTOMER.foto_exists==true) %>
                reload_item();
            <%/if%>
        </script> 
        
 
            <%include file="cb.panel.header.tpl" title="Mitgliedschaft & Berechtigungen"%>
            <form method="post" action="<%$PHPSELF%>" class="form jsonform">
                <fieldset>
                    <legend>Mitgliedschaft</legend>
                    <table class="table table-striped">
                        <% foreach from=$POBJ.collection item=row %>
                            <% if (count($row.group_ids)>0) %>
                                <tr>
                                    <td><strong><% $row.col_name %></strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul>
                                        <% foreach from=$row.groups item=group %>
                                           <li> 
                                            <div class="checkbox">
                                                <label>
                                                    <input class="form-control" type="checkbox" <% if ($group.gid|in_array:$group.col_group_ids) %>checked<%/if%> name="MEMBERGROUPSCOL[<%$group.gid%>_<% $row.id%>]" value="<%$group.gid%>_<% $row.id%>"><%$group.G_OBJ.groupname%>
                                                </label>
                                            </div>
                                           </li> 
                                        <%/foreach%>
                                        </ul>
                                    </td>
                                </tr>
                            <%/if%>
                        <%/foreach%>
                     </table>
                    <h3>Berechtigungsgruppen</h3>
                    <table class="table table-striped">
                        <% foreach from=$POBJ.rgroups item=row %>
                            <tr>
                                <td>
                                <div class="checkbox">
                                 <label>
                                    <input class="input-sm" autocomplete="off" type="checkbox" <% if ($row.id|in_array:$POBJ.cust_groups_active) %>checked<%/if%> name="MEMBERGROUPS[<% $row.id %>]" value="<% $row.id %>">
                                    <% $row.groupname %>
                                  </label>  
                                </div>
                                </td>
                            </tr>
                        <%/foreach%>
                    </table>
                    
                    <input type="hidden" name="cmd" value="savegroupkid">
                    <input type="hidden" name="kid" value="<% $REQUEST.kid %>">
                </fieldset>
            <div class="form-feet">
                <%$subbtn%>
            </div><!-- /.form-feet -->
        </form>
    <%include file="cb.panel.footer.tpl"%> 