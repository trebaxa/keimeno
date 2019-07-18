<%include file="cb.page.title.tpl" icon="far fa-file-alt" title="HTA Access bearbeiten"%>


    <form class="stdform form-inline" action="<%$PHPSELF%>" method="POST">
        <input type="hidden" name="cmd" value="htasaveconf">
        <input type="hidden" name="epage" value="<%$epage%>">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                <th>{LBL_HTADESCRIPTION}</th>
                <th>{LBL_HTAPREFIX} / {LBL_HTADELIMETER}</th>
                <% if ($gbl_config.debug_mode==1) %><th></th><%/if%>                
                <th>SMARTY</th>       
                <th>Page ID</th>
                </tr>
           </thead>
            <tbody>
                <%$HTAEDIT.table%>
            </tbody>
        </table>
        
     <%include file="cb.panel.header.tpl" title="Anfang - Manuelle Einstellungen (nur für Experten!)"%>
        <textarea data-theme="<%$gbl_config.ace_theme%>" class="form-control se-html" wrap="off" name="CONFIG[hta_specialtext_first]" rows="20" cols="130"><% $gbl_config.hta_specialtext_first|hsc %></textarea>
     <%include file="cb.panel.footer.tpl"%>  
     
     <%include file="cb.panel.header.tpl" title="Ende - Manuelle Einstellungen (nur für Experten!)"%>   
        <textarea data-theme="<%$gbl_config.ace_theme%>" class="form-control se-html" wrap="off" name="CONFIG[hta_specialtext]" rows="20" cols="130"><% $gbl_config.hta_specialtext|hsc %></textarea>
     <%include file="cb.panel.footer.tpl"%>   
     <%$subbtn%>
     </form>

    
    <%include file="cb.panel.header.tpl" title="Alle Trenner &auml;ndern"%>
        <form action="<%$PHPSELF%>" method="POST" class="stdform form-inline">
            &auml;ndern in <select class="form-control custom-select" name="delimeter">
            <% foreach from=$HTAEDIT.htad item=trenner %>
                <option value="<% $trenner %>"><% $trenner %></option>
            <%/foreach%>
            </select><%$subbtn%>
            <input type="hidden" name="cmd" value="htachange">
        </form>
    <%include file="cb.panel.footer.tpl"%>
    
    
    <%include file="cb.panel.header.tpl" title="aktuelle .htaccess"%>
        <textarea readonly="true" id="htafile" data-theme="<%$gbl_config.ace_theme%>" class="se-html se-readonly"><%$HTAEDIT.htaccesstext%></textarea>
    <%include file="cb.panel.footer.tpl"%>
