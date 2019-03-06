<%*$FACEBOOKGROUP.feed|echoarr*%>
<% foreach from=$FACEBOOKGROUP.feed item=row %>               
  <% if ($row.g_message!="") %>
  <div class="row">
    <div class="col-md-12">
        <img class="img-responsive" src="<%$row.thumb%>" alt=""> 
        <p><%$row.g_message%></p>
        <a class="btn btn-default" href="<%$row.g_post_link%>" title="">Facebook Post</a>
        <div class="row">
        <% foreach from=$row.images item=img %>               
         <div class="col-md-3"><img class="img-responsive" src="<%$img.src%>" alt=""></div>
        <%/foreach%>
        </div>
    </div>
  </div>
  <%/if%>
<%/foreach%>