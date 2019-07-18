<%include file="cb.page.title.tpl" icon="" title="Status"%>

 <div style="width:600px">
<fieldset>	
<table  >
<tr>
	<td>Status:
		</td><td><%$STATUSBAR.done%> Elemente von <%$STATUSBAR.total%> bearbeitet.
	</td>
</tr>	
<tr>
	<td>
Bisherige Dauer:</td><td><%$STATUSBAR.timediffmin%> min
	</td>
</tr>	
<tr>
	<td>
Gesch&auml;tzte Verarbeitungszeit:</td><td> <%$STATUSBAR.totaltime%> min
	</td>
</tr>	
<tr>
	<td>
Gesch&auml;tzte Restzeit:</td><td> <%$STATUSBAR.resttime%> min
	</td>
</tr>
</table>
<div style="margin-top:10px;width:100%;height:16px;border:1px solid #303030;background-color:#E3E3E2;">
		<div style="width:<%$STATUSBAR.procent|round:2%>%;background-color:#A0282A;height:100%;text-align:center;color:white;font-style:bold;">
		<%$STATUSBAR.procent|round:2%>%
		</div>
</div>
 </fieldset>	
 </div>
 
<br><br><h3>bitte warten...</h3>