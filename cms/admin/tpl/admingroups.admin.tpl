<%include file="cb.page.title.tpl" icon="fa fa-users" title="{LBLA_ADMINGROUPS_MANAGER}"%>

<div class="btn-group">
    <a class="btn btn-primary" href="<%$PHPSELF%>?epage=<%$epage%>">{LBLA_SHOWALL}</a>
    <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=edit"><i class="fa fa-plus"></i></a>
<%*    <a class="btn btn-secondary" href="#" onclick="dc_show('addpolicy');">Policy hinzufügen</a> *%>
</div> 



<% if ($cmd=='') %>
        <%include file="cb.panel.header.tpl" title="{LBLA_ADMINGROUPS_MANAGER}"%>        
        <form class="jsonform" action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="cmd" value="ag_savetable">
            <input type="hidden" name="epage" value="<%$epage%>">
            
            <table class="table table-striped table-hover" id="admingrouptable">
                <thead>
                    <tr>
                        <th class="col-md-1">Sort.</th>
                        <th>{LBL_GROUPNAME}</th>
                  <!--      <th><a href="<%$PHPSELF%>?aktion=<%$aktion%>&epage=<%$epage%>&col=rl_ident&direc=<%$REQUEST.direc%>">{LBL_ROLE} Ident</a></th>
                        <th><a href="<%$PHPSELF%>?aktion=<%$aktion%>&epage=<%$epage%>&col=rl_name&direc=<%$REQUEST.direc%>">{LBL_ROLE}</a></th>-->
                        <th class="tdright">Aufgaben</th>
                    </tr>
                </thead>
                <tbody>
                <% foreach from=$AGROUP.admin_groups item=ag %>
                    <tr>
                        <td><input type="text" class="form-control" maxlength="2" size="2" value="<% $ag.ag_sort %>" name="FORM[<%$ag.GID%>][ag_sort]"></td>
                        <td><a href="<%$PHPSELF%>?epage=<%$epage%>&id=<%$ag.id%>&cmd=edit"><% $ag.mgname %></a></td>
                       <!-- <td><% $ag.rl_ident %></td>
                        <td><% $ag.role_names %></td>
                        -->
                        <td class="text-right">
                            <div class="btn-group">
                                <% foreach from=$ag.icons item=picon name=cicons %><% $picon %><%/foreach%>
                            </div><!-- /.btn-group -->
                        </td>
                    </tr>
                <%/foreach%>
                </tbody>
            </table>
            
            <div class="form-feet"><%$subbtn%></div>
        </form>
         <%include file="cb.panel.footer.tpl"%>
    
<%* Tabellen Sortierungs Script *%>
<%include file="table.sorting.script.tpl" tablesortid="admingrouptable"%>

<%/if%>


<% if ($cmd=='edit') %>
    <div class="row">
        <div class="col-md-12">
            <%include file="admingroups.edit.tpl"%>
       </div>     
       <div class="col-md-6">
            <%include file="admingroups.pageaccess.tpl"%>
       </div>
    </div>   
<%/if%>

<% if ($aktion=='roles') %>
    <script>
        function clear_role() {
            $('#formrolegpo input[type="text"]').val('');
            $('#formrolegpo input[name="id"]').val('0');
            $('#formrolegpo').removeClass('jsonform');
            $('#formrolegpo').unbind('submit');
        }
    </script>

    
<%include file="cb.panel.header.tpl" title="{LBL_ROLES}"%>
    <a class="btn btn-secondary" href="#" onclick="clear_role();">{LBL_ADD_ROLE}</a>
    
    <form action="<%$PHPSELF%>" id="formrolegpo" method="post" enctype="multipart/form-data" <% if ($REQUEST.id>0) %>class="jsonform form-inline"<%/if%>>
        <input type="hidden" name="cmd" value="save_role">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="id" value="<%$REQUEST.id%>">
        <div class="row">
            <div class="col-md-6">
                <fieldset>
                        <label>{LBL_ROLE} Name:</label>
                        <input type="text" required class="form-control" name="FORM[rl_name]" size="21" value="<%$AGROUP.loaded_role.rl_name|sthsc%>">
                        <label>{LBL_ROLE} Ident:</label>
                        <input type="text" required class="form-control" name="FORM[rl_ident]" maxlength="3" size="3" value="<%$AGROUP.loaded_role.rl_ident|sthsc%>">
                        <div class="subright"><%$subbtn%></div>
                </fieldset> 
            </div>
        </div>
    </form> 

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{LBL_ROLE} Name</th>
                <th>{LBL_ROLE} Ident</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <% foreach from=$AGROUP.admin_roles item=role %>
            <tr>
                <td><% $role.rl_name %></td>
                <td><% $role.rl_ident %></td>
                <td>
                    <div class="btn-group"><% foreach from=$role.icons item=picon name=cicons %><% $picon %><%/foreach%></div><!-- /.btn-group -->
                </td>
            </tr>
    <%/foreach%>
        </tbody>
    </table>
<%include file="cb.panel.footer.tpl"%>

<%/if%>

<% if ($cmd=='gpo') %>
    <% include file="perm.table.admin.tpl" %>
<%/if%>