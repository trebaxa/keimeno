<% if (count($BANNERMATRIXRAND )>0) %>
<% assign var=bannerlistname value="bannerlist" %>
<% assign var=ban_vertical_pos value="C" %>
<% assign var=ban_horiz_pos value="T" %>
<div style="text-align:center">
<% foreach from=$BANNERMATRIXRAND key=topid item=bannerlist %>
    <% if ($cmstoplevels.toplobj.TOPID==$topid ) %>          
         <div id="<%$bannerlistname%>_bannerrotate"> </div>
    <%/if%>
<%/foreach%>
</div>


<% include file="banner_rotation_loader.tpl" %>

<%/if%>
