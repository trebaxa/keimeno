<% if ($selected_item.DID>0) %>
<% assign var=useflags value=true %>
<h1>{LBL_PINEDIT}</h1>
<% else %>
<% assign var=useflags value=false %>
<h1>{LBL_PINADD}</h1>
<% /if %>
<form role="form" id="aform" name="aform" action="<% $PHPSELF %>" method="post">
<input type="hidden" name="valoaction" value="">
    <input type="hidden" name="conid" value="<% $selected_item.CONID %>">
    <input type="hidden" name="id" value="<% $selected_item.DID %>">
    <input type="hidden" name="cmd" value="a_save">
    <input name="token" type="hidden" value="<% $cms_token %>">
    <input type="hidden" name="page" value="<% $page %>">
    <table class="tab_std"  width="100%">
        <% if ($CU_LOGGEDIN==false) %>
        <tr><td>
        <strong>Ihr Name:</strong><br><input class="form-control" type="text" size="60" value="<% $selected_item.username %>" name="FORM[username]">
        <% if ($form_err.username!='') %><span class="important">bitte ausfüllen</span><% /if %>
        </td></tr>
        <%/if%>
        <tr><td>
        <strong>Titel:</strong><br><input size="60" class="form-control" type="text" value="<% $selected_item.title %>" name="FORM_CON[title]">
        <% if ($form_err.title!='') %><span class="important"><% $form_err.title %></span><% /if %>
        </td></tr>
        <tr><td>
        <strong>Einleitung:</strong>
        <% if ($form_err.introduction !='') %><span class="important"><% $form_err.introduction %></span><% /if %>
        <br><textarea class="form-control"  rows="6" cols="60" name="FORM_CON[introduction]"><% $selected_item.introduction %></textarea>
        </td></tr>
        <tr><td>
        <% include file="conflags.tpl" %>        
        </td></tr>        
        <tr><td><strong>Inhalt:</strong>
        <% if ($form_err.content!='') %><br><span class="important"><% $form_err.content %></span><% /if %>
        <br><textarea class="form-control"  rows="9" cols="90" name="FORM_CON[content]"><% $selected_item.content %></textarea>
        </td></tr>
<% if ($gbl_config.captcha_active==1) %>
            <tr>
                <td><strong>{LBL_SECODE}:</strong><br><br>
                <img title="{LBL_SECODE}" alt=""  src="/captcha.php"> <br>
                {LBL_CODEENTER}:<input size="6" name="securecode" class="form-control" type="text">
                <% if ($form_err.capcha!='') %><br><span class="important">Code Eingabe falsch</span><% /if %></td>
            </tr>
<% /if %>        
    </table>
    <div class="std_con_left">

<% if ($selected_item.DID>0) %>
    <input onclick="window.location='<% $PHPSELF %>?page=<% $page %>'" class="btn btn-primary" type="button"  value="{LBL_BACK}">
<% else %>
    <input onclick="window.location='<% $PHPSELF %>?page=<% $page %>'" class="btn btn-primary" type="button"  value="{LBL_CANCEL}">
<% /if %>
&nbsp;
<input type="submit" class="btn btn-primary" href="javascript:document.aform.submit();" value="speichern">

</div>
</form>
<% if ($selected_item.perm.del==true) %>
<div class="std_con_left">
<br><h2>{LBL_ADDITIONOPT}</h2>
<form role="form" name="delform" id="delform" action="<% $PHPSELF %>" method="post">
<input type="hidden" name="id" value="<% $selected_item.DID %>">
<input type="hidden" name="aktion" value="a_del">
<input type="hidden" name="page" value="<% $page %>">
<select class="form-control" name="aktion">
<option value="a_delpin">löschen</option>
</select>
<input class="subbtn" type="submit" value="GO" %>
</form></div>
<% /if %>