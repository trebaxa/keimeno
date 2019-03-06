<link rel="stylesheet" type="text/css" href="<%$PATH_CMS%>js/plugins/nivo-slider/themes/default/default.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<%$PATH_CMS%>js/plugins/nivo-slider/nivo-slider.css" media="screen" />

<div class="slider-wrapper theme-default">
                            <div class="ribbon"></div>
<div id="themeslider" class="nivoSlider">
<% foreach from=$PAGEOBJ.theme_images item=foto %>
    <img src="<% $foto.thumb %>" alt="<% $foto.img_title %>" title="#themecaption<% $foto.id%>" />
<% /foreach %>
</div>

<% foreach from=$PAGEOBJ.theme_images item=foto %>
  <div id="themecaption<% $foto.id%>" class="nivo-html-caption">
    <div class="slider-desc">
        <div class="hbox">
            <h2><% $foto.img_title %></h2>
            <p><% $foto.imginfo.pic_content %></p>
        </div>
    </div>                            
 </div>
<% /foreach %>
                           
</div>

<script type="text/javascript" src="<%$PATH_CMS%>js/plugins/nivo-slider/jquery.nivo.slider.pack.js"></script>
        <script type="text/javascript">
            $(window).load(function() {
                $('#themeslider').nivoSlider({
                    effect:'<%$PAGEOBJ.t_slideeffect%>',
                    animSpeed:<%$PAGEOBJ.t_animspeed%>,
                    pauseTime:<%$PAGEOBJ.t_pausetime%>,
                    directionNav: true,
                    directionNavHide: true,
                    pauseOnHover: true,
                    boxCols: 8,
                    controlNav: false,
                    boxRows: 4,
                    captionOpacity:1
                });
            });
            $('.nivoSlider').css('box-shadow','none');
</script>
