<% if (count($FLEXTEMP.flextpl.tpls)>0)%>
      <table class="table table-striped table-hover" id="feedback-table">
            <thead>
                <tr>
                    <th>Field</th>
                    <th></th>
                </tr>
            </thead>
            
            <% foreach from=$FLEXTEMP.flextpl.tpls item=row %>
                <tr>
                    <td><a href="javascript:void(0)" onclick="simple_load('js-tpledit','<%$eurl%>cmd=edittpl&flxid=<%$FLEXTEMP.flextpl.FID%>&id=<%$row.id%>');"><% $row.t_name%></a></td>
                    <td class="text-right">
                        <div class="btn-group">
                            <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                            <a class="btn btn-default" href="javascript:void(0)" onclick="simple_load('js-tpledit','<%$eurl%>cmd=edittpl&flxid=<%$FLEXTEMP.flextpl.FID%>&id=<%$row.id%>');"><i class="fa fa-pencil-square-o"></i></a>
                        </div>
                    </td>
                </tr>
            <%/foreach%>
        </table>
<%else%>
    <div class="alert alert-info">Keine Templates angelegt</div>
<%/if%>  