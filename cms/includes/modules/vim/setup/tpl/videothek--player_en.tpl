<a href="<% $HTA_CMSFIXLINKS.BAAAA_URL %>">zur Videothek</a>
<div class="clear"></div>
<h1><%$VIM.video.v_videotitle%></h1>

<script type='text/javascript' src='<%$PATH_CMS%>js/jwplayer/jwplayer.js'></script>
<script type='text/javascript' src='<%$PATH_CMS%>js/jwplayer/swfobject.js'></script>

<div style="width:471px;float:left;">
<div id="jw_containter">Player startet...</div>
</div>
<div style="width:360px;float:left;margin-left:10px;">
<div class="bluebox">
<h2><%$VIM.video.v_videotitle%></h2>
Dauer:<%$VIM.video.v_duration %>
<h3>Beschreibung</h3>
<%$VIM.video.v_videodescription %>
</div>
</div>

<div class="clear"></div>

<script type='text/javascript'>
function flashLoaded(e) {
 jwplayer(e.ref).onComplete(function() {  });
} 
var params = { wmode:'transparent', allowfullscreen:'true', allowscriptaccess:'always' };
var attributes = { id:'v-player', name:'v-player' };
</script>

<% if ($VIM.video.v_stock=='YT') %>
<script type='text/javascript'>
var flashvars = { file:'<% $VIM.video.v_watchpageurl %>', autostart:true };
swfobject.embedSWF('<%$PATH_CMS%>js/jwplayer/player.swf', 'jw_containter', 470, 320, '9.0.115', 'false',
 flashvars, params, attributes, flashLoaded);
</script>
<%/if%>

<% if ($VIM.video.v_stock=='VI') %>
<script type='text/javascript'>
var flashvars = { type:'<%$PATH_CMS%>js/jwplayer/vimeo.swf',file:'<% $VIM.video.v_watchpageurl %>', autostart:true };
swfobject.embedSWF('<%$PATH_CMS%>js/jwplayer/player.swf', 'jw_containter', 470, 320, '9.0.115', 'false',
 flashvars, params, attributes, flashLoaded);
</script>
<%/if%>
