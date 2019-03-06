<% if ($cmd=='osdone') %>
<h3>Ihr Auftragsnummer lautet: <% $order_obj.AID %></h3>
 <%$sheet_obj.t_donemsg%><br>
 <div style="text-align:center;margin-top:30px">
     <a href="<%$PHPSELF%>?page=<%$page%>&cmd=showpdf&an=<% $order_obj.AID %>&kid=<%$GET.kid%>&hash=<%$GET.hash%>" target="_pdf" title="ausdrucken">Antrag JETZT ausdrucken</a>
 </div> 
<%else%>
<form action="<%$PHPSELF%>" method="POST">
<input type="hidden" name="cmd" value="send">
<input type="hidden" name="page" value="<%$page%>">
{TPL_FORM_TABLE}
<div style="text-align:center"><% html_subbtn class="sub_btn" value="Auftrag senden" %></div>
</form>
<%/if%>
