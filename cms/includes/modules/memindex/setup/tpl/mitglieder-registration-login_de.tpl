<section class="container">
    <div class="row">
    <div class="col-md-6">
        <h3>{LBL_LOGIN}</h3>
        <form method="POST" action="<% $PHPSELF %>">
        <input type="hidden" name="page" value="<% $page %>"><br>
        <input type="hidden" name="cmd" value="login"><br>
        
        <div class="form-group">
            <label>{LBL_IHREEMAIL}:</label>
            <input type="text" class="form-control" name="email">
        </div>    
        <div class="form-group">
            <label>{LBL_PASSWORT}:</label>
            <input type="password" name="pass" class="form-control">
            <input type="checkbox" value="1" name="stayloggedin"> angemeldet bleiben        
        </div>
        <% html_subbtn class="btn btn-default" value="{LBL_LOGIN}" %>
    </form><br>
        <h3>{LBL_NEUKUNDE}</h3>
        <% html_subbtn class="btn btn-default" href="register.html" value="{LBL_REGISTER}" %>
    </div>
    
    <div class="col-md-6">
        <h3>{LBL_PASSWORTVERG}</h3>
        <form action="<% $HTA_CMSSSLLINKS_CMS.EC_URL %>?" method="post">
        <input type="hidden" name="page" value="<% $page %>">
        <input type="hidden" name="cmd" value="sendpass">{LBL_PASSINFO}<br>
        <div class="form-group">
            <label>{LBL_IHREEMAIL}:</label>
            <input type="email" required class="form-control" value="@" name="email">
            <% if ($loginform_err.email!='') %>                
            <span class="important"><% $loginform_err.email%></span>
            <% /if %>
        </div>
    <b>{LBL_ODER}...</b><br>
    <div class="form-group">
            <label>{LBL_KNR}:</label>
            <input type="text" class="form-control"  name="knr">
    </div>        
    <% html_subbtn class="btn btn-default" value="{LBL_ANFORDERN}" %>
        
    </form>
    </div>    
</div>    
</section>    