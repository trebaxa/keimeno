<% if (count($mdates)>0) %>
<h3>{LBL_OVERVIEWMONTH}</h3>
<table class="table table-striped table-hover" id="otimer1-table" >
<thead>
<tr  >
	<th>Datum</th>
	<th>Zeit</th>	
	<th>Programm</th>
	<th>Kunde</th>
	<th></th>
    </tr>
    </thead>
<% foreach from=$mdates_month item=mdate name=mt %>
    <% include file="otimer.row.tpl" %>
	<% assign var=counter value=$smarty.foreach.mt.iteration %>
<% /foreach %>
 </table> 
 <%* Tabellen Sortierungs Script *%>
        <%assign var=tablesortid value="otimer1-table" scope="global"%>
        <%include file="table.sorting.script.tpl"%> 
<% else %>{LBL_NOITEMS}
<% /if %>

<% if (count($mdates)>0) %>
<h3>{LBL_OVERVIEWYEAR}</h3>
<% include file="otimer.header.tpl" %>
<% foreach from=$mdates item=mdate name=mt %>
    <% include file="otimer.row.tpl" %>
	<% assign var=counter value=$smarty.foreach.mt.iteration %>
<% /foreach %>
</table> 
        <%* Tabellen Sortierungs Script *%>
        <%assign var=tablesortid value="otimer-table" scope="global"%>
        <%include file="table.sorting.script.tpl"%> 
<%else%>        
    {LBL_NOITEMS}
<% /if %>