<div class="wio">
<h3>Wer ist online</h3>
<% foreach from=$whoislonline item=wio name=wioloop %>
<% if ($wio.username) %><% $wio.username %><% else %>{LBL_GUEST}<%/if%>
<% if ($smarty.foreach.wioloop.last==false) %>, <%/if%>
<%/foreach%>
</div>