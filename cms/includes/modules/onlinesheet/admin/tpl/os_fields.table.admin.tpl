<div class="page-header"><h1><i class="fa fa-file-code-o"></i>Online Anträge</h1></div>

<% if ($aktion=='archives') %>
<% if ($archives.count>0) %>
<table class="table table-striped table-hover" id="overture-table" >
	<thead><tr>
	 <th>NR</th>
	 <th>Datum/Zeit</th>
	 <th>Antrag</th>
	 <th>Kunde</th>
	 <th>Kunden Email</th>
	 <th></th>
	</tr></thead>
    <tbody>	
		<% foreach from=$archives.table item=archive %>		
			<tr>
				<td><%$archive.AID%></td>
				<td><%$archive.date_print%></td>
				<td><%$archive.s_name%></td>
				<td><a href="kreg.php?aktion=show_edit&kid=<%$archive.a_kid%>"><%$archive.a_kid%>, <%$archive.nachname%></a></td>
				<td><a href="kreg.php?aktion=show_edit&kid=<%$archive.a_kid%>"><%$archive.a_email%></a></td>
				<td class="text-right"><%$archive.icon_pdf%><%$archive.icon_pdfemail%><%$archive.icon_del%></td>
			</tr>
			<% /foreach %>
            </tbody>
</table>
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="overture-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>   	
<%else%>		
<div class="bg-info text-info">Sie liegen keine Antr&auml;ge vor.</div>
<%/if%>	
<%/if%>	


<% if ($aktion=='showsheets') %>
<h3>{LBL_ONLINESHEET} <% if ($sheetid>0) %> - <% $sheet_obj.s_name %><%/if%></h3>
<br><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=edit&sheetid=0">Neuen Antrag anlegen</a><br><br>
<% if ($scount>0) %>
<form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data" class="form jsonform">
<input type="hidden" name="cmd" value="msavesheet">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="uselang" value="<%$uselang%>">
	<table class="table table-striped table-hover" >
	<thead><tr>
	 <th>Antrag</th>
	 <th>Kunden automatisch registrieren</th>
	 <th>Kunden erh&auml;lt Antrag als PDF Email</th>
	 <th>Antrag in DB speichern</th>
	 <th>Joker</th>
	 <th></th>
	</tr></thead>	
		<% foreach from=$SHEETS item=sheet name=gloop %>
		
			<tr>
				<td><%$sheet.s_name%></td>
				<td><input <% if ($sheet.s_custregister==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$sheet.SID%>][s_custregister]"></td>
				<td><input <% if ($sheet.s_sendpdf==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$sheet.SID%>][s_sendpdf]"></td>
				<td><input <% if ($sheet.s_dbsave==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$sheet.SID%>][s_dbsave]"></td>
				<td><%$sheet.joker%></td>
				<td class="text-right"><%$sheet.icon_edit%><%$sheet.icon_del%></td>
			</tr>
			<% /foreach %>
		</table>	
		<% $btnsave %>
</form>
<%/if%>		
<% if ($scount==0) %>
<div class="bg-info text-info">Bitte legen Sie erst ein Antrag an.</div>
<%/if%>	

<form method="post" class="stdform form-inline" action="<%$PHPSELF%>" enctype="multipart/form-data">
<input type="hidden" name="cmd" value="setautoincre">
<input type="hidden" name="epage" value="<%$epage%>">
<h3>Auftragsnummern - Start</h3>
Start bei:<input type="text" class="form-control" name="FORM[startauto]"><%$subbtn%>
</form>
	
<%/if%>


<% if ($aktion=='edit') %>
<h3>{LBL_ONLINESHEET} <% if ($sheetid>0) %> - <% $sheet_obj.s_name %><%/if%></h3>
<% if ($GBLPAGE.access.language==TRUE)%>
<form class="jsonform form-inline" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
<input type="hidden" name="sheetid" value="<%$sheetid%>">
<input type="hidden" name="cmd" value="msave">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="uselang" value="<%$sheet_obj.t_langid%>">
<input type="hidden" name="FORMSHEETLANG[t_langid]" value="<%$sheet_obj.t_langid%>">

<% if ($sheetid==0) %>
<h3>Anlegen</h3>
<table  class="table table-striped table-hover" width="600">
<tr>
	<td>Title (admin):</td>
	<td><input type="text" class="form-control" name="FORMSHEET[s_name]" value="<%$sheet_obj.s_name%>"></td>
</tr>
</table>
<% $btnsave %>
<%/if%>

<% if ($sheetid>0) %>
Sprache: <%$uselangselect%>
<h3>Felder</h3>
<div class="btn-group"><a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=addfield&sheetid=<%$sheetid%>">Neues Feld anlegen</a></div>
<% if ($ccount>0) %>
	<table  class="table table-striped table-hover" id="os-table">
	<thead><tr>
	 <th>{LBL_FIELDNAME}</th>
	 <th>{LBL_FIELDTYPE}</th>
	 <th>W&ouml;rter getrennt mit ";"</th>
	 <th>Emailfeld</th>
	 <th>Pflichtfeld</th>
	 <th>AutoComplete</th>
	 <th>Fehlermeldung</th>
	 <th>Layout Class</th>
	 <th>Feldl&auml;nge</th>
	 <th>Joker</th>
	 <th>Kundenprofil Zuordnung</th>
	 <th></th>
	</tr></thead>
    <tbody>
		<% foreach from=$FIELDS item=osfield name=gloop %>
			
			<tr>
				<td><input type="text" class="form-control" size="20" maxlength="100" name="FORM[<%$osfield.FID%>][f_name]" value="<%$osfield.f_name%>"></td>
				<td>
				  <select class="form-control" name="FORM[<%$osfield.FID%>][f_type]">
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
				<td><input type="text" class="form-control" size="20" maxlength="100" name="FORMFIELDLANG[f_errmsg][<%$osfield.FID%>]" value="<%$osfield.f_errmsg%>"></td>
				<td><input type="text" class="form-control" size="10" maxlength="100" name="FORM[<%$osfield.FID%>][f_layoutclass]" value="<%$osfield.f_layoutclass%>"></td>
				<td>
				<% if ($osfield.f_type!='CHECK') %>
				 <input type="text" class="form-control" size="3" maxlength="2" name="FORM[<%$osfield.FID%>][f_len]" value="<%$osfield.f_len%>">
				 <%else%>
				 -
				<% /if %>
				</td>
				<td><%$osfield.joker%></td>
				<td>
				<select class="form-control" name="FORM[<%$osfield.FID%>][f_column]">
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
<div class="bg-info text-info">F&uuml;r dieses Sheet wurden noch keine Felder hinterlegt.</div>
<%/if%>


<h3>Formular bearbeiten</h3>
<table class="table table-striped table-hover" width="900">
<tr><td>Formular:<br><%$sheet_obj.fck%></td></tr>
<tr><td>Nachricht an Kunden nach erfolgreichem Absenden:<br><%$sheet_obj.fck_donemsg%></td></tr>
<tr><td>Zus&auml;tzlicher Text (nur sichtbar in PDF)<br><%$sheet_obj.fck_signtext%></td></tr>

</table>
<input type="hidden" name="FORMSHEETLANG[t_sid]" value="<%$sheetid%>">
<%/if%>

<% $btnsave %>
</form>
<% else %>
<%include file="no_permissions.admin.tpl" %>
<%/if%>
<%/if%>
