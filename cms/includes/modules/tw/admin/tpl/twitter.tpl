<div class="page-header"><h1><i class="fab fa-twitter"></i>Twiiter</h1></div>
<% if ($aktion=="" || $section=='start') %>

<% if ($TW.twuser.id!="") %>
<h3>Status Update</h3>
<form action="<%$PHPSELF%>" method="POST" class="jsonform form-inline">
<input type="text" class="form-control" value="" name="FORM[status]"> <input type="submit" class="btn btn-primary" value="{LBL_SEND}">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="cmd" value="send_tw_msg">
</form>
<%/if%>

<h3>Account Info</h3>
<table class="table table-striped table-hover">
<tr>
	<td>ID:</td>
	<td><% $TW.twuser.id %></td>
</tr>	
<tr>
	<td>Name:</td>
	<td><a target="_tw"  href="http://twitter.com/#!/<% $TW.twuser.screen_name %>"><% $TW.twuser.name %></a></td>
</tr>	
<tr>
	<td>Beschreibung:</td>
	<td><% $TW.twuser.description %></td>
</tr>	
<tr>
	<td>Followers Count:</td>
	<td><% $TW.twuser.followers_count %></td>
</tr>	
<tr>
	<td>Friends Count:</td>
	<td><% $TW.twuser.friends_count %></td>
</tr>		
<tr>
	<td>Created at:</td>
	<td><% $TW.twuser.created_at %></td>
</tr>
<tr>
	<td>Lists Count:</td>
	<td><% $TW.twuser.listed_count %></td>
</tr>
<tr>
	<td>Profil Image:</td>
	<td><img src="<% $TW.twuser.profile_image_url %>" class="img-thumbnail"></td>
</tr>

</table>

<table class="table table-striped table-hover">
<% foreach from=$TW.timeline item=row %>
<tr>
    <td><%$row.user.name%></td>
    <td><%$row.twdate%></td>
    <td><%$row.text%></td>
</tr>
<%/foreach%>
</table>     

<%/if%>

<% if ($aktion=='tw_post_confirm' || $aktion=='') %>
<% if ($TW.connected==TRUE) %>
<form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
<div style="width:600px">
<fieldset>	
<legend>{LA_SENDTOTWITTER}</legend>
<%$TWO.org_txt%>
<textarea class="form-control" name="FORM[twstatus]" rows="3" cols="91" onKeyPress="return taLimit(this,140,event)" onKeyUp="return taCount(this,'myCounter',140)"><%$TWO.txt|sthsc%></textarea>
	<br>{LA_YOUHAVE} <B><SPAN id="myCounter">140</SPAN></B> {LA_CHARACTERSREMAININGFO}
	 	<br><script>tae=document.getElementById('mdesc');taCount(tae,'myCounter',140);</script>
<div class="subright"><%$sendbtn%></div>
</fieldset>	
</div>
  <input type="hidden" name="aktion" value="tw_post_status">
  <input type="hidden" name="comingfrom" value="<%$REQUEST.comingfrom%>">
	<input type="hidden" name="epage" value="<%$epage%>">
</form>
<%else%>
	


<%/if%>


<% if (count($tw_list)>0) %>
<h3>Twitter Listen</h3>
 <table class="table table-striped table-hover" >
 <thead><tr>
 	<th>Icon</th>
 	<th>List Name</th>
 	<th>Link</th>
 	<th>Subscriber</th>
 	<th>Mitglieder</th>
 	<th>Mode</th>
 </tr></thead>
 		<% foreach from=$tw_list item=twlist %>         
 		 	<tr>
            	<td><img src="<% $twlist.USER.PROFILE_IMAGE_URL %>"  width="48" height="48"></td>
            	<td><% $twlist.FULL_NAME %><br><span class="small"><%$twlist.DESCRIPTION%></span></td>
            	<td><a target="_tw" href="http://www.twitter.com<%$twlist.URI%>">http://www.twitter.com<%$twlist.URI%></a></td>
            	<td><% $twlist.SUBSCRIBER_COUNT %></td>
            	<td><% $twlist.MEMBER_COUNT %></td>
            	<td><% $twlist.MODE %></td>
         </tr>
  <%/foreach%>
  </table>
<%/if%>
<%/if%>




<% if ($section=='modstylefiles') %>
 <% include file="modstylefiles.tpl"%>
<%/if%>

<% if ($section=='conf') %>

<div class="btn-group">
<% if ($gbl_config.tw_oauth_token_secret=="") %>
<a class="btn btn-secondary" title="Connect to Twitter" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=request_token">Request Token</a> 
<%/if%> 
<a class="btn btn-secondary" href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps</a>
</div>
<% if ($gbl_config.tw_oauth_token_secret!="sss") %>
{LA_SIEMSSENSICHERSTMITTW}
<%/if%> 

<div class="page-header"><h1>{LA_MODCONFIGURATION}</h1></div><%$TW.CONFIG%>
<%/if%>