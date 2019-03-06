<% if ($section=='done') %>
<h1>{LBL_DANKENACHRICHT}</h1>
<%else%>
<h1>{LBL_KONTAKTCAPTION}</h1>
<form role="form" action="<% $PHPSELF %>" method="post" enctype="multipart/form-data" class="jsonform">
<input name="token" type="hidden" value="<% $cms_token %>">   
    <input type="hidden" name="page" value="<% $page %>">
    <input type="hidden" name="cmd" value="sendmsg">
    <input type="hidden" name="ajaxsubmit" value="1">
    <div align="left">
    <table class="tab_std" width="100%" >
        <tbody>
            <tr>
                <td width="10%">{LBL_NACHNAME}:</td>
                <td width="90%"><input class="text" size="38" name="FORM_NOTEMPTY[nachname]" value="<% $CONTACTF.values.nachname|sthsc %>" class="form-control" type="text"></td>
                <td valign="top" rowspan="5">&nbsp;</td>
            </tr>
            <tr>
                <td width="10%">{LBL_VORNAME}:</td>
                <td width="90%"><input class="text" size="38" name="FORM[vorname]" value="<% $CONTACTF.values.vorname|sthsc %>" class="form-control" type="text"></td>
            </tr>
            <tr>
                <td width="10%">Email:</td>
                <td width="90%"><input class="text" size="26" autocomplete="OFF" name="FORM[tschapura]" value="<% $CONTACTF.values.tschapura|sthsc %>" class="form-control" type="text"></td>
            </tr>
            <tr>
                <td width="10%">{LBL_TELEFON}:</td>
                <td width="90%"><input class="text" name="FORM[telefon]" value="<% $CONTACTF.values.telefon|sthsc %>" class="form-control" type="text"></td>
            </tr>
      <tr>
                <td width="10%">Datei:</td>
                <td width="90%"><input class="text" name="datei"  type="file"></td>
            </tr>
            <tr>
                <td width="10%">{LBL_NACHRICHT}:</td>
                <td width="90%"><textarea class="form-control"  rows="12" cols="60" name="FORM_NOTEMPTY[nachricht]"><% $CONTACTF.values.nachricht|sthsc %></textarea></td>
            </tr>
<% if ($gbl_config.captcha_active==1) %>
            <tr>
                <td width="10%">{LBL_SECODE}:</td>
                <td width="90%"><img title="{LBL_SECODE}" alt=""  src="/captcha.php"> <br>
                {LBL_CODEENTER}:<input size="6" name="securecode" class="form-control" type="text"></td>
            </tr>
<% /if %>
        </tbody>
    </table>
    </div>
<br>
    <% html_subbtn class="btn btn-primary" value="{BTN_SENDEN}" %>
</form>
<% /if %>
