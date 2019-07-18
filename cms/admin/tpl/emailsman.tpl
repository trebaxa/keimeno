<%include file="cb.panel.header.tpl" icon="far fa-envelope" title="Email Vorlagen Verwaltung" title_addon="`$EM.sess.mod`"%>
<div class="row">
    <div class="col-md-6">
        <div class="btn-group">
            <a class="btn btn-secondary ajax-link" href="<%$PATH_CMS%>admin/run.php?epage=emails.man.inc"><i class="fa fa-table"></i> {LBLA_SHOWALL}</a>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#js-add-eml"><i class="fa fa-plus"></i> {LBL_ADD}</button>
        </div>  
    </div>
    <div class="col-md-6 text-right">
        <label>App:</label><select class="form-control custom-select" onChange="showPageLoadInfo();location.href=this.options[this.selectedIndex].value">           
            <option <% if ($EM.sess.mod=="") %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&mod=-1&cmd=">System</option>
            <% foreach from=$EM.modlist item=row  %>
              <% if ($row.hastpls==true) %> 
                <option <% if ($EM.sess.mod==$row.settings.id) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&mod=<%$row.settings.id%>&cmd="><%$row.settings.module_name%></option>
              <%/if%>
            <%/foreach%>
            </select>
    </div>
</div>

<% if ($cmd=='') %>
<form action="<%$PHPSELF%>" method="post" class="jsonform form-inline">
	<input type="hidden" name="epage" value="<%$epage%>">
<table class="table table-striped table-hover">
		<thead><tr>
		<th>Vorlage</th>
		<th>Absender-Text<br>hinzuf&uuml;gen</th>
		<!-- <th>Kopie an Admin</th> -->
		<th>Kopie an Mitarbeiter</th>
        <th>Absender</th>
		<th></th>
		</tr></thead>
        <tbody>
        <% foreach from=$EM.elist item=row  %>
        <tr>
			<td>
                <input type="hidden" name="FORM[id][<%$row.id%>]" value="<%$row.id%>">
                <input type="text" class="form-control"  name="FORM[title][<%$row.id%>]" value="<%$row.title|sthsc%>">
            </td>
			<td >
                <input type="checkbox" name="FORM[add_adress][<%$row.id%>]" value="1" <% if ($row.add_adress==1) %>checked<%/if%>>
            </td>
			<td>
                <input title="<%$row.mit_emails%>" type="checkbox" name="FORM[mit_in_copy][<%$row.id%>]" value="1" <% if ($row.mit_in_copy==1) %>checked<%/if%>>
            </td>
			<td>
                <select class="form-control custom-select" name="FORM[t_email][<%$row.id%>]">
                    <% foreach from=$EM.emails item=email  %>
                        <option <% if ($email=="<%$row.t_email%>") %>selected<%/if%> value="<%$email%>"><%$email%></option>
                    <%/foreach%>
                </select>
            </td>
            <td class="text-right">
                <div class="btn-group"><% foreach from=$row.icons picon  %><%$picon%><%/foreach%></div>
            </td>
            </tr>
        <%/foreach%>
        </tbody>
        </table>
		<input type="hidden" value="save_etpl_tab" name="cmd">
		<%$subbtn%>
		</form>
<%/if%>        

<!-- Modal -->
<div class="modal fade" id="js-add-eml" tabindex="-1" role="dialog" aria-labelledby="js-add-emlLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<%$PHPSELF%>" method="POST">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="cmd" value="add">
      <div class="modal-header">
        <h5 class="modal-title" id="js-add-emlLabel">Vorlage {LBL_ADD}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
      </div>
      <div class="modal-body">
            <label>Titel der Email:</label>
            <input type="text" class="form-control" value="" name="FORM[title]" placeholder="Titel der Email"><br>
            <label>Module:</label><select class="form-control custom-select" name="FORM[module_id]">           
                        <option <% if ($EM.mailtemplate.module_id=="") %>selected<%/if%> value="">System</option>
                        <% foreach from=$EM.modlist item=row  %>
                        <option value="<%$row.settings.id%>"><%$row.settings.module_name%></option>
                        <%/foreach%>
                        </select>
            <label>Absender:</label>            
            <select class="form-control custom-select" name="FORM[t_email]">
                        <% foreach from=$EM.emails item=email  %>            <option value="<%$email%>"><%$email%></option>            <%/foreach%>
                </select>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         <%$subbtn%>
      </div>
      
    </form>
    </div>
  </div>
</div>


<% if ($cmd=='edit') %>
    <% include file="emailsman.editor.tpl"%>
<%/if%>

<%include file="cb.panel.footer.tpl"%>