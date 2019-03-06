    <h3><%$EVENT.group.groupname%></h3>
	<form action="<%$PHPSELF%>" method="post" class="form jsonform">
    
	<div class="form-group">
	   <label>{LBL_LANGUAGE}: </label>
       <%$EVENT.langsel%>
    </div>   
	<div class="form-group">
	   <label>admin. {LBL_TITLE}</label>
       <input type="text" class="form-control" value="<%$EVENT.group.groupname|sthsc%>" name="FORM[groupname]">
    </div>
	<div class="form-group">
	   <label>{LBL_TITLE}</label>
       <input type="text" class="form-control" value="<%$EVENT.group_content.g_title|sthsc%>" name="FORM_CON[g_title]">
    </div>
	
    
    <h3>Sichtbar f&uuml;r</h3>
    <%$EVENT.perm_checkoxes%>

	<input type="hidden" name="tid" value="<%$GET.id%>">
	<input type="hidden" name="epage" value="<%$epage%>">
	<input type="hidden" name="conid" value="<%$FORM_CON.id%>">
	<input type="hidden" name="FORM_CON[lang_id]" value="<%$EVENT.langid%>">
	<input type="hidden" name="cmd" value="setallperm">
        <%$subbtn%></form> 