<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Themen-Liste</h3><!-- /.panel-title -->
    </div><!-- /.panel-heading -->
    <div class="panel-body">
        <div class="btn-group">
            <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=show_all">{LBLA_SHOWALL}</a>
            <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=edit">{LBLA_NEW_SETUP}</a>
        </div>
    </div><!-- /.pnl-body -->

    <form class="jsonform" action="<%$PHPSELF%>" method="post">
        <table class="table">
            <thead>
                <tr>
                    <th>{LBL_ADMINDESC}</th>
                    <th>{LBL_FIRSTPAGE}</th>
                    <th>{LBL_SHOWPARENTCAT}</th>
                    <th>{LBL_SORTING}</th>
                    <th>Theme</th>
                    <th>{LBL_OPTIONS}</th>
                </tr>
            </thead>
            
            <% foreach from=$TOPLMAN.topltable item=row %>
                <tr>
                    <td>
                        <input type="text" class="form-control" name="FORM[<% $row.id%>][description]" value="<%$row.description|hsc%>">
                    </td>
                    <td>
                        <select class="form-control custom-select" name="FORM[<%$row.id%>][first_page]">
                            <% foreach from=$row.entrypoints item=opt %><%$opt%><%/foreach%>
                        </select>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" <% if ($row.show_parent_level==1) %>checked<%/if%> name="FORM[<% $row.id%>][show_parent_level]" value="1">
                    </td>
                    <td>
                        <input type="text" class="form-control" size="3" name="FORM[<% $row.id%>][morder]" autocomplete="off" value="<%$row.morder%>">
                        <input type="hidden" name="FORM[<% $row.id%>][id]" value="<% $row.id%>">
                    </td>
                    <td>
                        <% if $row.theme_image!=""%><img src="<%$row.thumb%>"><%/if%>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a class="btn btn-secondary" title="Seiten anzeigen" href="run.php?toplevel=<% $row.id%>&epage=websitemanager.inc"><i class="fas fa-eye"></i></a>
                            <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%>
                        </div><!-- /.btn-group -->
                    </td>
                </tr>
            <%/foreach%>
            
        </table>
        <input type="hidden" value="save_tpltable" name="cmd">
        <input type="hidden" value="<%$epage%>" name="epage">
        
        <div class="form-feet">
            <%$subbtn%>
        </div><!-- /.form-feet -->
    </form>

</div><!-- /.panel panel-default -->