<% if (count($REFLIST.links)>0) %>
<table class="table table-striped table-hover" id="reflist-table">

<thead><tr>
    <th></th>
    <th>Firma</th>
    <th>Strasse</th>
    <th>PLZ</th>
    <th>Ort</th>
    <th>Tel.</th>
    <th>Homepage</th>
    <th></th>
</tr></thead> 

<tbody>
<% foreach from=$REFLIST.links item=row %>
 <tr>
    <td><img src="<%$row.thumb%>" class="img-thumbnail"></td>
    <td><%$row.r_firma%></td>
    <td><%$row.r_street%></td>
    <td><%$row.r_plz%></td>
    <td><%$row.r_ort%></td>
    <td><%$row.r_tel%></td>
    <td><a href="<%$row.r_url%>" target="_blank"><%$row.r_url%></a></td>
    <td class="text-right">
       <div class="btn-group"> 
        <a href="javascript:void(0);" class="btn btn-secondary" onclick="$('#reflinkedit').modal('show');load_json_form('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_reflink&id=<%$row.id%>', 'reflistform')"><span class="glyphicon glyphicon-pencil"><!----></span></a>
        <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%>
       </div> 
    </td>
</tr>    
<%/foreach%>
</tbody>
</table>
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="reflist-table" scope="global"%>
<%include file="table.sorting.script.tpl"%> 
<h3>Konfiguration</h3>
<%$REFLIST.config%>
<%else%>
<p class="alert alert-info">Noch keine Ref.-Links hinzugefügt.</p>
<%/if%>