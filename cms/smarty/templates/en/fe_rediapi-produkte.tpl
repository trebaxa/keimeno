<% if (count($rediapitable)>0) %>
<section>
  <div class="container">
      <h2>Produkte</h2>
      <%*<%$rediapitable|echoarr%>*%>
      <div class="row">
        <% foreach from=$rediapitable item=row name=prolist %>
      	<div class="col-md-3 col-sm-6">
      					<div class="thumbnail">
      						<img src="<%$row.thumb%>" alt="<%$row.pname|sthsc%>" class="img-responsive">
      						<div class="caption text-center">
      							<h3><%$row.pname%></h3>
      							<p><strong><%$row.vk_eur%> €</strong> pro Stück</p>
      							<p class="text-center">
      								<a href="#" class="btn btn-default"><i class="fa fa-shopping-cart"><!-- Placeholder --></i>&nbsp;Bestellen</a>
      							</p>
      					</div>
      			</div>
      	</div>   
      	 <% if ($smarty.foreach.prolist.iteration % 4 == 0)  %></div><div class="row"><%/if%>
        <%/foreach%>
      </div>
    </div>
</section>
<%/if%>