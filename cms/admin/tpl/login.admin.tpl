<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keimeno Login</title>
    <link rel="stylesheet" href="./assets/fonts/fontawesome/css/all.css">
    <link href="./assets/css/app.all.min.css?<%$smarty.now|date_format:"%d%m%Y"%>" rel="stylesheet">    
    <link rel="stylesheet" type="text/css" href="./assets/css/layout-login.css?<%$smarty.now|date_format:"%d%m%Y"%>">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="shortcut icon" href="./images/favicon.ico" />
    <script src="./assets/js/jquery.min.js"></script><!-- 331 -->
    <script src="./assets/js/app.js"></script>            
    <script src="./assets/js/functions.js"></script>
    <script type="text/javascript" src="./assets/js/login.js"></script>
  </head>
  <body id="admin-login-wrapper">

    <div class="container">
<div class="row">
      <div class="col-xs-12 col-xs-offset-0 col-sm-4 col-sm-offset-4 col-md-4 offset-md-4">
        <div id="login-box">
          <div class="login-header">
            <h1 class="sr-only">Admin Login</h1>
          </div>
          <% if ($login_log.next > $time) %>
              <div class="login-message text-center">
                <span>Sie m&uuml;ssen nun <span class="badge" id="js-seconds"><%$restart_in%></span> Sekunden warten, bis der n&auml;chste Login m&ouml;glich ist.</span>
              </div>
                <script>
                  setTimeout("set_login_secs(<%$restart_in%>)", 1000);
                </script>                     
             <% else %>
        <% if ($GET.failed==1) %>
            <div class="login-message text-center">
                <span>{LA_LOGINFEHLGESCHLAGEN}</span>
              </div>
        <%/if%>
        <% if ($runable==false) %>
              <div class="login-message text-center">
                <span>Pro Datenbank darf nur eine CMS Instanz installiert werden.</span>
              </div>        
        <%/if%>
        <form action="index.php?cmd=login" method="post">
        <input type="hidden" name="token" value="<%$cms_token%>"/>
            <div class="login-body">
              <div class="form-group">
                <label for="username" class="sr-only">Benutzername</label>
                <input type="text" placeholder="Username" name="FORM[mitindent]" id="username" class="form-control input-lg" required <% if ($gbl_config.admin_password_autocomplete==1) %>autocomplete="off"<%/if%>>
              </div>
              <div class="form-group">
                <label for="password" class="sr-only">Passwort</label>
                <input type="password" placeholder="****" name="FORM[password]" id="password" class="form-control input-lg" required <% if ($gbl_config.admin_password_autocomplete==1) %>autocomplete="off"<%/if%>>
              </div>
            </div>
            <div class="login-footer text-center">
              <button type="submit" class="btn btn-login btn-lg"><i class="fa fa-sign-in"></i> Login</button>
            </div>
          </form>             
                 
             <%/if%>
             <div class="terms text-center">
                  <p>&copy; 2005-<%'Y'|date%> <a href="https://www.keimeno.de" target="_blank">Trebaxa GmbH & Co.KG</a> All Rights Reserved.
                  This work is licensed under GNU GENERAL PUBLIC LICENSE Version 2 or higher.</p>
                </div>
          
            </div>
        </div>
    </div>
</div>
<script>
    if ($('#sidebar-menu').length>0) {
        window.location.href="<%$PATH_CMS%>admin";
    }
</script>
  </body>
</html>
