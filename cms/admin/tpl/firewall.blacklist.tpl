<h3>Blacklist</h3>	
<% if (count($FW_LOG)>0) %>
<table class="table table-striped table-hover">
<thead><tr>
	<th>Bereich</th>
	<th>IP</th>
	<th>Zeit</th>
	<th></th>
	<th></th>
	<th></th>
	<th></th>
</tr></thead>
	<% foreach from=$FW_LOG item=litem  %>	 	 	
	  <tr>
	 	<td><% $litem.fw_id %></td>
	 	<td><% $litem.fw_ip %></td>
	 	<td><% $litem.date_ger %></td>
	 	<td><% $litem.fw_calls %></td>
	 	<td><a href="<% $litem.fw_script %>" target="_fw"><% $litem.fw_script %></a></td>
	 	<td><a href="javascript:void(0)" title="<%$litem.fw_ip%> suchen" onClick="GetRequest2InnerHTML('mapsarea','ax_location','run','&epage=<%$epage%>&ip=<%$litem.fw_ip%>','./images/opt_loader.gif')">GeoTracking</a></td>
	 	<td><% $litem.icon_del %></td>
	 </tr>	
	<%/foreach%>
	</table>	<br>
	<br>
<a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=delall">Alle gesperrten IPs entfernen</a> | <a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=clear">Blacklist gem&auml;ss Vorgaben leeren</a>
	<%else%>
	<div class="bg-info text-info">Keine Eintr&auml;ge vorhanden</div>
<%/if%>