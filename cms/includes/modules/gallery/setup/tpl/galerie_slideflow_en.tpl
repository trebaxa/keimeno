<% if ($gallery) %>
  <style type="text/css">
   @import url(<% $PATH_CMS %>js/plugins/tinyslideshow/style.css);
</style>


    <ul id="slideshow">
    <% foreach from=$gallery item=gal_image name=gal1 %>
        <li>
            <h3><% $gal_image.img_title%></h3>
            <span><% $gal_image.img_redfullsize %></span>
            <p><% $gal_image.img_description%></p>
            <a href="#"><img src="<%$gal_image.img_src%>" alt="<% $gal_image.img_title%>"></a>
        </li>
   <% /foreach %> 
    </ul>
    <div id="wrapper">
        <div id="fullsize">
            <div id="imgprev" class="imgnav" title="Previous Image"></div>
            <div id="imglink"></div>
            <div id="imgnext" class="imgnav" title="Next Image"></div>
            <div id="image"></div>
            <div id="information">
                <h3>&nbsp;</h3>
                <p>&nbsp;</p>
            </div>
        </div>
        <div id="thumbnails">
            <div id="slideleft" title="Slide Left"></div>
            <div id="slidearea">
                <div id="slider"></div>
            </div>
            <div id="slideright" title="Slide Right"></div>
        </div>
    </div>
    
    <!-- http://www.leigeber.com/2008/12/javascript-slideshow/ -->    
<script type="text/javascript" src="<% $PATH_CMS %>js/plugins/tinyslideshow/compressed.js"></script>
<script type="text/javascript">
//<![CDATA[
    $('slideshow').style.display='none';
    $('wrapper').style.display='block';
    var slideshow=new TINY.slideshow("slideshow");
    window.onload=function(){
        slideshow.auto=true;
        slideshow.speed=5;
        slideshow.link="linkhover";
        slideshow.info="information";
        slideshow.thumbs="slider";
        slideshow.left="slideleft";
        slideshow.right="slideright";
        slideshow.scrollSpeed=4;
        slideshow.spacing=5;
        slideshow.active="#000000";
        slideshow.init("slideshow","image","imgprev","imgnext","imglink");
    }
//]]>
</script>

<%/if%>
