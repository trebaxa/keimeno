<%include file="cb.panel.header.tpl" title="{LBLA_CONFIRMATION}"%>
<table class="table table-striped table-hover" >
    <tr><td>{LBLA_SENDER}</td><td><% $gbl_config.news_senderemail%></td></tr>
    <tr><td>{LBLA_SUBJECT}</td><td><% $NEWSLETTER.newsedit.e_subject%></td></tr>
    <tr><td>{LBLA_SALUTATION_MALE}:</td><td><% $NEWSLETTER.newsedit.e_anrede_m%></td></tr>
    <tr><td>{LBLA_SALUTATION_FEMALE}:</td><td><% $NEWSLETTER.newsedit.e_anrede_w%></td></tr>
    <tr><td>{LBLA_CONTENT}:</td><td><iframe style="border:1px solid #000000" src="<% $NEWSLETTER.previewlink %>" width="99%" height="300" name="news_preview" scrolling="yes" marginheight="0" marginwidth="0" frame target="_self" class="thumb"></iframe></td></tr>
        <tr><td>{LBLA_CONTENT_TYPE}:</td><td><% $NEWSLETTER.format%></td></tr>
    <tr><td>{LBLA_RECIPIENT}:</td><td><% $NEWSLETTER.newsedit.groupname%></td></tr>
    <tr><td>Aktive Emails:</td><td><% $NEWSLETTER.active_count%></td></tr>
    <% if (count($NEWSLETTER.attachments)>0) %>
    <tr><td>Email Anh&auml;nge:</td><td>
        <div class="form-group">             
                <label>Attachments:</label>
            <ul>
            <% foreach from=$NEWSLETTER.attachments item=row %>	
                <li><a target="_blank" href="<%$row.relativefile%>"><%$row.bafile%> <%$row.fs%></a></li>
            <%/foreach%>
            </ul>              
            
            </div>
    
    </td></tr>
    <%/if%>
    </table>
  <div class="text-center">
  	<form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
		<input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="aktion" value="START_SEND">
		<input type="hidden" name="roundzero" value="1">
		<input type="hidden" name="id" value="<%$POST.id%>">
		<input type="submit" class="btn btn-primary" value="NEWSLETTER SENDEN">
    </form>
  </div>
   <%include file="cb.panel.footer.tpl"%> 