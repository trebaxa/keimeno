<tr>
	<td><% $mdate.timefrom.date_ger %></td>
	<td><% $mdate.timefrom.time.formatedtime %>-<% $mdate.timeto.time.formatedtime %>
	<br><span class="small">Dauer: <% $mdate.duration_min %> min</span></td>	
	<td><a href="run.php?epage=<%$epage%>&aktion=otprograms_edit&id=<%$mdate.PROGID%>"><% $mdate.prog_title %></a>
	<br><span class="small"><% $mdate.prog_employee %></span></td>	
	<td><a href="kreg.php?aktion=show_edit&kid=<%$mdate.CUSTID%>"><% $mdate.nachname %>,<% $mdate.vorname %></a>
	<br><a href="mailto:<% $mdate.KEMAIL %>"><% $mdate.KEMAIL %></a></td>
	<td class="text-right">
        <div class="btn-group">
		<% $mdate.icon_edit %>
		<% $mdate.icon_del %>
		<% $mdate.icon_approve %>
		<% $mdate.icon_clone %>
		<% $mdate.icon_missed %>		
		<% $mdate.icon_block %>
		<% $mdate.icon_comment %>
        </div>
	</td>	</tr>
