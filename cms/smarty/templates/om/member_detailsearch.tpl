<h1>{LBL_DETAILMEMBERSEARCH}</h1>

<form action="<% $PHPSELF %>" method="post">
<table class="tab_std">
<tr>
    <td>{LBL_NACHNAME}</td>
    <td><input type="text" name="FORM_NOTEMPTY[nachname]" size="30" value="<% $searchform_notempty.nachname %>"></td>
 </tr>
<tr>
    <td>{LBL_PLZ} / {LBL_ORT}</td>
    <td><input type="text" name="FORM[plz]" size="6" value="<% $searchform.plz %>"> 
    <input type="text" name="FORM[ort]" size="10">
    </td>
</tr>
</table>
<input type="hidden" name="aktion" value="detailsearch">
<input type="hidden" name="page" value="<% $page %>">
<% html_subbtn class="sub_btn" value="{LBL_SEARCH}" %>
</form>
