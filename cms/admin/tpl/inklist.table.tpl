<br>Kategorie - Filter:
<select class="form-control custom-select"  onChange="location.href=this.options[this.selectedIndex].value">
	<option <% if ($GET.cid==0) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&cid=0">alle anzeigen</option>
		<% foreach from=$linklist_groups item=gi %>
		<option <% if ($GET.cid==$gi.id) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&cid=<%$gi.id%>"><%$gi.lc_title%></option>
			<%/foreach%>
		</select>
       <br> 
<% if (count($linkliste)>0) %>           	
<table class="table table-striped table-hover" >
			<thead><tr>
			<th></th>
			<th></th>
			<th>Name</th>
			<th>Link</th>
			<!-- <th>Sort.</th>			-->
			<th>Kategorie</th>
			<th></th>
			</tr></thead>
 		<% foreach from=$linkliste item=linkitem %>         
 		  <tr>
        			<td><input type="checkbox" class="checkall" name="metaids[]" value="<%$linkitem.id%>"></td>
        			<td><% if ($linkitem.picture_thumb!="") %><img src="<%$linkitem.picture_thumb%>" ><%/if%></td>
            	<td width="300"><% $linkitem.title %><br><span class="small"><%$linkitem.sp_comment%></span></td>
            	<td><a href="<%$linkitem.url|hsc%>" target="_blank"><%$linkitem.url%></a>
            	<input name="orders[<%$linkitem.id%>]" type="hidden" value="<%$linkitem.s_order%>">
            	</td>
            	<!-- <td><input name="orders[<%$linkitem.id%>]" type="text" class="form-control" size="3"  value="<%$linkitem.s_order%>"></td> -->
            	<td>
            	<select class="form-control custom-select" name="category[<%$linkitem.id%>]">
            	 		<% foreach from=$RELLINK.linklist_groups item=gi %>         
 									<option <% if ($gi.id==$linkitem.cat_id) %>selected<%/if%> value="<%$gi.id%>"><%$gi.lc_title%></option>
 									<%/foreach%>
            	</select>
            	</td>
            	<td class="text-right"> <% foreach from=$linkitem.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
         </tr>
 		<%/foreach%>
</table><br>
Ausf&uuml;hren:<select class="form-control custom-select" name="cmd">
		<option value="save_link_table">alles speichern</option>
		<option value="show_meta_import">... markierte Metas importieren</option>
		<option value="delete_listed_links">... markierte {LBL_DELETE}</option>
		<!-- <option value="a_movecat">Move to...</option> -->
		</select> 

<%else%>
<div class="alert alert-info">Es liegen Daten vor.</div>
<%/if%>

