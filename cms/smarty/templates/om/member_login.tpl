<h1>{LBL_LOGIN}</h1>
<form method="POST" action="<% $PHPSELF %>">
        <input type="hidden" name="page" value="<% $page %>"><br>
                <input type="hidden" name="cmd" value="login"><br>

{LBL_IHREEMAIL}: <br>
        <input type="text" class="text" name="email" size="15"><br>
        {LBL_PASSWORT}:<br>
        <input type="password" name="pass" class="text" size="15"><br><br>
        <input type="checkbox" value="1" name="stayloggedin"> angemeldet bleiben        
        <br>
        <% html_subbtn class="sub_btn" value="{LBL_LOGIN}" %>
    </form><br>


<br>
<h1>{LBL_NEUKUNDE}</h1>
<br>
<% html_subbtn class="sub_btn" href="register.html" value="{LBL_REGISTER}" %><br><br>
<h1>{LBL_PASSWORTVERG}</h1>
<form action="<% $HTA_CMSSSLLINKS_CMS.EC_URL %>?" method="post">
<input type="hidden" name="page" value="<% $page %>">
    <input type="hidden" name="cmd" value="sendpass">{LBL_PASSINFO}<br>
    <br>
    {LBL_IHREEMAIL}: <br>
    <input type="text" class="text" size="20" value="@" name="email">
    <% if ($loginform_err.email!='') %>                
<span class="important"><% $loginform_err.email%></span>
<% /if %>
    <br>
    <b>{LBL_ODER}...</b><br>
    {LBL_KNR}:<br>
    <input type="text" class="text" size="20" name="knr"><br><br>
    <% html_subbtn class="sub_btn" value="{LBL_ANFORDERN}" %></form><br>
    
    <% include file="facebook_login.tpl" %>
