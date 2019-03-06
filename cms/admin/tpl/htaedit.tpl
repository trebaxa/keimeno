<div class="page-header"><h1>HTA Access bearbeiten</h1></div>
<form class="stdform form-inline" action="<%$PHPSELF%>" method="POST">
<%$subbtn%>
    <input type="hidden" name="cmd" value="htasaveconf">
    <input type="hidden" name="epage" value="<%$epage%>">
    <table class="table table-striped table-hover">
    <thead><tr>
    <th>{LBL_HTADESCRIPTION}</th>
    <th>{LBL_HTAPREFIX} / {LBL_HTADELIMETER}</th>
    <% if ($gbl_config.debug_mode==1) %><th></th><%/if%>
    <th>SSL</th>
    <th>SMARTY</th>
    <th>Page ID</th>
    </tr></thead>
    
    <%$HTAEDIT.table%>
    
    </table>
    <h3>Anfang - Manuelle Einstellungen (nur für Experten!)</h3>
    <textarea data-theme="<%$gbl_config.ace_theme%>" class="form-control se-html" wrap="off" name="CONFIG[hta_specialtext_first]" rows="20" cols="130"><% $gbl_config.hta_specialtext_first|hsc %></textarea>
    <h3>Ende - Manuelle Einstellungen (nur für Experten!)</h3>
    <textarea data-theme="<%$gbl_config.ace_theme%>" class="form-control se-html" wrap="off" name="CONFIG[hta_specialtext]" rows="20" cols="130"><% $gbl_config.hta_specialtext|hsc %></textarea>
    <br><%$subbtn%>
    </form>
    
    
    <form action="<%$PHPSELF%>" method="POST" class="stdform form-inline">
    <h3>Alle Trenner &auml;ndern</h3>
    &auml;ndern in <select class="form-control" name="delimeter">
    <% foreach from=$HTAEDIT.htad item=trenner %>
        <option value="<% $trenner %>"><% $trenner %></option>
    <%/foreach%>
    </select><%$subbtn%>
    <input type="hidden" name="cmd" value="htachange">
    </form><hr>
    <h3>aktuelle .htaccess</h3>
    <textarea readonly="true" id="htafile" data-theme="<%$gbl_config.ace_theme%>" class="se-html se-readonly"><%$HTAEDIT.htaccesstext%></textarea>
