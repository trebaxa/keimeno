
<% if ($gallery) %>
<script type="text/javascript" src="<% $PATH_CMS %>js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
<link rel="stylesheet" href="<% $PATH_CMS %>js/fancybox/source/jquery.fancybox.css?v=2.1.4" type="text/css" media="screen" />
<script type="text/javascript" src="<% $PATH_CMS %>js/fancybox/source/jquery.fancybox.pack.js?v=2.1.4"></script>

<!-- Optionally add helpers - button, thumbnail and/or media -->
<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
<link rel="stylesheet" href="<% $PATH_CMS %>js/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
<script type="text/javascript" src="<% $PATH_CMS %>js/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
<script type="text/javascript" src="<% $PATH_CMS %>js/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.5"></script>
<link rel="stylesheet" href="<% $PATH_CMS %>js/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
<script type="text/javascript" src="<% $PATH_CMS %>js/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>


  <h2><% $GAL_OBJ.gallery_name %></h2>

<div class="row">
<% foreach from=$gallery item=foto key=id name=gloop %>
<div class="col-md-4">
 <a class="fancybox" rel="group" href="<% $foto.img_redfullsize %>" title="<% $foto.img_title%><% if ($foto.img_copyright!='') %>&lt;br&gt;Quelle:<% $foto.img_copyright%><%/if%>&lt;br&gt;<% $foto.imginfo.pic_content|hsc%>">
 <img class="img-thumbnail" id="foto<%$id%>" src="<% $foto.img_src %>" alt="Fotos" /></a>
  <div class="fdesc"><p><b><% $foto.img_title%></b><br> <% $foto.imginfo.pic_content|st|truncate:150%></p></div>
</div>
<% /foreach %>
</div>

<div class="clearer"></div>

<script type="text/javascript" charset="utf-8">
$('.infotext').hide();

var hiddenTitle; 
$(".fancybox").hover(function() {
    hiddenTitle = $(this).attr('title'); 
    $(this).attr('title',''); 
}, function() {
    $(this).attr('title',hiddenTitle); 
});  

$(".fancybox").click(function() {
 $(this).attr('title',hiddenTitle); 
});

$(".roundpic").hover(
function () {
   $('#infogal').html($('#info'+$(this).attr('id')).html());
},
function () {
    $('#link'+$(this).attr('id')).css('background-color','transparent');
    }    
);

$(document).ready(function() {
    $(".fancybox").fancybox({
        prevEffect  : 'none',
        nextEffect  : 'none',
        beforeShow: function () {
            if (this.title) {
                this.title += '<br />';
            //    this.title += '<a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-url="' + this.href + '">Tweet</a> ';
             //   this.title += '<iframe src="//www.facebook.com/plugins/like.php?href=http://www.frohnhaeuser-muehle.de&amp;layout=button_count&amp;show_faces=true&amp;width=500&amp;action=like&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frame style="border:none; overflow:hidden; width:110px; height:23px;" allowTransparency="true"></iframe>';
            }
        },
        afterShow: function() {
            twttr.widgets.load();
        },
        helpers : {
            title   : {
                type: 'inside'
            },
            thumbs  : {
                width   : 160,
                height  : 90
            }
        }
    });
});    
</script>

<% /if %>