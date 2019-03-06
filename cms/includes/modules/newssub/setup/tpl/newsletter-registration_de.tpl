<h1>{LBL_NEWSLETTER}</h1>
<form role="form" action="<% $PHPSELF %>" method="post">
    <table >
        <tbody>
            <tr>
                <td>Nachname</td>
                <td><input name="FORM[nachname]" value="<% $POST.FORM.nachname|hsc %>"> </td>
            </tr>
            <tr>
                <td>{LBL_FORENAME}</td>
                <td><input name="FORM[vorname]" value="<% $POST.FORM.vorname|hsc%>"> </td>
            </tr>
            <tr>
                <td>Email</td>
                <td><input name="FORM[tschapura]" value="<% $POST.FORM.tschapura|hsc%>"> </td>
            </tr>
            <tr>
                <td colspan="2"><% html_subbtn class="btn btn-primary" value="{LBL_REGISTER}" %></td>
            </tr>
        </tbody>
    </table>
<input type="hidden" name="cmd" value="insert"> 
<input type="hidden" name="page" value="<% $page %>">
<input name="token" type="hidden" value="<% $cms_token %>"> 
</form>

<h1>{LBL_UNSUBSCRIBE_NEWSLETTER}</h1>
<form role="form" action="<% $PHPSELF %>" method="post">
    <table >
        <tbody>
            <tr>
                <td>Email</td>
                <td><input name="FORM[tschapura]" value="<% $POST.FORM.tschapura|hsc%>"> </td>
            </tr>
            <tr>
                <td colspan="2">
<% html_subbtn class="btn btn-primary" value="{LBL_UNSUBSCRIBE}" %>
             </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="cmd" value="remove"> 
<input type="hidden" name="page" value="<% $page %>">
<input name="token" type="hidden" value="<% $cms_token %>"> 
</form>