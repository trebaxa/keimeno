<%*$socialmediastream|echoarr*%>
<section>
  <div class="container">
    <div class="main-title text-center centered">
      <h2 class="grey">Redimero Aktuell</h2>
    </div>
      <div class="row">
          <% foreach from=$socialmediastream item=row name=redifbloop %>
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading">
                <div class="row">
                <div class="col-md-6 <% if ($row.socialtype=='flickr') %>flickr<%/if%><% if ($row.socialtype=='fb') %>facebook<%/if%><% if ($row.socialtype=='tw') %>twitter<%/if%>-bg">
                  <h4><i class="fa fa-<% if ($row.socialtype=='flickr') %>flickr<%/if%><% if ($row.socialtype=='fb') %>facebook<%/if%><% if ($row.socialtype=='tw') %>twitter<%/if%>">&nbsp;</i>
                  <% if ($row.socialtype=='flickr') %>flickr<%/if%><% if ($row.socialtype=='fb') %>facebook<%/if%><% if ($row.socialtype=='tw') %>twitter<%/if%></h4>
                </div>
                <div class="col-md-6 text-right">
                  <% if ($row.beforexmin<60) %>
                      vor <%$row.beforexmin%> Minuten
                  <%/if%>
                  <% if ($row.beforexhours<24) %>
                      vor <%$row.beforexhours%> Stunden
                  <%/if%>
                 
                  <% if ($row.beforexdays<=31) %>
                      vor <%$row.beforexdays%> Tagen
                  <%/if%>  
                  <% if ($row.beforexmonths>=1) %>
                      vor <%$row.beforexmonths%> Monate(n)
                  <%/if%>          
                  </div>
                </div>  
              </div>
              
              <div class="panel-body" style="min-height: 550px;">
               <% if ($row.isvideo==1) %>
                <% if ($row.video_type=='FB') %>
                 <iframe src="https://www.facebook.com/plugins/video.php?href=<%$row.video_url%>&width=640&show_text=false&appId=<%$FBWP.WP.fb_appid%>&height=480" width="100%" height="240" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
                <%/if%>
                <% if ($row.video_type=='YT') %>
                 <iframe width="100%" height="240" src="<%$row.source%>" frameborder="0" allowfullscreen></iframe>
                <%/if%> 
               <%else%>
                <figure><a href="<%$row.post_link%>" title="<%$row.text|sthsc|truncate:30%>">
                  <% if ($row.thumb!="") %><img src="<%$row.thumb%>" alt="<%$row.text|sthsc|truncate:30%>" class="img-responsive img-hover mb-sm"><%/if%>
                <%/if%>
                
                  <%*<h3><%$row.story%></h3>*%>
                <p><%$row.text|nl2br|truncate:600%></p>
              </div>
              
              <div class="panel-footer">
                <div class="text-right"><a target="_blank" href="<%$row.post_link%>" class="btn btn-default btn-xs" title="<%$row.text|sthsc|truncate:30%>">...mehr</a></div>
              </div>
              
            </div>
            </div>   
          <% if $smarty.foreach.redifbloop.iteration is div by 3 %></div><div class="row"><%/if%>  
        <%/foreach%>
      </div>
    </div>
</section>