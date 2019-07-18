<!-- Modal -->
<div class="modal fade" id="js-addgroup" tabindex="-1" role="dialog" aria-labelledby="js-addgroupLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form method="post" action="<%$PHPSELF%>" class="jsonform form-inline">        
        <input type="hidden" name="cmd" value="save_group">
        <input type="hidden" name="id" value="0">    
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="section" value="<%$section%>">
      <div class="modal-header">
        <h5 class="modal-title" id="js-addgroupLabel">Neue Gruppe</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>{LBL_GROUP} {LBL_TITLE}</label>
            <input type="text" class="form-control" name="FORM[groupname]" required="" autocomplete="off" placeholder="{LBL_GROUP} {LBL_TITLE}">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>



<script>
    function reloadgroups() {
       simple_load('custgroups','<%$PHPSELF%>?epage=<%$epage%>&cmd=reloadgroups');
    }
</script>


<%if ($section=='start') %>
    <div class="page-header">
        <h1><i class="fa fa-users"><!----></i> Benutzergruppen</h1>
    </div><!-- /.page-header -->
    <div id="custgroups">
        <%include file="custgroup.list.tpl"%>
    </div>    
<%/if%>


<%if ($section=='coll') %>
    <%include file="custgroups.coll.tpl"%>
<%/if%>


<% if ($section=='edit') %>
    
     <%include file="cb.page.title.tpl" icon="" title="{LBL_GROUPS}"%>
    
     <%include file="cb.panel.header.tpl" title="{LBL_PERMISSIONS}"%>
        
        <form method="post" action="<%$PHPSELF%>" class="jsonform form-inline">
            <div class="form-group">
                <label for="gtitle">{LBL_GROUP} {LBL_TITLE}</label>
                <input id="gtitle" type="text" class="form-control" name="FORM[groupname]" value="<%$CUSTGROUPS.group.groupname%>">
            </div><!-- /.form-group -->
        
            <table class="table table-striped table-hover" >
                <thead>
                    <tr>
                        <th>Modul</th>
                        <th>{LBL_ADD}</th>
                        <th>{LBL_EDIT}</th>
                        <th>{LBL_DELETE}</th>
                    </tr>
                </thead>
  
                <% foreach from=$CUSTGROUPS.pages item=row %>
                    <tr>
                        <td><%$row.description%><input type="hidden" name="pageids[<%$row.PAGEID%>]" value="<%$row.PAGEID%>"></td>
                        <td valign="middle" ><input <% if ($row.p_add==1) %>checked<%/if%> type="checkbox" name="PERM[<%$row.PAGEID%>][p_add]" value="1"></td>
                        <td valign="middle" ><input <% if ($row.p_edit==1) %>checked<%/if%> type="checkbox" name="PERM[<%$row.PAGEID%>][p_edit]" value="1"></td>
                        <td valign="middle" ><input <% if ($row.p_del==1) %>checked<%/if%> type="checkbox" name="PERM[<%$row.PAGEID%>][p_del]" value="1"></td>
                    </tr>
                <%/foreach%>

                <% foreach from=$CUSTGROUPS.perm item=row %>
                    <tr>
                        <td><%$row.module_name%><input type="hidden" name="pagemods[<%$row.key%>]" value="<%$row.key%>"></td>
                        <td valign="middle" ><input <% if ($row.CP.p_add==1) %>checked<%/if%> type="checkbox" name="PERMOD[<%$row.key%>][p_add]" value="1"></td>
                        <td valign="middle" ><input <% if ($row.CP.p_edit==1) %>checked<%/if%> type="checkbox" name="PERMOD[<%$row.key%>][p_edit]" value="1"></td>
                        <td valign="middle" ><input <% if ($row.CP.p_del==1) %>checked<%/if%> type="checkbox" name="PERMOD[<%$row.key%>][p_del]" value="1"></td>
                    </tr>
                <%/foreach%> 
  
            </table>
            <input type="hidden" name="cmd" value="save_group">
            <input type="hidden" name="id" value="<%$GET.id%>">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="section" value="<%$section%>">
            <div class="form-feet"><%$subbtn%></div><!-- /.form-feet -->
        </form>
  <%include file="cb.panel.footer.tpl"%>
<%/if%>


<% if ($section=='ksuche') %>
  
    <%include file="cb.page.title.tpl" icon="" title="Gefunden"%>
    <table class="table table-striped table-hover" >
    
        <% foreach from=$CUSTGROUPS.customers item=row %>
            <tr>
                <td><%$row.kid%></td>
                <td><%$row.nachname%></td>
                <td><%$row.vorname%> </td>
                <td><%$row.firma%></td>
                <td> <%$row.email%></td>
                <td> <%$row.email_notpublic%></td>
                <td class="text-right" valign="middle"><% foreach from=$row.icons key=iconkey item=picon %><% $picon %><%/foreach%></td>
            </tr>
        <%/foreach%> 
        
    </table>

<%/if%> 
 
 
 <% if ($section=='showcustomer') %>
    <%include file="cb.page.title.tpl" icon="" title="Mitglieder der Gruppe"%>
    <div class="btn-group">
        <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=all&section=start">Alle Gruppen anzeigen</a>
    </div><!-- /.btn-group -->

    <form method="post" action="<%$PHPSELF%>">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Knr</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th></th>
                </tr>
            </thead>
            
            <% foreach from=$CUSTGROUPS.customers item=row %>
                <tr>
                    <td><a href="kreg.php?cmd=show_edit&kid=<%$row.kid%>"><%$row.kid%></a></td>
                    <td><%$row.nachname%>, <%$row.vorname%> <% if ($row.username!="")%><%$row.username%><%/if%></td>
                    <td><%$row.email%></td>
                    <td class="text-right" valign="middle"><% foreach from=$row.icons key=iconkey item=picon %><% $picon %><%/foreach%></td>
                </tr>
            <%/foreach%> 
        
        </table>
        <input type="hidden" name="cmd" value="savecolconnect">
        <input type="hidden" name="gid" value="<%$GET.id%>">
        <input type="hidden" name="epage" value="<%$epage%>"><input type="hidden" name="section" value="<%$section%>">
    </form>

 <%/if%>

<% if ($section=='a_addcolgroup') %>

    
    <%include file="cb.page.title.tpl" icon="" title="{LBL_MEMSELECTIONS} ->`$CUSTGROUPS.COLL_OBJ.col_name`"%>
    <form method="post" action="<%$PHPSELF%>">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{LBL_TITLE}</th>
                    <th>Options</th>
                </tr>
            </thead>

            <% foreach from=$CUSTGROUPS.groups item=row %>
                <tr>
                    <td>ID:<%$row.GID%></td>
                    <td><%$row.groupname%> [<%$row.KCOUNT%>]</td>
                    <td class="text-right" valign="middle"><input <%$row.checked%> type="checkbox" name="FORM[bereich_id][<%$row.GID%>]" value="<%$row.GID%>"></td>
                </tr>
            <%/foreach%>
            
        </table>
    
        <input type="hidden" name="cmd" value="savecol">
        <input type="hidden" name="collid" value="<%$CUSTGROUPS.COLL_OBJ.id%>">
        <input type="hidden" name="epage" value="<%$epage%>"><input type="hidden" name="section" value="<%$section%>">
        
        <div class="form-feet"><%$subbtn%></div><!-- /.form-feet -->
    </form>

<%/if%>


<!-- Modal -->
<div class="modal fade" id="js-addkunde" tabindex="-1" role="dialog" aria-labelledby="js-addkundeLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="js-addkundeLabel">Kunden Suche</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
      </div>
      <div class="modal-body">
        <form method="post" action="<%$PHPSELF%>">
        <input type="text" class="form-control" placeholder="Suchbegriff"  id="custsearchword" value="<%$REQUEST.wort|sthsc%>" onKeyUp="simple_load('custgsearch','<%$PHPSELF%>?epage=<%$epage%>&cmd=custsearch&id='+$('#custgroupid').val()+'&sw='+$('#custsearchword').val());">
        <input type="hidden" name="cmd" value="a_ksuche">
        <input type="hidden" name="id" id="custgroupid" value="">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="section" value="<%$section%>">
    </form>
    <div id="custgsearch"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>       
      </div>
    </div>
  </div>
</div>
 
