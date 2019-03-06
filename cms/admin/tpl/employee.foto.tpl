<div class="thumbnail">
    <div id="empimg">
        <img class="img-thumbnail" src="<%$empobjform.mi_profil_img%>">
    </div><!-- /#empimg -->
    <div class="caption">
        <h3><% $empobjform.mi_firstname|hsc %> <% $empobjform.mi_lastname|hsc %></h3>
        <small>Login: <% $empobjform.mitarbeiter_name|hsc %></small>
    </div><!-- /.caption-->
    <form action="<%$PHPSELF%>" method="POST" class="jsonform" enctype="multipart/form-data">
        <input type="hidden" name="cmd" value="upload_profil_img">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="id" class="empid" value="<%$GET.id%>">
        
         <div class="form-group">
            <label for="datei" class="sr-only">Foto</label>
            <div class="input-group">
                <input type="text" name="" value="" class="form-control" readonly="" placeholder="Keine Datei ausgewählt"><input id="datei" type="file" name="datei" value="" class="xform-control autosubmit">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">Durchsuchen...</button>
                </span>
            </div><!-- /input-group -->
        </div><!-- /.form-group -->
        
        <div class="form-feet">
            <button class="btn btn-primary" role="button">Upload</button>
            <a href="javascript:void(0)" id="delimgbtnpro" onclick="delete_profil_img(<%$GET.id%>);" class="btn btn-danger" role="button"><i class="fa fa-trash"><!----></i></a>
        </div><!-- /.form-feet -->
    </form>
</div><!-- /.thumbnail -->