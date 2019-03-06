
<% if ($VIM.stocktype=='') %>
<form action="<%$PHPSELF%>" method="GET">
<div style="width:400px;">
<fieldset>
<legend>Video Suche - {LA_CHOOSEQUERYSTOCK}</legend>
<% foreach from=$VIM.fix_stocks key=skey item=stock %>	
   <input type="radio" name="stocktype" <% if ($VIM.stocktype==$stock || ($VIM.stocktype=="" && $skey=='YT')) %>checked<%/if%> value="<%$skey%>"><% $stock %><br>
<%/foreach%>

  <input type="hidden" name="section" value="<%$REQUEST.section%>">
  <input type="hidden" name="aktion" value="<%$aktion%>">
  <input type="hidden" name="cmd" value="<%$cmd%>">
  <input type="hidden" name="epage" value="<%$epage%>">
<div class="subright"><%$btngo%></div>
</fieldset>
</div>
</form>
<%/if%>

<% if ($REQUEST.stocktype=='YT') %>
<% include file="video.yt.search.tpl" %>
<%/if%>

<% if ($REQUEST.stocktype=='VI') %>
<% include file="video.vimeo.search.tpl" %>
<%/if%>

