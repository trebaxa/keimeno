<% if ($ccount>0) %>
        	<table  class="table table-striped table-hover" id="os-table">
        	<thead><tr>
        	 <th>{LBL_FIELDNAME}</th>
        	 <th>{LBL_FIELDTYPE}</th>
        	 <th>W&ouml;rter getrennt mit ";"</th>
        	 <th>Emailfeld</th>
        	 <th>Pflichtfeld</th>
        	 <th>AutoComplete</th>
             <th>Required</th>
        	 <th>Fehlermeldung</th>
        	 <th>Layout Class</th>
        	 <%*<th>Feldl&auml;nge</th>*%>
        	 <th>Joker</th>
        	 <th>Kundenprofil Zuordnung</th>
        	 <th></th>
        	</tr></thead>
            <tbody>
        		<% foreach from=$FIELDS item=osfield name=gloop %>        			
        			<tr>
        				<td><input type="text" class="form-control" size="20" maxlength="100" name="FORM[<%$osfield.FID%>][f_name]" value="<%$osfield.f_name%>"></td>
        				<td>
        				  <select class="form-control custom-select" name="FORM[<%$osfield.FID%>][f_type]">
        				  <% foreach from=$fieldtypes item=ftype %>
        						<option <% if ($ftype==$osfield.f_type) %>selected<% /if %> value="<%$ftype%>"><%$ftype%></option>
        					<% /foreach %>
        					</select>
        				</td>
        				<td>
        				 <% if ($osfield.f_type=='LIST') %>
        				  <input type="text" class="form-control" size="20" name="FORMFIELDLANG[f_list][<%$osfield.FID%>]" value="<%$osfield.f_list%>">
        				  <%else%>
        				  -
        				 <%/if%>
        				</td>
        				<td><input <% if ($osfield.f_isemail==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$osfield.FID%>][f_isemail]"></td>                        
        				<td><input <% if ($osfield.f_force==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$osfield.FID%>][f_force]"></td>
        				<td><input <% if ($osfield.f_autoc==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$osfield.FID%>][f_autoc]"></td>
                        <td><input <% if ($osfield.f_required==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$osfield.FID%>][f_required]"></td>
        				<td><input type="text" class="form-control" size="20" maxlength="100" name="FORMFIELDLANG[f_errmsg][<%$osfield.FID%>]" value="<%$osfield.f_errmsg%>"></td>
        				<td><input type="text" class="form-control" size="10" maxlength="100" name="FORM[<%$osfield.FID%>][f_layoutclass]" value="<%$osfield.f_layoutclass%>"></td>
        			<%*	<td>
        				<% if ($osfield.f_type!='CHECK') %>
        				 <input type="text" class="form-control" size="3" maxlength="2" name="FORM[<%$osfield.FID%>][f_len]" value="<%$osfield.f_len%>">
        				 <%else%>
        				 -
        				<% /if %>
        				</td>
                        *%>
        				<td><%$osfield.joker%></td>
        				<td>
        				<select class="form-control custom-select" name="FORM[<%$osfield.FID%>][f_column]">
        				  <% foreach from=$cust_cols item=coltypesel %>
        						<option <% if ($coltypesel.column==$osfield.f_column) %>selected<% /if %> value="<%$coltypesel.column%>"><%$coltypesel.value%></option>
        					<% /foreach %>
        					</select>
        					</td>
        				<td class="text-right"><%$osfield.icon_del%></td>				
        			</tr>
        			<% /foreach %>
                    </tbody>
        		</table>
                        <%* Tabellen Sortierungs Script *%>
                <%assign var=tablesortid value="os-table" scope="global"%>
                <%include file="table.sorting.script.tpl"%> 	
        <% $btnsave %>
    
    <% else %><br>
        <div class="alert alert-info">F&uuml;r dieses Sheet wurden noch keine Felder hinterlegt.</div>
    <%/if%>