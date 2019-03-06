<h3>Städte für Workshops</h3>

<a href="#" class="btn btn-default" data-toggle="modal" data-target="#add_city">Neue Stadt</a>

<% if (count($WORKSHOP.cities)>0)%>
<form action="<%$PHPSELF%>" class="jsonform" method="POST">
    <input type="hidden" name="cmd" value="save_cities"/>
    <input type="hidden" name="epage" value="<%$epage%>"/>
<table class="table table-striped table-hover">
        <thead>
            <tr>                
                <th>Stadt</th>                
                <th>Anzahl Workshops</th>
                <th></th>
            </tr>
        </thead>   
        <tbody>
        <% foreach from=$WORKSHOP.cities item=row %>
            <tr >                
                <td><input name="FORM[<%$row.id%>][c_city]" value="<%$row.c_city|sthsc%>" class="form-control" /></td>
                <td><span class="badge"><%$row.count%></span></td>                
                <td class="text-right"><div class="btn-group"><% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>                
                </div></td>
            </tr>
        <%/foreach%>
        </tbody>
</table>
<%$subbtn%>
</form>

<%else%>
    <div class="alert alert-info">Keine Städte eingetragen</div>
<%/if%>

