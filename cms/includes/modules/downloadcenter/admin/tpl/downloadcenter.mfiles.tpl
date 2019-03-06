<%include file="cb.panel.header.tpl" title="Indexziert Dateien"%>
<% if (count($doc_center.mfiles)>0)%>
<form method="post" action="<% $PHPSELF %>">
	<table class="table table-striped table-hover" id="doc-verw-table" >
	<thead>
        <tr>
    	   <th>{LBL_TITLE}</th>
        	<th class="text-right">{LBL_HITS}</th>
        	<th></th>
        	<th>{LBL_DOWNLOADLINK}</th>
        	<th class="col-md-1">Sort.</th>
        	<th>{LBL_ACTIONS}</th>
    	</tr>
    </thead>
    <tbody>    
    <% foreach from=$doc_center.mfiles item=file  %>    
            <tr>
                <td><% if ($file.title!="") %><% $file.title %><%else%>-<%/if%></td>
        		<td class="text-right"><span class="badge"><% $file.TOTAL %></span></td>
        		<td><a href="<%$PHPSELF%>?epage=<%$epage%>&cmd=dc_down&id=<% $file.file|escape:urlencode %>"><% $file.file %></a></td>                
                <td><code><% $file.download_url %></code></td>
                <td><input class="form-control text-right" type="text" name="morder[<% $file.id %>]" value="<% $file.morder%>"></td>
                <td class="text-right"><div class="btn-group"><% $file.del_img_tag_icon%> <% $file.approval_icon%> <% $file.stat_icon%>  <% $file.del_img_adm_confirm%></div></td>
            </tr>
    
    <% /foreach%>
    </tbody>
    </table>
    <%* Tabellen Sortierungs Script *%>
        <%assign var=tablesortid value="doc-verw-table" scope="global"%>
        <%include file="table.sorting.script.tpl"%>
    
    <% $doc_center.submit_save %>
    
    
    <input type="hidden" name="epage" value="<%$epage%>"><input type="hidden" name="cmd" value="a_msave"/>
 </form>
 <%else%>
 <div class="alert alert-info">Es liegen keine Daten über Datein vor.</div>
 <%/if%>
 <%include file="cb.panel.footer.tpl"%>