<h2><% $EM.mailtemplate.title %><small>[<%$EM.mailtemplate.id%>]</small></h2>
	<% if ($GBLPAGE.access.language==TRUE)%>
    <div class="row">
        <div class="col-md-6">
        <h3>Editor</h3>
    	<form class="jsonform" method="post" action="<%$PHPSELF%>">
        	<input type="hidden" name="id" value="<% $EM.mailtpl.id %>">
        	<input type="hidden" name="epage" value="<%$epage%>">
        	<input type="hidden" name="email_id" value="<%$GET.id%>">
        	<input type="hidden" name="cmd" value="save_mail_template">
        	<input type="hidden" name="lang_id" value="<%$GET.uselang%>">
    
        	<div class="form-group">
        		<label>{LBL_LANGUAGE}:</label>
        		<%$ADMIN.langselect%>
        	</div>	
        	<div class="form-group">
        		<label>{LBL_SUBJECT}:</label>
        		<input type="text" class="form-control" required name="FORM[email_subject]" size="61" value="<% $EM.mailtpl.email_subject|hsc %>">
        	</div>
        	<div class="form-group">
        		<label>Module:</label>
        		<select class="form-control" name="module_id">           
                    <option <% if ($EM.mailtemplate.module_id=="") %>selected<%/if%> value="">System</option>
                    <% foreach from=$EM.modlist item=row  %>
                         <option <% if ($EM.mailtemplate.module_id|lower==$row.settings.id) %>selected<%/if%> value="<%$row.settings.id%>"><%$row.settings.module_name%></option>
                    <%/foreach%>
                </select>
        	</div>   
        	<div class="form-group">
        		<label>Text:</label>
        		<textarea class="form-control se-html" data-theme="<%$gbl_config.ace_theme%>" required="" name="FORM[content]"><% $EM.mailtpl.content|hsc %></textarea>		
            </div>
        	
        	<div class="subright"><%$subbtn%></div>    	
    	</form>
    </div>
    <div class="col-md-6">
    
  	<form class="jsonform form-inline" method="post" action="<%$PHPSELF%>">
    	<input type="hidden" name="id" value="<%$REQUEST.id%>">
    	<input type="hidden" name="epage" value="<%$epage%>">
    	<input type="hidden" name="cmd" value="save_recipient_matrix">     	
        <h3>Empf&auml;nger dieses Tempalte (Admin in Copy)</h3>
        <table  class="table table-striped table-hover">
        <thead><tr>
                <th>Name</th>
                <th>Email</th>
                <th>Aktiv</th>
            </tr></thead>     
        <% foreach from=$employees item=row  %>
             <% if ($row.MID<>100) %>
             <tr>
                <td><a href="run.php?epage=employee.inc&id=<%$row.MID%>&cmd=edit"><% $row.mitarbeiter_name%></a></td>
                <td><% $row.email%></td>
                <td >
                    <% if ($row.email!="") %>
                    <input <% if ($row.MID|in_array:$EM.selected_mid) %>checked<%/if%> type="checkbox" name="FORM[<%$row.MID%>]" value="1">
                    <%else%>
                    -
                    <%/if%>
                </td>            
            </tr>    
            <%/if%>    
         <%/foreach%>
         </table>
    	<div class="subright"><%$subbtn%></div>    
    	</form>
    </div>
</div>        
   	
    <div class="row">

        <div class="col-md-6">
            <h3>Variablen für Firmenangaben</h3>
            <table class="table"><% $EM.legende %></table>
                
            <% if ($EM.mailtemplate.admin != 1) %>
                <form action="<%$PHPSELF%>" method="post">
            		<input type="hidden" value="delete" name="aktion">
            		<input type="hidden" value="<%$GET.id%>" name="id">
            		<input type="hidden" name="epage" value="<%$epage%>">
            		<input type="submit" value="{LBL_DELETE}" class="btn btn-primary">
        		</form>
            <%else%>    
                <div class="bg-info text-info">Dieses Template kann nicht gel&ouml;scht werden, <br>da es ein fester Bestandteil des CMS ist.</div>
            <%/if%>
    	</div>
                <div class="col-md-6">
        <h3>Spezielle Variablen</h3>
            <table class="table">
                <tbody>
                <tr>
                    <td>!!ANREDE!!</td>
                    <td>Anrede</td>
                </tr>
                <tr>
                    <td>!!DATE!!</td>
                    <td> aktuelles Datum: Format dd.mm.YYYY</td>
                </tr>
                    <tr>
                    <td>!!EMAIL!!</td>
                    <td>Kunden Email</td>
                </tr>
                <tr>
                    <td>!!PASSWORT!!</td>
                    <td>Kunden-Passwort</td>
                </tr>    
                <tr>
                    <td>!!LOGINNAME!!</td>
                    <td>Kunden Login Name</td>
                </tr>
                <tr>
                    <td>!!CMS_LINK!!</td>
                    <td>Domain</td>
                </tr>
                <tr>
                    <td>!!LINK_TO_HOMEPAGE!!</td>
                    <td>Link zum zur Homepage</td>
                </tr>
                <tr>
                    <td>!!ACTIVATE_LINK!!</td>
                    <td>Kunde Account Aktivierungslink</td>
                </tr>
                </tbody>                
            </table>
        </div>
	</div>    
<% else %>
<%include file="no_permissions.admin.tpl" %>
<%/if%>    