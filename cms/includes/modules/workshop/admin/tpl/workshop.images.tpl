<% if (count($WORKSHOP.ws.images)>0) %>
    <h3>Bilder des Workshops</h3>
    <div class="row">
        <% foreach from=$WORKSHOP.ws.images name=gloop item=img %>
        <div class="col-md-4">
          <a href="javascript:void(0)" title="löschen" onclick="delete_img(<%$WORKSHOP.ws.id%>,'<%$img%>')"><i style="position:absolute;top:0px;right:0px;" class="fa fa-trash bg-danger text-danger"></i></a>
          <img alt="<%$img|sthsc%>" src="../file_data/workshop/<%$img%>" class="img-responsive" />
        </div>
         <% if ($smarty.foreach.gloop.iteration % 3 == 0 )%></div><div class="row"><%/if%>
        <%/foreach%>
    </div>
<%/if%>