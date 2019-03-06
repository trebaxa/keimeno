<div class="page-header">
        <div class="page-header"><h1>{LBL_COLLECTIONS}</h1></div>
    </div><!-- /.page-header -->
    
    <%include file="cb.panel.header.tpl" title="Kollektionen"%>    
        <form method="post" action="<%$PHPSELF%>" class="">
            <table class="table">
                <thead>
                    <tr>
                        <th>{LBL_TITLE}</th>
                        <th>Anzahl Gruppen</th>
                        <th>{LBL_TITLE}(edit)</th>
                        <th>Options</th>
                    </tr>
                </thead>
                
                <% foreach from=$CUSTGROUPS.collections item=row %>
                    <tr>
                        <td><%$row.col_name%></td>
                        <td><%$row.count%></td>
                        <td><input type="text" class="form-control" name="FORM[<%$row.id%>][col_name]" size="60" value="<%$row.col_name|sthsc%>"></td>
                        <td class="text-right"><% foreach from=$row.icons key=iconkey item=picon %><% $picon %><%/foreach%></td>
                    </tr>
                <%/foreach%>
            </table>
            <input type="hidden" name="cmd" value="msave_col">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="section" value="<%$section%>">
            <div class="form-feet">
                <div class="btn-group">
                    <a class="btn btn-default" href="<%$PHPSELF%>?cmd=addnewcol&epage=<%$epage%>">{LBL_ADDCOLLECTION}</a>
                </div>
                <%$subbtn%>
            </div>
        </form>
    <%include file="cb.panel.footer.tpl"%>

    <%include file="cb.panel.header.tpl" title="Kollektionen hinzuf&uuml;gen"%>
        <div class="panel-body">
            <div class="bg-info text-info"><p class="text-info">Pro Zeile eine Kollektion.</p></div>
        </div><!-- /.panel-body -->
    
        <form method="post" action="<%$PHPSELF%>">
    
            <textarea class="form-control" name="collist" rows="15"></textarea>
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="cmd" value="add_col_list">
            <input type="hidden" name="section" value="<%$section%>">
            <div class="form-feet"><%$addbtn%></div>
            <!-- /.form-feet -->
        </form>    
    <%include file="cb.panel.footer.tpl"%>
