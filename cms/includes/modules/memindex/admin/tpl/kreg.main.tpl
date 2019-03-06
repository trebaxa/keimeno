<div class="page-header">
    <h1><i class="fa fa-users"><!----></i> Kunden <% if ($CUSTOMER.kid>0) %><small>{LA_KNR}: <%$GET.kid %> - <% $POBJ.custformatedname %></small><%/if%></h1>
</div><!-- /.page-header -->

<% if ($cmd=="show_edit") %>
      <% if ($CUSTOMER.kid==0 && $REQUEST.kid>0) %>
        <div class="bg-danger">{LA_DIESERKUNDEEXISTIERTN}.</div>
    <%/if%>
  <%include file="memindex.editor.tpl"%> 
<%/if%>
    
<% if ($cmd=='' || $cmd=='showall') %>
    <% if ($cmd=="") %>
          <%include file="memindex.manager.tpl"%>     
    <%/if%>
<%/if%>


<% if ($cmd=="a_simport") %>
    <%include file="memindex.import.tpl"%> 
<%/if%>

<!-- Modal -->
<div class="modal fade" id="addcustomermodal" tabindex="-1" role="dialog" aria-labelledby="addcustomermodalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" class="jsonform" action="<%$PHPSELF%>">            
            <input type="hidden" name="cmd" value="add_customer">
      <div class="modal-header">
        <h5 class="modal-title" id="addcustomermodalLabel">{LA_NEUERKUNDE}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <%include file="kreg.addcustomer.tpl"%>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>