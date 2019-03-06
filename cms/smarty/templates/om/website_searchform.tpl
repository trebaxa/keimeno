<div id="searchbox">
<form action="<% $searchformurl %>" method="post">
    <table >
        <tbody>
            <tr>
                <td><input class="field" onclick="this.value=''" size="6" name="FORM[keyword]" value="<%$POST.FORM.keyword%>"> </td>
                <td valign="middle"><input type="submit" class="btn" value="suchen"></td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="cmd" value="fulltextsearch">    
</form>
</div>
