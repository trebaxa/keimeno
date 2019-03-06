<div style="margin:10px 0px 10px 0px;text-align:right">
<a href="javascript:void(0)" class="cmsbtnhref" onclick="dc_show('feedback-ins',600)">Beitrag hinzufügen</a>
</div>
<div style="display:none" id="feedback-ins" class="divframe">
<form role="form" action="<% $PHPSELF %>" method="post">
    <table >
        <tbody>
            <tr>
                <td>Vorname, Nachname</td>
                <td><input size="38" name="FORM[kname]" value="<% $POST.FORM.kname %>" class="form-control" type="text"></td>
            </tr>
            <tr>
                <td>Ihre Emailadresse:</td>
                <td><input size="38" name="FORM[tschapura]" value="<% $POST.FORM.tschapura %>" class="form-control" type="text"></td>
            </tr>
            <tr>
                <td colspan="2">Ihre Nachricht:<br>
                <textarea class="form-control"  rows="15" cols="46" name="FORM[feedback]"><% $POST.FORM.feedback%></textarea></td>
            </tr>
            <% if ($gbl_config.capcha_active==1) %>
         <tr>
                <td width="10%">{LBL_SECODE}:</td>
                <td width="90%"><img title="{LBL_SECODE}" alt=""  src="<% $PATH_CMS %>captcha.php"> <br>
                {LBL_CODEENTER}:<input size="6" name="securecode" class="form-control" type="text"></td>
            </tr>
            <%/if%>
        </tbody>
    </table>
    <input type="hidden" name="page" value="<% $page %>">
    <input type="hidden" name="cmd" value="add_item">
    <input name="token" type="hidden" value="<% $cms_token %>">  
<% html_subbtn class="btn btn-primary" value="{BTN_SENDEN}" %>
</form>
</div>

<h1>Feedbacks</h1>


<% if (count($feedback)>0) %>
<table class="tab_std"  width="100%">
  <tbody>
<% foreach from=$feedback item=fb_obj %>
<tr><td>
{LBL_ABSENDER} <% $fb_obj.fb_customer %><br>
 {LBL_GESCHRIEBENAM} <% $fb_obj.fb_date %><br>
 <% $fb_obj.fb_text%>
</td></tr>
     <tr>
            <td><hr style="border-right: #cccccc 1px solid; border-top: #cccccc 1px solid; border-left: #cccccc 1px solid; border-bottom: #cccccc 1px solid">
            </td>
           
        </tr>
<% /foreach %>
</tr>
    </tbody>
</table>
<% /if %>