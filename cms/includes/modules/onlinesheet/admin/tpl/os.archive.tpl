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
        				<td class="text-right"><div class="btn-group"><%$archive.icon_pdf%><%$archive.icon_pdfemail%><%$archive.icon_del%></div></td>
        			</tr>
        			<% /foreach %>
                    </tbody>
        </table>
        <%* Tabellen Sortierungs Script *%>
        <%assign var=tablesortid value="overture-table" scope="global"%>
        <%*include file="table.sorting.script.tpl"*%>   	
    <%else%>		
        <div class="alert alert-info">Sie liegen keine Antr&auml;ge vor.</div>
    <%/if%>	