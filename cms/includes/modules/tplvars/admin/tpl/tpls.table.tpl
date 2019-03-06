<div class="row">
    <div class="col-md-12">
        <h3>Angelegte Vorlagen</h3>
            <% if (count($TPLVARS.tpls)>0) %>
            <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Vorlage</th>
                    <th>Beschreibung</th>
                    <th class="text-center">Verwendung</th>
                    <th></th>
                </tr>
            </thead>        
        <% foreach from=$TPLVARS.tpls item=row %>
            <tr>
                <td><%$row.tpl_name%></td>
                <td><%$row.tpl_description|st%></td>
                <td class="text-center"><span class="badge"><%$row.usedcount%></span></td>
                <td class="text-right"><% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
            </tr>
        <%/foreach%>
            </table>
            <% if ($GET.axcall==1) %><script>fwstart();</script><%/if%>
            <%/if%>

        </div>
</div>