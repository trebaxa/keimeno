<div class="page-header"><h1><i class="fa fa-file-code-o"></i>Online Anträge</h1></div>

<% if ($aktion=='archives') %>
    <%include file="os.archive.tpl"%>
<%/if%>	


<% if ($aktion=='showsheets') %>
    <%include file="os.sheets.tpl"%>	
<%/if%>


<% if ($aktion=='edit') %>
    <h3>{LBL_ONLINESHEET} <% if ($sheetid>0) %> - <% $sheet_obj.s_name %><%/if%></h3>
    <% if ($GBLPAGE.access.language==TRUE)%>
        <form class="jsonform" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
            <input type="hidden" name="sheetid" value="<%$sheetid%>">
            <input type="hidden" name="cmd" value="msave">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="uselang" value="<%$sheet_obj.t_langid%>">
            <input type="hidden" name="FORMSHEETLANG[t_langid]" value="<%$sheet_obj.t_langid%>">
            
            <% if ($sheetid==0) %>
                <h3>Anlegen</h3>
                <div class="form-group">
                	<label>Title (admin):</label>
                	<input type="text" class="form-control" name="FORMSHEET[s_name]" value="<%$sheet_obj.s_name%>">
                </div>                
                <% $subbtn %>    
            <%/if%>

        <% if ($sheetid>0) %>
            <div class="form-group">
                	<label>Sprache:</label>
                     <%$uselangselect%>
            </div>         
            <div class="form-group">
                	<label>Title (admin):</label>
                	<input type="text" class="form-control" name="FORMSHEET[s_name]" value="<%$sheet_obj.s_name%>">
            </div>     
            <h3>Felder</h3>
            <div class="btn-group"><a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=addfield&sheetid=<%$sheetid%>">Neues Feld anlegen</a></div>
            <%include file="os.fields.tpl"%>
    
    
            <h3>Formular bearbeiten</h3>            
                <div class="form-group"><label>Formular:</label><textarea class="se-html" name="FORMSHEETLANG[t_content]"><%$sheet_obj.t_content|hsc%></textarea></div>
                <div class="form-group"><label>Nachricht an Kunden nach erfolgreichem Absenden:</label><textarea class="se-html" name="FORMSHEETLANG[t_donemsg]"><%$sheet_obj.t_donemsg|hsc%></textarea></div>
                <div class="form-group"><label>Zus&auml;tzlicher Text (nur sichtbar in PDF)</label><textarea class="se-html" name="FORMSHEETLANG[t_signtext]"><%$sheet_obj.t_signtext|hsc%></textarea></div>

            <input type="hidden" name="FORMSHEETLANG[t_sid]" value="<%$sheetid%>">
            <% $btnsave %>
        <%/if%>
        </form>
    <% else %>
        <%include file="no_permissions.admin.tpl" %>
    <%/if%>
<%/if%>
