<% if (count($linkliste)>0)%>
<div class="row">
    <div class="col-md-3">
Kategorie - Filter:
<select class="form-control form-inline" onChange="location.href=this.options[this.selectedIndex].value">
	<option <% if ($GET.cid==0) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&cid=0">alle anzeigen</option>
		<% foreach from=$linklist_groups item=gi %>
		<option <% if ($GET.cid==$gi.id) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&cid=<%$gi.id%>"><%$gi.lc_title%></option>
			<%/foreach%>
		</select>
    </div>        
</div>        
            	
<table class="table table-striped table-hover" id="links-manager-table" >
			<thead><tr>
            <th>#</th>
			<th></th>
			<th></th>
			<th>Name</th>
            <th width="100">Banner</th>
            <th>Type</th>
            <th>Toplevel</th>
			<th>Link</th>
			<th>Sort.</th>			
            <th>Position</th>
			<th>Kategorie</th>
			<th>Views</th>
            <th>Clicks</th>
            <th></th>
			</tr></thead>
 		<% foreach from=$linkliste item=linkitem %>         
        <tr>
        <td><%$linkitem.id%></td>
        			<td><input type="checkbox" id="db_ids" name="metaids[]" value="<%$linkitem.id%>"></td>
        			<td><% if ($linkitem.thumbnail!="") %><img src="<%$linkitem.thumbnail%>" ><%/if%></td>
            	<td width="300"><% $linkitem.title %><br><span class="small"><%$linkitem.sp_comment|st|truncate:100%></span></td>
                <td><% if ($linkitem.picture_thumb!="") %><img src="<%$linkitem.picture_thumb%>"  height="30"><%/if%></td>
                <td><%$linkitem.lb_type_written%></td>
                <td><% ','|implode:$linkitem.toplevellist %></td>
                <td><a href="<%$linkitem.url|hsc%>" target="_blank"><%$linkitem.url%></a></td>                            	
            	<td><input name="orders[<%$linkitem.id%>]" type="text" class="form-control" size="3"  value="<%$linkitem.s_order%>"></td>
            	<td><%$linkitem.pos%></td>
                <td>
            	<select class="form-control custom-select" name="category[<%$linkitem.id%>]">
            	 		<% foreach from=$linklist_groups item=gi %>         
 									<option <% if ($gi.id==$linkitem.cat_id) %>selected<%/if%> value="<%$gi.id%>"><%$gi.lc_title%></option>
 									<%/foreach%>
            	</select>
            	</td>
                <td class="text-right"><%$linkitem.lb_views%></td>
                <td class="text-right"><%$linkitem.lb_clicks%></td>
            	<td class="text-right"> <% foreach from=$linkitem.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
         </tr>
 		<%/foreach%>
</table>Ausf&uuml;hren:<select class="form-control custom-select" name="cmd">

    <%* Tabellen Sortierungs Script *%>
    <%assign var=tablesortid value="links-manager-table" scope="global"%>
    <%include file="table.sorting.script.tpl"%>   



		<option value="save_link_table">alles speichern</option>
		<option value="show_meta_import">... markierte Metas importieren</option>
		<option value="delete_listed_links">... markierte {LBL_DELETE}</option>
		<!-- <option value="a_movecat">Move to...</option> -->
		</select> 
	<%$btngo%>
		</form>
<%/if%>        	