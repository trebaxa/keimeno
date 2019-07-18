<link rel="stylesheet" href="../includes/modules/indexsearch/admin/css/style.css" type="text/css"/>

<div class="page-header"><h1>Indexierte Suche</h1></div>


<% if ($cmd=='load_index') %>
<h3>Indexierte Seiten (<% $site_urls.count %>)</h3>
<% if (count($site_urls.listarr)>0) %>
	<% include file="paging.admin.tpl" %>
<br><form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
 <table class="table table-striped table-hover">
 <thead><tr>
 	<th>URL</th>
 	<th>Titel</th>
 	<th>Keywords</th>
 	<th></th>
 </tr></thead>
 <% foreach from=$site_urls.listarr item=site %>            
        <tr>
            	<td><a href="<% $site.s_url %>" target="_sr"><% $site.s_url %></a></td>
            	<td><% $site.s_title|truncate:100 %></td>
            	<td><% $site.s_keywords|truncate:100 %></td>
            	<td><% $site.icon_del %></td>
         </tr>
  <%/foreach%>
  </table>
  <input type="hidden" name="cmd" value="masssave">
  <input type="hidden" name="orgaktion" value="<%$cmd%>">
	<input type="hidden" name="epage" value="<%$epage%>">
</form>
<%else%> 
<div class="alert alert-info">Noch keine Seite aufgenommen.</div>
<%/if%>
<%/if%>

<% if ($cmd=='words') %>
	<% include file="paging.admin.tpl" %>
<% assign var=bis value=$GET.start+1000 %>	
<h3>Indexierte W&ouml;rter</h3>
<small>(<%$paging.count_total%>) - aufgelistet:<%$GET.start%> - <%$bis%></small>
<% if (count($site_words.listarr)>0) %>
<br><form action="<%$PHPSELF%>" method="post" class="form-inline" enctype="multipart/form-data">
<div class="row">
  <div class="col-md-4">
    <table class="table table-striped table-hover" >
    <thead><tr>
 	<th></th>
 	<th>Wort</th>
 	<th></th>
    </tr></thead>
    <% foreach from=$site_words.listarr item=sword name=gloop %>
        <tr>
     			<td width="10"><input type="checkbox" name="bwords[]" value="<% $sword.si_word %>"></td>
            	<td><p style="word-wrap: break-word;"><% $sword.si_word %></p></td>
            	<td class="text-right" width="130"><% $sword.icon_del %>
            	<a class="btn btn-secondary pull-right" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=blockword&word=<% $sword.si_word %>"><i class="fa fa-eraser"><!----></i></a></td>
         </tr>
       <% if $smarty.foreach.gloop.iteration % 10 == 0 && $smarty.foreach.gloop.last==false%>
       </table>
  </div><!-- COL -->
  <div class="col-md-4">
    <table class="table table-striped table-hover" >
    <thead><tr>
 	<th></th>
 	<th>Wort</th>
 	<th></th>
    </tr></thead>       
       <%/if%>  
  <%/foreach%>
  </table>
</div>
</div>
  	<label>ausgew&auml;hlte:</label><select class="form-control custom-select" name="cmd">
		<option value="massblock" <% if ($POST.task=='massblock') %>selected<%/if%>>blockieren</option>				
	</select>
  
	<input type="hidden" name="epage" value="<%$epage%>"><%$subbtn%>
</form>
<%else%> 
<div class="alert alert-info">Noch keine W&ouml;rter aufgenommen.</div>
<%/if%>
<%/if%>



<% if ($cmd=='showtasks') %>
<div class="row">
<div class="col-md-4">
<h3>Aufgaben</h3>
<form action="<%$PHPSELF%>" onSubmit="showPageLoadInfo();" class="form-inline" method="post" enctype="multipart/form-data">
	<select class="form-control custom-select" name="task">
		<option value="autocrawl" <% if ($POST.task=='autocrawl') %>selected<%/if%>>Automatische Indexierung starten</option>				
		<option value="clean_sites" <% if ($POST.task=='clean_sites') %>selected<%/if%>>Index nach Vorgabe s&auml;ubern</option>				
		<option value="reset" <% if ($POST.task=='reset') %>selected<%/if%>>Kompletten Suchindex leeren</option>		
	</select>
    <%$execbtn%>
    <input type="hidden" name="cmd" value="tasks">
	<input type="hidden" name="epage" value="<%$epage%>">
</form>
</div>
<div class="col-md-4">
<h3>W&ouml;rter l&ouml;schen</h3>
<form action="<%$PHPSELF%>" onSubmit="showPageLoadInfo();" class="form-inline" method="post" enctype="multipart/form-data">
	<input type="text" class="form-control" name="wort" value="" placeholder="Wort">
<%$execbtn%>
  <input type="hidden" name="cmd" value="tasks">
  <input type="hidden" name="task" value="delete_words">
  <input type="hidden" name="epage" value="<%$epage%>">
</form>
</div>
<div class="col-md-4">
<div class="alert alert-info">Verwenden Sie das "%" Zeichen als Platzhalter.</div>
</div>
</div>
<%/if%>


<% if ($cmd=='conf') %>
    <%$INDEXSEARCH.conf%>
<%/if%>