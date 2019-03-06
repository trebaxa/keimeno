<div style="width:1000px">
<fieldset>	
<legend>Video Kategorien</legend>
<form action="<%$PHPSELF%>" method="post" name="catform">		
		<input type="hidden" value="<% $REQUEST.starttree %>" name="starttree">
		<input type="hidden" value="<% $GET.tmsid %>" name="tmsid">		
		<input type="hidden" value="save_cat_table" name="aktion">
		<input type="hidden" value="<%$epage%>" name="epage">
		<input type="hidden" value="<%$section%>" name="section">
		
	Text filter:<input id="wlu_wordfilter" type="text" class="form-control" value="<%$GET.wlu_wordfilter%>" name="wlu_wordfilter"><a href="#" onClick="wlucat_jumb();"><img style="margin-left:3px;" src="./images/arrow_right.png" ></a>  
	<script>
	function wlucat_jumb() {	 
		location.href='<%$PHPSELF%>?wlu_wordfilter='+$('#wlu_wordfilter').val()+'&aktion=<%$aktion%>&starttree=<%$GET.starttree%>&epage=<%$epage%>&section=<%$section%>&countryid=<%$GET.countryid%>&employeeid=<%$GET.employeeid%>';
	}
	</script>
	<table class="table table-striped table-hover"   >
	<tr>
		<td valign="top" style="width:200px;border-right:3px solid #898057;">
		<div id="adminmenu">
			<div class="vim_scrollbar">
				<% $VIM.admin_tree %>
			</div>
		</div>

		</td>
		<td valign="top" id="cat-table">
		<% if (count($VIM.cat_table)>0) %>

<div class="subright"><% $subbtn %></div>

		<table  class="table table-striped table-hover">
		 <thead><tr>
		 <th></th>
		 <th><a href="<%$PHPSELF%>?order=name&direc=<% if ($GET.direc!='ASC') %>ASC<%else%>DESC<%/if%>&epage=<%$epage%>&section=<%$section%>&aktion=<%$aktion%>&msid=<%$GET.msid%>">Admin. {LBLA_DESCRIPTION}</a></th>
		 <th><a href="<%$PHPSELF%>?order=videocount&direc=<% if ($GET.direc!='ASC') %>ASC<%else%>DESC<%/if%>&epage=<%$epage%>&section=<%$section%>&aktion=<%$aktion%>&msid=<%$GET.msid%>">Video Count</a></th>

		 <th>Sort.</th>
		 <th></th>
		 <th></th>
         <th></th>
		 </tr></thead>
	<% foreach from=$VIM.cat_table item=ws %>	 
	
	<tr>
			<td><% if ($ws.childcount>0) %>
				<a title="{LA_KATEGORIEFFNEN}: <% $ws.name|sthsc %>" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=showall&section=cats&starttree=<% $ws.id %>">
			<img src="./images/folder_open.png" ></a>
			<%else%>
			 <img src="./images/folder_doc.png" >
			<%/if%>
			</td>
			<td><% $ws.ytc_name %><input type="hidden" name="CATS[<% $ws.id %>][id]" value="<% $ws.id %>"></td>
			<td><% $ws.ytc_videocount %>/<% $ws.ytc_videocounttotal %></td>
	
			
			<td>
				<input type="text" class="form-control" id="it<% $ws.id %>" size="3" name="CATS[<% $ws.id %>][ytc_order]" value="<% $ws.ytc_order %>">
			</td>
			<td>
				<input type="image" onClick="moveup(<% $ws.id %>);" class="subimg" name="sort_btn[MOVEUP_<% $ws.id %>]" value="MOVEUP" title="position up" src="./images/page_moveup.png" >
				<input type="image" onClick="movedown(<% $ws.id %>);" class="subimg" name="sort_btn[MOVEDOWN_<% $ws.id %>]" value="MOVEDOWN" title="position down" src="./images/page_movedown.png" >
			</td>
		    <td><div style="width:10px;height:10px;background-color:#<%$ws.ytc_color%>"></div></td>  
			<td class="text-right"> <% foreach from=$ws.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
	</tr>		
	<%/foreach%>		
</table>
<div class="btn-group">
 <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=alphasort_cats&parent=<%$GET.starttree%>">sort categories by name</a>
</div>
<%/if%>
		</td></tr></table>

<div class="subright"><% $subbtn %></div>

</form>
	</fieldset>	
	</div>
	

<script>
$('.vim_scrollbar').height($('#cat-table').height() + 'px');
</script>
