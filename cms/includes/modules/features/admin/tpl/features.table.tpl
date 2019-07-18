<% if (count($FEATURES.features)>0) %>
    <h3>Featrues</h3>
    <form action="<%$PHPSELF%>" method="post" class="jsonform">
        <input type="hidden" name="cmd" value="save_tab"/>
        <input type="hidden" name="epage" value="<%$epage%>"/>
        <input type="hidden" name="gid" value="<%$GET.gid%>"/>
        <table class="table table-striped table-hover" id="feature-table">
        <thead><tr>    
            <th>Titel</th>
            <th>Text</th>
            <th class="text-center">Icon</th>
            <th class="text-center">Bild</th>
            <% if ($GET.gid>0)%><th class="text-center">Sort.</th><%/if%>
            <th></th>
        </tr></thead> 
        
        <tbody>
        <% foreach from=$FEATURES.features item=row %>
         <tr>
            <td><a href="javascript:void(0);" onclick="add_show_box_tpl('<%$PHPSELF%>?epage=<%$epage%>&cmd=edit_feature&id=<%$row.id%>', 'Feature Editor');")><%$row.f_title%></a></td>
            <td><%$row.f_text|st%></td>
            <td class="text-center"><i class="fa fa-<%$row.f_icon%> fa-2x"></i></td>
            <td class="text-center"><img src="<%$row.thumb%>" class="img-thumbnail"></td>
            <% if ($GET.gid>0)%>
            <td class="text-center">
                <input class="form-control" type="text" name="sort[<%$row.id%>][f_order]" value="<%$row.f_order%>" />
                <input type="hidden" name="sort[<%$row.id%>][id]" value="<%$row.id%>" />
            </td>
            <%/if%>
            <td class="text-right">
               <div class="btn-group"> 
                <a href="javascript:void(0);" class="btn btn-secondary" onclick="add_show_box_tpl('<%$PHPSELF%>?epage=<%$epage%>&cmd=edit_feature&id=<%$row.id%>', 'Feature Editor');"><span class="glyphicon glyphicon-pencil"><!----></span></a>
                <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%>
               </div> 
            </td>
        </tr>    
        <%/foreach%>
        </tbody>
        </table>
       <% if ($GET.gid>0)%> <%$subbtn%><%/if%>
    </form>
    <%* Tabellen Sortierungs Script *%>
    <%assign var=tablesortid value="feature-table" scope="global"%>
    <%*include file="table.sorting.script.tpl"*%>
<%else%>
    <p class="alert alert-info">Noch keine Features hinzugefügt.</p>
<%/if%>

<% if (count($FEATURES.feature_groups)>0) %>
<hr>
    <h3>Feature-Gruppen</h3>
    <table class="table table-striped table-hover">
    <thead><tr>    
        <th>Titel</th>
        <th></th>
    </tr></thead>     
    <tbody>
    <% foreach from=$FEATURES.feature_groups item=row %>
     <tr>
        <td><%$row.fg_name%></td>
        <td class="text-right">
           <div class="btn-group"> 
            <a href="javascript:void(0);" class="btn btn-secondary" onclick="$('#feateditgroup').modal('show');load_json_form('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_feature_group&id=<%$row.id%>', 'featuregfroupform')"><span class="glyphicon glyphicon-pencil"><!----></span></a>
            <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%>
           </div> 
        </td>
    </tr>    
    <%/foreach%>
    </tbody>
    </table>
    
<%else%>
    <p class="alert alert-info">Noch keine Feature-Gruppen hinzugefügt.</p>
<%/if%>

