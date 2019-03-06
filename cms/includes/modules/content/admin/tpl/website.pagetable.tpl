<% if (count($WLIST.websites)>0) %>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Seiten</h3><!-- /.panel-title -->
        </div><!-- /.panel-heading -->
    <div class="panel-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Admin. {LBLA_DESCRIPTION}</th>
                    <th>Sort.</th>
                    <th></th>
                </tr>
            </thead>
            <% foreach from=$WLIST.websites item=ws name=pageloop %>
                <tr id="tr-page-ident-<% $ws.id %>">
                    <td><input type="checkbox" name="pageids[<% $ws.id %>]" value="<% $ws.id %>"></td>
                    <td>
                        <% if ($ws.childcount>0) %>
                            <a title="{LA_KATEGORIEFFNEN}: <% $ws.name|sthsc %>" href="<%$PHPSELF%>?epage=<%$epage%>&toplevel=<%$REQUEST.toplevel%>&starttree=<% $ws.id %>"><i class="fa fa-folder"></i></a>
                        <%else%>
                            <i class="fa fa-file"></i>
                        <%/if%>
                    </td><!-- File or Folder -->
                    <td>
                        <div class="btn-group">
                            <% if ($smarty.foreach.pageloop.first==false) %>
                                <a class="btn btn-default" title="nach oben bewegen" href="javascript:void(0);" onClick="mup(<% $ws.id %>,<% $ws.parent %>);"><i class="fa fa-arrow-circle-o-up"><!----></i></a>
                            <%/if%>
                            <% if ($smarty.foreach.pageloop.last==false) %>
                                <a class="btn btn-default" title="nach unten bewegen" href="javascript:void(0);" onclick="mdown(<%$ws.id%>,<%$ws.parent%>);"><i class="fa fa-arrow-circle-o-down"><!----></i></a>
                            <%/if%>
                        </div><!-- /.btn-group -->
                    </td><!-- Up and Down Buttons -->
                    <td>
                        <input type="hidden" name="CATS[<% $ws.id %>][id]" value="<% $ws.id %>">
                        <% if ($ws.admin==0) %>
                            <input type="text" class="form-control" name="CATS[<% $ws.id %>][description]" value="<% $ws.description|hsc %>">
                        <%else%>
                            <input type="hidden" name="CATS[<% $ws.id %>][description]" value="<% $ws.description|sthsc %>">
                            <% $ws.description %>
                        <%/if%>
                    </td>
                    <td><input type="text" class="form-control"name="CATS[<% $ws.id %>][morder]" value="<% $ws.morder %>"></td>
                    <td class="text-right"> <div class="btn-group"><% foreach from=$ws.icons item=picon name=cicons %><% $picon %><%/foreach%></div><!-- /.btn-group --></td>
                </tr>
            <%/foreach%>
        </table>
        </div>
        <div class="panel-footer">
            <div class="form-group">
             <label class="sr-only">Aufgabe:</label>
                <div class="input-group"> 
                    <select class="form-control" name="cmd">
                        <option value="a_msave">Tabelle speichern</option>
                        <option value="set_perm_to_public">markierte f&uuml;r Besucher sichtbar machen</option>
                    </select>
                    <span class="input-group-btn"><button class="btn btn-primary"><i class="fa fa-floppy-o"></i></button></span>                   
                 </div>   
            </div>        
        </div><!-- /.panel-feet -->

        <input type="hidden" value="<% $REQUEST.starttree %>" name="starttree">
    </div><!-- /.panel panel-default -->

<%else%>
    <div class="bg-info text-info"><p class="text-info">F&uuml;r diesen Toplevel gibt es keine Webseiten Zuordnung.</p><!-- /.text-info --></div>
<%/if%>

<%if ($GET.axcall==1) %>
    <script>set_ajaxapprove_icons();</script>
<%/if%>

