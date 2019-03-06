<% if ($gallery) %>
  <style type="text/css">
   @import url(<% $PATH_CMS %>js/images/milkbox/milkbox.css);
   @import url(<% $PATH_CMS %>js/images/mooflow/MooFlow.css);

</style>
    
    <script type="text/javascript" src="<% $PATH_CMS %>js/mootools-1.2-core.js"></script>
    <script type="text/javascript" src="<% $PATH_CMS %>js/mootools-1.2-more.js"></script> 
    <script type="text/javascript" src="<% $PATH_CMS %>js/MooFlow.js"></script>
    <script type="text/javascript" src="<% $PATH_CMS %>js/milkbox.js"></script>

<% assign var=startindex value=$gallery_picount/2 %>

<script type="text/javascript">
var myMooFlowPage = {

    start: function(){
        /* MooFlow instance with the Milkbox Viewer */
        var mf = new MooFlow($('MooFlow'), {
                                startIndex: <% $startindex %>,      
                                bgColor: "#FFFFFF",
                                heightRatio: 0.4,
                                offsetY: -80,
                                factor: 100,
        useSlider: true,
        useCaption: true,
        useMouseWheel: true,
        useKeyInput: true,
        useViewer: true,
                                onClickView: function(obj){
                Milkbox.showThisImage(obj.href, obj.title + ' - ' + obj.alt);
            }

            
        }); 
    }
    
};

window.addEvent('domready', myMooFlowPage.start);
</script>

<div id="MooFlow">
<% foreach from=$gallery item=gal_image name=gal1 %>
<a rel="milkbox[gall<% $gallery_id %>]" href="<% $gal_image.img_redfullsize %>" title="<% $gal_image.img_title %>">
<img title="<% $gal_image.img_title %>" alt="<% $gal_image.img_description %>" src="<% $gal_image.img_src %>" >
</a>
<% /foreach %>
</div>
Doppel Klick auf Bild
<% /if %>