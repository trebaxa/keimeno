<% if (count($selected_item.comments)>0) %>
      <% foreach from=$selected_item.comments item=row %>
        <div class="row">
          <div class="col-md-6"><%$row.c_autor%></div>
          <div class="col-md-6 text-right"><%$row.c_time|date_format:"%d:%m:%Y"%></div>
        </div>  
        <div class="row">
          <div class="col-md-12"><%$row.c_comment|sthsc%><hr></div>
        </div>
      <%/foreach%>
    <%/if%>