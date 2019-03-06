<link rel="stylesheet" href="../includes/modules/tcblog/admin/css/style.css" type="text/css">
<div class="page-header"><h1><i class="fa fa-file-code-o"></i> Blog <%$TCBLOG.selected_group.groupname%></h1></div>
<!-- /.page-header -->



 
 <!-- Modal -->
<div class="modal fade" id="newblogitem" tabindex="-1" role="dialog" aria-labelledby="newblogitemLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="post">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="newblogitemLabel">Neuer Beitrag</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cmd" value="add_item"/>
        <input type="hidden" name="epage" value="<%$epage%>"/>
        <label>{LBL_TITLE}:</label>
        <input type="text" class="form-control" required="" name="FORM_CON[title]"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>




<% if ($section=="conf") %>
    <% $TCBLOG.CONFIG %>
    <a href="<%$eurl%>cmd=rebuildpageindex" class="btn btn-default json-link">Rebuild URL Page Index</a>
    <%*Verküpfte Page ID: <%$TCBLOG.pageindex.pi_page%> *%> 
<%/if%>

<% if ($section=='items' || $section=='start') %>

<div class="btn-group">
    <a class="btn btn-primary" href="javascript:void(0);" data-toggle="modal" data-target="#newblogitem"><i class="fa fa-plus"></i> Neu anlegen</a>
    <div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
      Blog Auswahl
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <% foreach from=$TCBLOG.groupsselect item=row %>
            <li><a href="<%$PHPSELF%>?epage=<%$epage%>&cmd=<%$cmd%>&gid=<%$row.id%>&section=start"><%$row.groupname%></a></li>
        <%/foreach%>
    </ul>
    </div>    
</div>




<div id="topofpage">
<%include file="cb.panel.header.tpl" title="Blog {LBL_ITEMS}"%>
<% if (count($TCBLOG.pin_items)>0)%>
        			<table class="table table-striped table-hover" id="js-blog-table">
        				<% foreach from=$TCBLOG.pin_items item=mdate name=mt %>
        					<tr>
                            	<td><% $mdate.date %></td>
                            	<td><a href="<%$PHPSELF%>?epage=<%$epage%>&cmd=edit&id=<%$mdate.NLID%>&uselang=1"><% $mdate.title %></a></td>
                            	<td><% $mdate.mitarbeiter_name %></td>
                            	<td><% $mdate.username %></td>
                            	<td class="text-right">
                                <div class="btn-group">
                            		<% $mdate.icon_edit %>
                            		<% $mdate.icon_del %>
                            		<% $mdate.icon_approve %>
                            		<% $mdate.icon_view %>
                            		</div>
                            	</td>	
                            </tr>
        				<% /foreach %>
        			</table>

        
   <%else%>
    <div class="alert alert-info">Keine Beiträge enthalten.</div>
   <%/if%>     
  <%include file="cb.panel.footer.tpl"%>
</div>




<%/if%>

<% if ($cmd=='edit') %>
    <%include file="tcblog.editor.tpl"%>
<%/if%>


<% if ($section=='groups') %>

<%include file="cb.panel.header.tpl" title="Blogs"%>
<div class="btn-group">
    <a href="#" data-toggle="modal" data-target="#newprogotimer" class="btn btn-primary"><i class="fa fa-plus"></i> Neuer Blog</a>
</div>

<form action="<%$PHPSELF%>" method="POST" class="jsonform form-inline">
     <input type="hidden" name="epage" value="<%$epage%>">
     <input type="hidden" name="cmd" value="save_groups">


    <table class="table table-striped table-hover" id="blog-groups-table">
    <thead>
        <tr>
            <th>Blog Name</th>
            <th>Page ID</th>
            <th></th>
        </tr>        
    </thead>
    <tbody>
				<% foreach from=$TCBLOG.groups item=row %>                
     <tr>
        <td><input name="FORM[<%$row.id%>][groupname]" type="text" value="<%$row.groupname%>" class="form-control"></td>
        <td><input name="FORM[<%$row.id%>][g_pageid]" type="text" value="<%$row.g_pageid%>" class="form-control"></td>
        <td class="text-right"><div class="btn-group"><% foreach from=$row.icons item=picon %><% $picon %><%/foreach%></div></td>
     </tr>           
                <%/foreach%>
                </tbody>
   </table>
       <%* Tabellen Sortierungs Script *%>
        <%assign var=tablesortid value="blog-groups-table" scope="global"%>
        <%include file="table.sorting.script.tpl"%>  
   <%$subbtn%>     
</form>   
<%include file="cb.panel.footer.tpl"%>

<!-- Modal -->
<div class="modal fade" id="newprogotimer" tabindex="-1" role="dialog" aria-labelledby="newprogotimerLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="post" class="form">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="cmd" value="add_blog">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="newprogotimerLabel">Neuer Blog</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>Blog</label>            
            <input type="text" required="" placeholder="Blog Name" class="form-control" name="FORM[groupname]">
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>
<%/if%>

<% if ($section=='edit_group') %>
<h3><%$TCBLOG.group.groupname%></h3>

<div class="btn-group">
        <%$TCBLOG.langselect%>
</div>    

							<form action="<%$PHPSELF%>" method="post" class="form jsonform">
							<div class="form-group">
                                <label>{LBL_TITLE}</label>
							     <input required="" value="<%$TCBLOG.groupcon.g_title|sthsc%>" class="form-control" name="FORM_CON[g_title]">
                            </div>
<% if ($TCBLOG.permchecks != "")%>
        <h3>{LBL_PERMISSIONS}</h3> <%$TCBLOG.permchecks %> 
<%/if%>
        	
    <input type="hidden" name="tid" value="<%$GET.id%>">
                            <input type="hidden" name="epage" value="<%$epage%>">
							<input type="hidden" name="conid" value="<%$TCBLOG.groupcon.id%>">
							<input type="hidden" name="FORM_CON[lang_id]" value="<%$GET.uselang%>">
							<input type="hidden" name="cmd" value="setallperm">
    <%$subbtn%></form>
    
<%/if%>


