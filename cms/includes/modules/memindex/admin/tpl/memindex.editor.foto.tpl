<div class="thumbnail">
    <div id="empimg">
        <img class="img-thumbnail" id="kreg-theme-img" src="./images/axloader.gif">
    </div><!-- /#empimg -->
    <div class="caption">
        <h3><% $CUSTOMER.anrede|hsc %> <% $CUSTOMER.vorname|hsc %> <% $CUSTOMER.nachname|hsc %></h3>
        <% if ($REQUEST.aktion=="show_edit") %>
            <small>Eingetragen</label> <% $CUSTOMER.datum%></small>
        <%/if%>
        <%* $CUSTOMER|echoarr *%>
    </div>
    <form action="<%$PHPSELF%>" method="POST" class="jsonform" enctype="multipart/form-data">
        <input type="hidden" name="cmd" value="change_cust_foto">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="kid" value="<% $CUSTOMER.kid %>">

        <div class="form-group">
                <label for="datei" class="sr-only">Foto</label>
                <div class="input-group">
                    <input type="text" name="" value="" class="form-control" readonly="" placeholder="Keine Datei ausgewählt"><input id="datei" type="file" name="datei" value="" class="xform-control autosubmit">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="button">Durchsuchen...</button>
                    </span>
                </div><!-- /input-group -->
            </div><!-- /.form-group -->
        <div class="form-feet mb-3">
            <button class="btn btn-primary" role="button">Upload</button>
            <a href="javascript:void(0)" id="delimgbtnpro" class="btn btn-danger del_cust_img" role="button"><i class="fa fa-trash"><!----></i></a>
        </div><!-- /.form-feet -->
    </form>
</div><!-- /.thumbnail -->

<script>
$( ".del_cust_img" ).click(function() {
                $('.img-thumbnail').attr('src','../images/opt_member_nopic.jpg');
                $(this).hide();
                execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=del_img&kid=<% $CUSTOMER.kid %>');
            });
</script>
