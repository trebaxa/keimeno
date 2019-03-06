<script src="<%$PATH_CMS%>includes/modules/jtagcloud/js/jquery.tagcanvas.min.js" type="text/javascript"></script>
<h3>Tagcloud</h3>
<div id="myCanvasContainer">
 <canvas width="300" height="300" id="myCanvas"></canvas>
</div>
 
<div id="tags">
 <ul>
 <% foreach from=$jtagcloud.webpages item=row %>
    <li><a href="<%$row.link_url%>" target="_blank"><%$row.link_label%></a></li>
  <%/foreach%>
 </ul>
</div>

<script type="text/javascript">
 $(document).ready(function() {
   if( ! $('#myCanvas').tagcanvas({
             <% foreach from=$jtagcloud.tagoptions item=row key=optkey %>
        <% if ($row=='false' || $row=='true' || $optkey=='initial') %>
        <%$optkey%>:<%$row%>,
         <%else%><% if ($row!="") %><%$optkey%>:'<%$row%>',<%/if%><%/if%> 
      <%/foreach%>

   },'tags')) {
     $('#myCanvasContainer').hide();
   }
 });
 
 </script>
