<% if ($aktion=='osdone') %>
<h3>Ihr Auftragsnummer lautet: <% $order_obj.AID %></h3>

<%$sheet_obj.t_donemsg%>
 <a href="<%$pdf_cached_request_file%>" title="DOWNLOAD">Antrag JETZT ausdrucken</a>
<% if ($pdf_cached_request_file!="") %>
<script type="text/javascript">
//<![CDATA[
  window.location.href='<%$pdf_cached_request_file%>';
//]]>
</script>
<%/if%>
<%else%>
<form role="form" action="<%$PHPSELF%>" method="POST">
<input type="hidden" name="aktion" value="send">
<input type="hidden" name="page" value="<%$page%>">
{TPL_FORM_TABLE}
<div style="width:100%;text-align:center"><% html_subbtn class="btn btn-primary" value="{LBL_SEND}" %></div>
</form>
<%/if%>
