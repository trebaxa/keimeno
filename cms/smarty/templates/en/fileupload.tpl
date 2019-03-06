<% if $fileup.isForm==true %>
<form role="form" action="<% $PHPSELF %>" method="post" enctype="multipart/form-data">
<% /if %>
    <div class="form-group">
                <label for="datei"><% $fileup.file_desc %></label>
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""></input>
                    <input id="datei" class="xform-control" type="file" onchange="this.previousElementSibling.value = this.value" value="" name="datei"></input>
                    <div class="input-group-btn"><button class="btn btn-default" type="button">Durchsuchen...</button></div>
                </div>
    </div>      
    <span class="help-block">{LBL_ALLOWEDFILESIZE}:<% $gbl_config.max_pic_size %>KB</span>
    <% if $fileup.canDelete==true && $fileup.picture!="" %>
                <img <% $fileup.mouseover %> src="<% $fileup.picture %>" >
                <br><small><% $fileup.picture_dim.width %>x<% $fileup.picture_dim.height %>px</small>
                <br><a <% $fileup.confirm %> title="löschen" href="<% $fileup.del_link %>">löschen</a>
                <% /if %>
    <input type="hidden" name="ftarget" value="<% $fileup.ftarget %>">
    <input type="hidden" name="page" value="<% $page %>">
    <input type="hidden" name="force_ext" value="<% $fileup.force_ext %>">
<% if $fileup.isForm==true %>
    <button class="btn btn-primary" type="submit">{LBL_UPLOAD}</button>
    <input type="hidden" name="aktion" value="<% $fileup.aktion %>">
</form>
<% /if %>