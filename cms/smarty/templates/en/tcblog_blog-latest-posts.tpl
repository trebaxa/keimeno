<section class="gray">
    	<div class="container">
    		<div class="section-title">
    			<h2>Blog - Aktuell</h2>
    		</div>
    		<div class="row">
    		  <% foreach from=$latest_blog_items item=row name=loop %>
      		  <div class="col-md-4">
    					<div class="panel panel-blue">
    						<div class="panel-heading text-center">
    							<%$row.title%>
    						</div>
    						<div class="panel-body">
    						  <% if ($pinitem.image_exists==true) %>
                  <div class="post-image">
                     <a href="<%$row.detail_link %>"><img class="zoomin img-thumbnail" src="<%$PATH_CMS%>file_data/tcblog/<%$pinitem.b_image%>" alt="<% $row.title|sthsc %>"></img></a>
                  </div>
                  <%/if%>
    							<p class="text-justify"><% if ($row.introduction!="") %><%$row.introduction%><%else%><% $row.content|st|truncate:360 %><%/if%></p>
    						</div>
    						<div class="panel-footer text-center">
    							<a href="<%$row.detail_link%>" title="<%$row.title|sthsc%>" class="btn btn-blue">weiterlesen</a>
    						</div>
    					</div>
      			</div>
    		  <%/foreach%>
    		</div>
</section>