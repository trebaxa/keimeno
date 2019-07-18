    <h3>{LBL_ONLINESHEET} <% if ($sheetid>0) %> - <% $sheet_obj.s_name %><%/if%></h3>
    <div class="btn-group">
        <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=edit&sheetid=0">Neuen Antrag anlegen</a><br><br>
    </div>    
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
        	 <th></th>
        	</tr></thead>	
        		<% foreach from=$SHEETS item=sheet name=gloop %>        		
        			<tr>
        				<td><%$sheet.s_name%></td>
        				<td><input <% if ($sheet.s_custregister==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$sheet.SID%>][s_custregister]"></td>
        				<td><input <% if ($sheet.s_sendpdf==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$sheet.SID%>][s_sendpdf]"></td>
        				<td><input <% if ($sheet.s_dbsave==1) %>checked<%/if%> type="checkbox" value="1" name="FORM[<%$sheet.SID%>][s_dbsave]"></td>        				
        				<td class="text-right"><div class="btn-group"><%$sheet.icon_edit%><%$sheet.icon_del%></div></td>
        			</tr>
        			<% /foreach %>
        		</table>	
        		<% $btnsave %>
        </form>
    <%/if%>		
    
    <% if ($scount==0) %>
        <div class="alert alert-info">Bitte legen Sie erst ein Antrag an.</div>
    <%/if%>	
    
    <form method="post" class="stdform form-inline" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <input type="hidden" name="cmd" value="setautoincre">
        <input type="hidden" name="epage" value="<%$epage%>">
        <h3>Auftragsnummern - Start</h3>
        <div class="form-group">
            <label>Start bei:</label>
            <input type="text" class="form-control" name="FORM[startauto]"><%$subbtn%>
        </div>    
    </form>