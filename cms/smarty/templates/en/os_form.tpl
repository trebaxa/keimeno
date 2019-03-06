<% if ($cmd=='osdone') %>
   <h3>Ihr Auftragsnummer lautet: <% $order_obj.AID %></h3>
   <div class="row">
     <div class="col-md-12 text-center">
       <%$sheet_obj.t_donemsg%>
     </div> 
   </div> 
<%else%>
  <form role="form" action="<%$PHPSELF%>" method="POST">
    <input type="hidden" name="cmd" value="send_os_sheet">
    <input type="hidden" name="page" value="<%$page%>">
      {TPL_FORM_TABLE}
      <div class="row">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Auftrag senden</button>
      </div>
    </div>
  </form>
<%/if%>