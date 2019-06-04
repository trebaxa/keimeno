<%include file="cb.panel.header.tpl" title="Empfänger - {LBLA_NEWSDEACTEMAILS}" %>
<form action="<%$PHPSELF%>" method="post" class="jsonform">
    <input type="hidden" name="epage" value="<%$epage%>">
    <input type="hidden" name="cmd" value="a_deac_news">{LBLA_DEAK_NEWS}:<br>
    <textarea class="form-control se-html" name="emails" rows="10" cols="90"></textarea>
        <%$subbtn%>
</form>
<%include file="cb.panel.footer.tpl" %>    
 
<% if (count($NEWSLETTER.members)>0) %> 
<%include file="cb.panel.header.tpl" title="{LBLA_NEWSMEMBERS}" %>
    <table class="table table-striped table-hover" id="customer-table">
    <thead>
        <tr>
           <th>Kunde</th>
	       <th>Email</th>
	       <th>Knr</th>           
	       <th></th>
        </tr>
    </thead>
    <tbody>
    <% foreach from=$NEWSLETTER.members item=row %>
        <tr>
            <td><%$row.nachname%>, <%$row.vorname%></td>
            <td><%$row.email%></td>
            <td><%$row.kid%></td>            
            <td><% foreach from=$row.icons item=icon %><%$icon%><%/foreach%></td>
        </tr>  
        <%/foreach%>
       </tbody> 
    </table>
<%include file="cb.panel.footer.tpl" %>    
<%/if%>