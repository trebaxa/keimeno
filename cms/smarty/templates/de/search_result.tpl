<h2>{LBL_SEARCHRESULT}</h2>
<% if (count($searchresult)>0) %>
<div class="row">
       <% foreach from=$searchresult item=sitem %>
        <div class="col-md-6">
          <div class="content-box gradient">
            <a href="<%$sitem.url%>" title="<%$sitem.title|sthsc%>"><%$sitem.title%></a><br>
            <%$sitem.content|truncate:300%>
            <div class="text-right">
              <a class="btn btn-default" href="<%$sitem.url%>" title="<%$sitem.title|sthsc%>">lesen...</a>
            </div>
            </div>
        </div>
       <%/foreach%>
</div>
<%else%>
<h2>Keine Treffer</h2>
<%/if%>