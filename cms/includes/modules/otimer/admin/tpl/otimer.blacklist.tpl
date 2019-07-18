<div class="page-header"><h1>Blacklist</h1></div>
<% if (count($blacklist)>0) %>
<table class="table table-striped table-hover" >
	<thead><tr>
		<th>Kunde</th>
		<th>vermisster Termin</th>
		<th>Programm</th>
		<th></th>
	</tr></thead>
	<% foreach from=$blacklist item=blitem name=mt %>
	
	<tr class="<% $sclass %>" >
		<td><a href="kreg.php?kid=<%$blitem.kid%>&aktion=show_edit"><% $blitem.vorname %> <% $blitem.nachname %></a></td>
		<td><% $blitem.dateger %></td>
		<td><% $blitem.prog_title %>
			<td align="right">
				<% $blitem.icon_blockc %>
			</td>	</tr>
			<%/foreach%>
		</table><br>
<div class="alert alert-info">In dieser Tabelle befinden sich alle Kunden, die von Online-Reservierungen ausgeschlossen werden.</div>
<%else%><br>
<div class="alert alert-info">Es wurden keine gesperrten Kunden gefunden.</div>
<%/if%>