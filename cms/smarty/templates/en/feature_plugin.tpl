<section class="bg-primary">
        <div class="container">
          <div class="page-header">
            <h2><%$feature.fg_name%></h2>
          </div>
          <div class="row">
            <div class="col-md-4">
             <% foreach from=$feature.table item=row name=featureloop %>
              <div class="media">
                <div class="pull-left">
                <% if ($row.f_icon!="") %>
                <span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x text-primary"></i><i class="fa fa-<%$row.f_icon%> fa-stack-1x fa-inverse"></i></span>
                <%/if%>
                <% if ($row.f_image!="" && $row.f_icon=="") %>
                    <img src="<%$row.thumb%>" class="img-tumbnail">
                <%/if%>
                </div>
                <div class="media-body">
                  <h4><%$row.f_title%></h4>
                  <p><%$row.f_text%></p>
                </div>
              </div>
              <% if ($smarty.foreach.featureloop.iteration % 3 == 0 )%></div><div class="col-md-4"><%/if%>
              <%/foreach%>
            </div>
          </div>
        </div>
      </section>