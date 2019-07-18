<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label>{LA_ANREDE}</label>
            <select class="form-control custom-select" name="FORM[anrede_sign]">
                <%$MEMINDEX.anrede_arr%>                                  
            </select>
        </div><!-- /.form-group -->
    </div><!-- /.col-md-2 -->
    <div class="col-md-5">
        <div class="form-group">
            <label for="fstnm">{LA_VORNAME}</label>
            <input id="fstnm" type="text" class="form-control" placeholder="{LA_VORNAME}" value="<% $CUSTOMER.vorname|sthsc %>" name="FORM[vorname]">
        </div><!-- /.form-group -->
    </div><!-- /.col-md-5 -->
    <div class="col-md-5">
        <div class="form-group">
            <label for="lstnm">{LA_NACHNAME}</label>
            <input id ="lstnm" type="text" class="form-control" placeholder="{LA_NACHNAME}" value="<% $CUSTOMER.nachname|sthsc %>" name="FORM[nachname]">
        </div><!-- /.form-group -->
    </div><!-- /.col-md-5 -->
</div><!-- /.row -->
<div class="form-group">
    <label>{LA_LAND}</label>
    <%$MEMINDEX.landselect%>
</div><!-- /.form-group -->
<div class="form-group">
    <label for="cmail">{LA_EMAIL}</label>
    <input id="cmail" placeholder="{LA_EMAIL}" value="<% $CUSTOMER.email|hsc %>" name="FORM[email]" type="email" class="form-control">
</div><!-- /.form-group -->

<script>
    function load_customer(kid) {
        window.location.href="<%$eurl%>aktion=show_edit&kid="+kid;        
    }
</script>