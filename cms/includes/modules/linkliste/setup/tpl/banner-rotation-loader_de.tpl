<script type="text/javascript">
//<![CDATA[
   var <%$bannerlistname%> = new Array();
    <% foreach from=$BANNERMATRIXALL key=topid item=bannerlist %><% if ($cmstoplevels.toplobj.TOPID==$topid ) %><% foreach from=$bannerlist.$ban_vertical_pos.$ban_horiz_pos key=topid item=banner name=bannerloop %>
    <% if ($banner.lb_type=='U') %>bannerlist[<%$smarty.foreach.bannerloop.index%>]='<a target="_blank" href="<%$banner.vboutlink%>"><img src="<%$banner.vblink%>" ></a>';<%/if%>
    <% if ($banner.lb_type=='S') %>bannerlist[<%$smarty.foreach.bannerloop.index%>]='<%$banner.lb_script_js%>';<%/if%>         
    <%/foreach%>      
    <%/if%>
<%/foreach%>  
function banner_rotate(<%$bannerlistname%>) {
 var html= <%$bannerlistname%>[Math.floor(Math.random()*<%$bannerlistname%>.length)];
 $('#<%$bannerlistname%>_bannerrotate').html(html);
 var milisek = <% $gbl_config.links_bannertimer %> * 1000;
 window.setTimeout("banner_rotate(<%$bannerlistname%>)",milisek);
}
banner_rotate(<%$bannerlistname%>);
//]]>
</script>
