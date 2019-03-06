<% if ($EVENT.edit_form.event.c_icon!="") %>
	<br><img class="img-thumbnail" src="<% $EVENT.edit_form.event.icon %>" >
	<br><a class="json-link" href="<%$eurl%>&cmd=delicon&id=<%$EVENT.edit_form.id%>">{LBL_DELETE}</a>
<%/if%>