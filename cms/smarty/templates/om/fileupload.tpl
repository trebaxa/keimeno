<% if $fileup.isForm==true %>
<form action="<% $PHPSELF %>" method="post" enctype="multipart/form-data">
<% /if %>
  <table class="tab_std" width="100%">
        <tr>
            <td valign="top"><strong><% $fileup.file_desc %>:</strong><br>
            <input type="file" name="datei" size="30" class="submit" value="Suche">
            <br>{LBL_ALLOWEDFILESIZE}:<% $gbl_config.max_pic_size %>KB</td>
            <% if ($fileup.picture!="") %>
            <td valign="top" align="center">
                <% if $fileup.canDelete==true && $fileup.picture!="" %>
                <img <% $fileup.mouseover %> src="<% $fileup.picture %>" >
                <br><span class="small"><% $fileup.picture_dim.width %>x<% $fileup.picture_dim.height %>px</span>
                <br><a <% $fileup.confirm %> title="lÃ¶schen" href="<% $fileup.del_link %>">lÃ¶schen</a>
                <% /if %>
            </td>
            <% /if %>
        </tr>
    </table>
    <input type="hidden" name="ftarget" value="<% $fileup.ftarget %>">
    <input type="hidden" name="page" value="<% $page %>">
    <input type="hidden" name="force_ext" value="<% $fileup.force_ext %>">
<% if $fileup.isForm==true %>
    <% html_subbtn class="sub_btn" value="{LBL_UPLOAD}" %>
    <input type="hidden" name="aktion" value="<% $fileup.aktion %>">
</form>
<% /if %>
