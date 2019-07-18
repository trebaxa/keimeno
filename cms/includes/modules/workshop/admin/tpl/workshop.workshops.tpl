<h3>Workshops</h3>

<form class="form-inline">
    <a href="#" class="btn btn-secondary" data-toggle="modal" data-target="#add_workshop">Neuer Workshop</a>
    <div class="form-group">
        <label>Stadt Filter:</label>
        <select id="js-ws-change" class="form-control">
            <option <% if ($GET.city==0)%>selected<%/if%> value="0">- bitte wählen -</option>
            <% foreach from=$WORKSHOP.cities item=row %>
                <option <% if ($GET.city==$row.id)%>selected<%/if%> value="<%$row.id%>"><%$row.c_city%></option>
            <%/foreach%>
        </select>
    </div>
</form>    

<% if (count($WORKSHOP.workshops)>0)%>
<form action="<%$PHPSELF%>" class="jsonform" method="POST">
    <input type="hidden" name="cmd" value="save_workshoptable"/>
    <input type="hidden" name="epage" value="<%$epage%>"/>
<table class="table table-striped table-hover">
        <thead>
            <tr>                
                <th>Workshop</th>  
                <th>Datum</th>              
                <th>Preis (br))</th>
                <th>Teilnehmer</th>
                <th></th>
            </tr>
        </thead>   
        <tbody>
        <% foreach from=$WORKSHOP.workshops item=row %>
            <tr >                
               <%* <td><input name="FORM[<%$row.id%>][ws_title]" value="<%$row.ws_title|sthsc%>" class="form-control" /></td>*%>
                <td><%$row.ws_title%></td>
                <td><%$row.date_ger%></td>
                <td><%$row.ws_price_br%> <i class="fa fa-eur"></i></td>
                <td><span class="badge"><%$row.cust_count%></span></td>                
                <td class="text-right"><div class="btn-group"><% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>                
                </div></td>
            </tr>
        <%/foreach%>
        </tbody>
</table><%*
<%$subbtn%>
*%>
</form>

<%else%>
    <div class="alert alert-info mt-lg">Keine Workshops angelegt oder noch keine Stadt im Filter ausgewählt.</div>
<%/if%>

<script>
$( "#js-ws-change" ).unbind('change');
$( "#js-ws-change" ).change(function() {    
   simple_load('js-ws-cont','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_workshops&city='+$(this).val());
});
</script>