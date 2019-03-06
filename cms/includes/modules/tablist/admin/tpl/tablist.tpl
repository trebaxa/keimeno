<div class="page-header"><h1>{LBL_TABELLEN}</h1></div>
<% if $aktion!="calgroups" %>
<div class="btn-group">
     
    <div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
      Gruppe
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
    <% $sel_box %>
    </ul>
    </div>
     <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=newtab&group_id=<% $group.id %>">{LBL_ADD}</a>
</div>
<% /if %>

<% if $aktion=="calgroups" %>
    <%include file="tablist.groups.tpl"%>
<% /if %>

<% if $aktion=="" %>
			<br>		<br>
<form action="<% $PHPSELF %>" method="post" class="form jsonform">
	<input type="hidden" name="aktion" value="save_tab_table">
    <input type="hidden" name="epage" value="<% $epage %>">			
			<table class="table table-striped table-hover" width="560">
<thead><tr>				
	<th>admin. {LBL_TITLE}</th>
	<th>{LBL_EMPLOYEE}</th>
	<th>Layout</th>
	<th></th>
	</tr></thead>
				<% foreach from=$tab_items item=mdate name=mt %>
					<tr>
	<td><% $mdate.tab_name %></td>
	<td><% $mdate.mitarbeiter_name %></td>
	<td><% $mdate.select %></td>
	<td class="text-right">
		<% $mdate.icon_edit %>
		<% $mdate.icon_del %>
		<% $mdate.icon_approve %>
	</td>	
    </tr>
				<% /foreach %>
			</table>
					<% $subbtn %>
			</form>
			<br>
		<br>
<% /if %>


<% if $aktion=="edit" %>
<h3><% $TAB_OBJ.tab_name %></h3>
<% if ($GBLPAGE.access.language==TRUE)%>
<form class="stdform" action="<% $PHPSELF %>" method="post">
	<%$ADMIN.langselect%> 
	<input type="hidden" name="conid" value="<% $TAB_OBJC.id %>">
	<input type="hidden" name="id" value="<% $TAB_OBJ.id %>">
    <input type="hidden" name="epage" value="<% $epage %>"> 
	<input type="hidden" name="FORM_CON[lang_id]" value="<% $TAB_OBJC.lang_id %>">
	<input type="hidden" name="cmd" value="save_tab">
	<div class="form-group">
			<label>admin. {LBL_TITLE}</label>
            <input type="text" class="form-control" value="<% $TAB_OBJ.tab_name %>" name="FORM[tab_name]" >
		</div>		
		<div class="form-group">
			<label>{LBL_TITLE}</label>
            <input type="text" class="form-control" value="<% $TAB_OBJC.title %>" name="FORM_CON[title]" >
	</div>	
		<% $btnsave %>
</form>


<form class="tabform" action="<% $PHPSELF %>" method="post">
	<input type="hidden" name="conid" value="<% $TAB_OBJC.id %>">
	<input type="hidden" name="id" value="<% $TAB_OBJ.id %>">
    <input type="hidden" name="epage" value="<% $epage %>">
	<input type="hidden" name="FORM_CON[lang_id]" value="<% $TAB_OBJC.lang_id %>">
    <input type="hidden" name="lang_id" value="<% $TAB_OBJC.lang_id %>">
	<input type="hidden" name="cmd" value="a_savetab">		
    <br>
<div class="btn-group">    
<a class="btn btn-default" href="javascript:void(0);" onClick="simple_load('tablist','<%$PHPSELF%>?epage=<%$epage%>&aktion=insertrow&id=<% $TAB_OBJ.id %>&uselang=<% $TAB_OBJC.lang_id %>&lang_id=<% $TAB_OBJC.lang_id %>');">Neue Zeile</a>
    <a class="btn btn-default" href="javascript:void(0);" onClick="simple_load('tablist','<%$PHPSELF%>?epage=<%$epage%>&cmd=addcol&id=<% $TAB_OBJ.id %>&uselang=<% $TAB_OBJC.lang_id %>&lang_id=<% $TAB_OBJC.lang_id %>');">Neue Spalte</a>
 <% if ($tabextview!=1) %>
    <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=edit&tabextview=1&id=<% $TAB_OBJ.id %>&lang_id=<% $TAB_OBJC.lang_id %>&uselang=<% $TAB_OBJC.lang_id %>">erweiterte Ansicht</a>
 <%else%>
    <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=edit&tabextview=2&id=<% $TAB_OBJ.id %>&lang_id=<% $TAB_OBJC.lang_id %>&uselang=<% $TAB_OBJC.lang_id %>">normale Ansicht</a>
 <%/if%>		
 </div>
 <br>
		<br>
        <div id="tablist"></div>				

	<% $btnsave %>
	</form>
		<script>
 
 function mup(id) {
     simple_load('tablist','<%$PHPSELF%>?cmd=sortit&up=1&index='+id+'&epage=<%$epage%>&id=<% $TAB_OBJ.id %>&lang_id=<% $TAB_OBJC.lang_id %>');
 }

 function mdown(id) {
    simple_load('tablist','<%$PHPSELF%>?cmd=sortit&up=0&index='+id+'&epage=<%$epage%>&id=<% $TAB_OBJ.id %>&lang_id=<% $TAB_OBJC.lang_id %>');
 }
         
	var toptions = {
		target: '#tablist',   
		type: 'POST',
		forceSync: true,
		success: show_saved_msg 
	};        
   $('.tabform').submit(function() {
		$(this).ajaxSubmit(toptions);
		return false;
	});          
            simple_load('tablist','<%$PHPSELF%>?aktion=list&epage=<%$epage%>&id=<% $TAB_OBJ.id %>&lang_id=<% $TAB_OBJC.lang_id %>');
		</script>
	
<% else %>
    <%include file="no_permissions.admin.tpl" %>
<%/if%>

<% /if %>