<div class="page-header"><h1>{LBL_NEWSLIST}</h1></div>
<div class="row">
   <div class="col-md-6"> 
        <div class="btn-group">
            <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=list"><i class="fa fa-table"></i> {LBL_SHOWALL}</a>  
            <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=a_new"><i class="fa fa-plus"></i> {LBL_NEW_NEWS}</a>
            <a class="btn btn-secondary" onclick="jsonexec('<%$PHPSELF%>?epage=<%$epage%>&cmd=rebuildperma',true);" href="#"><i class="fa fa-disk"></i> Permalinks neu erstellen</a>
        </div>
    </div>    
    <div class="col-md-6 text-right form-inline">     
        <input placeholder="{LBL_SEARCH}" type="text" class="form-control" ID="wortnews" value="<%$wortnews%>" name="wortnews" size="9" onKeyUp="sendRequest2InnerHTMLWithLimit('wortnews','news_search_area','ax_searchnews','run','&epage=news.inc&orderby=title&direc=ASC',3,'./images/opt_loader.gif')">
    </div>
</div>

<div id="news_search_area"></div>

<% if ($cmd=='list') %>

<%include file="cb.panel.header.tpl" title="{LBL_NEWSGROUPS}"%>

<div class="row">
    <div class="col-md-3 form-inline">
        <div class="form-group">
            <label>{LBL_NEWSGROUPS}:</label>
            <select class="form-control custom-select" name="cid" onChange="location.href=this.options[this.selectedIndex].value">
                <option value="">- please select -</option>
                	<% foreach from=$NEWSADMIN.groups item=row %>
                    <option <% if ($row.id==$REQUEST.gid) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$GET.epage%>&cmd=list&gid=<%$row.id%>"><%$row.groupname%></option>
                    <%/foreach%>
                </select>
        </div>
    </div>        
</div>
<% if ($NEWSADMIN.allnewslist.count>0) %>
<form method="POST" action="<%$PHPSELF%>" class="">
<input type="hidden" value="<%$epage%>" name="epage">
<table class="table table-striped table-hover" id="news-table">
	<thead><tr>
     <th></th>
     <th>#</a></th>
	 <th>eingestellt am</th>
	 <th>{LBL_TITLE}</th>
	 <th>News-Datum</th>
	 <th>letzte &Auml;nderung</th>
	 <th>Author</th>
	 <th>Aufrufe</th>
	 <th></th>
	</tr></thead>	
		<% foreach from=$NEWSADMIN.allnewslist.table item=row %>
	<tr>
				<td><input type="checkbox" name="newsids[]" value="<%$row.NID%>"></td>
                <td><%$row.NID%></td>
				<td><%$row.date_print%></td>
				<td><a href="<%$PHPSELF%>?epage=news.inc&id=<%$row.NID%>&cmd=edit&epage=news.inc"><%$row.title%></td>
                <td><%$row.n_lastchange%></td>
				<td><%$row.ndate%></td>				
				<td><%$row.n_author%></td>
				<td class="text-center"><span class="badge"><%$row.views%></span></td>
				<td class="text-right">
                <div class="btn-group">
				<% if ($row.AFCOUNT>0) %><img alt="Attachment" title="<%$row.AFCOUNT%> Anh&auml;nge" src="./images/attach.png" ><%/if%>
                <%$row.icon_edit%><%$row.icon_del%><%$row.icon_approve%>
                </div>
                </td>
			</tr>
			<% /foreach %>
		</table>
markierte: <select class="form-control custom-select" name="cmd">
<option value="massdeletenews">{LBL_DELETE}</option>
</select>  <%$btngo%>     
</form>
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="news-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>         	
<%else%>		
    <div class="alert alert-info">{LBL_NOITEMS}</div>
<%/if%>	
<%include file="cb.panel.footer.tpl"%>
<%/if%>	


<% if ($cmd=='edit') %>
 <% include file="news.editor.tpl" %>
<%/if%>	

<% if ($cmd=='conf') %>
 <%$NEWSADMIN.conf%>
<%/if%>	
