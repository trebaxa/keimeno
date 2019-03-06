<% if ($gbl_config.fb_socialnetwork==1) %>
<div id="share-content">
    <div class="twitter-button float-left"><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="<% $gbl_config.tw_screenname %>">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
    <div id="fb-ilike-button" class="facebook-like-button float-left">

    </div>          
  <div class="tc_clear"></div>
</div>

<div id="fb-root"></div>

<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
<script type="text/javascript">
//<![CDATA[

<% if ($gbl_config.fb_show_comments==1) %>
 $('#fb-root').html('<fb:comments href="http://www.facebook.com/pages/<% $gbl_config.fb_screenname %>/<% $gbl_config.fb_profilid %>" num_posts="8" width="470"></fb:comments>');
<%/if%>

<% if ($gbl_config.fb_show_ilike==1) %> 
    <% if ($gbl_config.fb_ilike_connecttofb==1) %> 
         $('#fb-ilike-button').html('<fb:like href="http://www.facebook.com/pages/<% $gbl_config.fb_screenname %>/<% $gbl_config.fb_profilid %>" send="true" layout="button_count" show_faces="false" font="verdana"></fb:like>');
    <%else%> 
         $('#fb-ilike-button').html('<fb:like href="" send="true" layout="button_count" show_faces="false" font="verdana"></fb:like>');
    <%/if%>
<%/if%>
//]]>
</script>
<%/if%>
