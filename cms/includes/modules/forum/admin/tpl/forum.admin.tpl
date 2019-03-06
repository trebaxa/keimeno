<% if ($aktion=='fgroups') %>
    <div class="page-header"><h1><i class="fa fa-file-code-o"><!----></i> Foren Gruppen</h1></div>
    <br/><a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&id=0&aktion=edit">Neue Gruppe anlegen</a><br><br>
    <% if (count($ftable)>0) %>
        <h3>Gruppen</h3>
         <table class="table table-striped table-hover" >
         <tbody>
         <% foreach from=$ftable item=fgroup name=csvt %>
                <thead><tr>
                    	<th><% $fgroup.fg_name %></th>
                    	<th class="text-right">
                        <div class="btn-group">
                            <% foreach from=$fgroup.icons item=picon name=cicons %><% $picon %><%/foreach%>
                        </div>
                        </th>
                 </tr></thead>
         		<% foreach from=$fgroup.foren item=forum %>
                <tr>
                    	<td><% $forum.fn_name %><br><span class="small"><%$forum.fn_shortdesc%></span></td>
                    	<td class="text-right">
                        <div class="btn-group">
                            <% foreach from=$forum.icons item=picon name=cicons %><% $picon %><%/foreach%>
                        </div>
                        </td>
                 </tr>
         		<%/foreach%>
          <%/foreach%>
          </tbody>
        </table>
    <%else%>
        <div class="alert alert-info">Keine Gruppen gefunden.</div>
    <%/if%>
<%/if%>


<% if ($aktion=='edit') %>
    <div class="page-header"><h1><i class="fa fa-file-code-o"><!----></i> Anlegen / Bearbeiten</h1></div>
    <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
        <input type="hidden" value="savegroup" name="cmd" />
        <input type="hidden" value="<%$GET.id%>" name="id" />
        <input type="hidden" value="<%$epage%>" name="epage" />
        <div id="topofpage">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Gruppe bearbeiten</h3><!-- /.panel-title -->
                    </div><!-- /.panel-heading -->
                    
                    <div class="panel-body">
                     <!-- table start -->
                        <div class="form-group"> 
                               <label for="form-control">Gruppenname:</label>
                              <input type="text" class="form-control" name="FORM[fg_name]" value="<% $fgroup.fg_name|sthsc %>" />
                             
                        </div><!-- /.form-group -->
                      <%$subbtn%>  
                      </div>
        </div>
     </form>    
<%/if%>


<% if ($aktion=='editforum') %>
<div class="page-header"><h1><i class="fa fa-file-code-o"><!----></i> Forum anlegen / Bearbeiten</h1></div>
<div class="row">
    <div class="col-md-6">
    <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
  <input type="hidden" name="cmd" value="save_forum">
  <input type="hidden" name="id" value="<%$REQUEST.id%>">
	<input type="hidden" name="epage" value="<%$epage%>">
        
        <div id="topofpage">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Forum bearbeiten</h3><!-- /.panel-title -->
                    </div><!-- /.panel-heading -->
                    
                    <div class="panel-body">
                    <div class="form-group"> 
                               <label for="form-control">Forumname:</label>
                               <input type="text" class="form-control" name="FORM[fn_name]" value="<% $forum.fn_name|htmlspecialchars %>" />
                    </div><!-- /.form-group -->
                    
                    
                    <div class="form-group"> 
                               <label for="form-control">Beschreibung:</label>
                                <input type="text" class="form-control" name="FORM[fn_shortdesc]" value="<% $forum.fn_shortdesc|htmlspecialchars %>" />
                    </div><!-- /.form-group -->
                    
                    
                    <div class="form-group"> 
                               <label for="changelanggbltpl">Gruppe:</label>
                               	<select class="form-control" name="FORM[fn_gid]">
                                    <% foreach from=$ftable item=fd name=csvt %>
                                       <option <% if ($fd.id==$GET.gid || $fd.id==$forum.fn_gid) %>selected<%/if%> value="<%$fd.id%>"><%$fd.fg_name%></option>
                                    <%/foreach%>
                               </select>
                    </div><!-- /.form-group -->
                    </div>
                </div> <!-- end panel default -->
                
        </div> <!--end topofpage-->
        
<%$subbtn%>

</form>
    </div>
    <div class="col-md-6">
    </div>
</div>

<%/if%>

<% if ($section=='modstylefiles') %>
 <% include file="modstylefiles.tpl"%>
<%/if%>

<% if ($section=='conf') %>
<div class="page-header"><h1>{LA_MODCONFIGURATION}</h1></div>
<a class="btn btn-default json-link" href="<%$eurl%>cmd=rebuildpageindex">Rebuild Page Index</a>
<%$FORUM.conf%>
<%/if%>

