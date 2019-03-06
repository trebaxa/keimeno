<% if ($cmd=='show_setnewpass') %>
<section class="container">
                <h3>Passwort neu setzen</h3>
            <form role="form" method="POST" action="<% $HTA_CMSSSLLINKS_CMS.EC_URL %>">
              
              <input type="hidden" name="page" value="<%$page%>">
              <input type="hidden" name="cmd" value="set_password">
              
              <div class="form-group">
                  <label class="text-right middle" for="pass1"><i class="fa fa-lock"></i></label>
                  <input type="password" required="" id="pass1" name="pass" placeholder="Neues Passwort">
              </div>
              <div class="form-group">
                  <label class="text-right middle" for="pass2"><i class="fa fa-lock"></i></label>
                  <input type="password" required="" id="pass2" name="passwdh" placeholder="Passwort wiederholen">
              </div>
              
                  <button type="submit" class="button"><i class="fa fa-save"></i>&nbsp; neu speichern</button>
              
            </form>
</section>
 <%elseif ($cmd=='show_setpok') %> 
<section class="section okbox mt-lg">
  <div class="container">
    <div class="alert alert-success">
     Passwort wurde neugesetzt.
   </div>
  </div>
</section>  
 <%else%>  

<section class="container">
    <div class="row">
    <div class="col-md-6">
        <h3>{LBL_LOGIN}</h3>
        <form method="POST" action="<% $PHPSELF %>">
        <input type="hidden" name="page" value="<% $page %>">
        <input type="hidden" name="cmd" value="login">
        
        <div class="form-group">
            <label>{LBL_IHREEMAIL}:</label>
            <input type="text" class="form-control" name="email">
        </div>    
        <div class="form-group">
            <label>{LBL_PASSWORT}:</label>
            <input type="password" name="pass" class="form-control">
            <input type="checkbox" value="1" name="stayloggedin"> angemeldet bleiben        
        </div>
        <button type="submit" class="btn btn-default">{LBL_LOGIN}</button>
        
    </form><br>
        <h3>{LBL_NEUKUNDE}</h3>
        <a href="/register.html" class="btn btn-default">{LBL_REGISTER}</a>
    </div>
    
    <div class="col-md-6">
        <h3>{LBL_PASSWORTVERG}</h3>
        <form action="<% $HTA_CMSSSLLINKS_CMS.EC_URL %>?" method="post">
        <input type="hidden" name="page" value="<% $page %>">
        <input type="hidden" name="cmd" value="send_pass_link">{LBL_PASSINFO}<br>
        <div class="form-group">
            <label>{LBL_IHREEMAIL}:</label>
            <input type="email" required class="form-control" value="@" name="FORM[tschapura]">
            <% if ($loginform_err.email!='') %>                
            <span class="important"><% $loginform_err.email%></span>
            <% /if %>
        </div>
    

        <button type="submit" class="btn btn-default">{LBL_ANFORDERN}</button>
    </form>
    </div>    
</div>    
</section>
<%/if%>