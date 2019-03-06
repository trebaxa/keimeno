<!DOCTYPE HTML>
<html lang="<% $meta.contentlang %>">
<head>
<title><% $meta.title %></title>
<meta charset="utf-8">
<meta name="revisit-after" content="<% $meta.revisit %>">
<meta name="keywords" content="<% $meta.keywords %>">
<meta name="description" content="<% $meta.description %>">
<meta name="author" content="<% $meta.author %>">

<meta name="ROBOTS" content="<% $meta.robots %>">
<meta name="generator" content="Keimeno CMS Engine by trebaxa.com"> 
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<% $PATH_CMS %>apple-touch-icon-144x144-precomposed.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<% $PATH_CMS %>apple-touch-icon-114x114-precomposed.png"/>
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<% $PATH_CMS %>apple-touch-icon-72x72-precomposed.png"/>
<link rel="apple-touch-icon-precomposed" sizes="57x57" href="<% $PATH_CMS %>apple-touch-icon-57x57-precomposed.png"/>
<link rel="apple-touch-icon-precomposed" href="<% $PATH_CMS %>apple-touch-icon-precomposed.png" />
<link rel="apple-touch-icon" href="<% $PATH_CMS %>apple-touch-icon.png" />
<% foreach from=$cssfiles item=cssfile%><link rel="stylesheet" type="text/css" href="<% $PATH_CMS %><%$cssfile%>"><%/foreach%>
<link rel="stylesheet" type="text/css" href="<% $PATH_CMS %>js/images/calendar/calendar.css">
<link rel="SHORTCUT ICON" type="image/x-icon" href="<% $PATH_CMS %>favicon.ico">
<script type="text/javascript" src="<% $PATH_CMS %>js/<% $gbl_config.jquery_version_script %>"></script>
<script type="text/javascript" src="<% $PATH_CMS %>js/autosubmit/jquery.form.js"></script>
<script type="text/javascript" src="<% $PATH_CMS %>cjs/keimeno.js"></script>
<script type="text/javascript" src="<% $PATH_CMS %>cjs/ajax_class.js"></script>
<script type="text/javascript" src="<% $PATH_CMS %>js/jform/jquery.form.js"></script>
<script type="text/javascript" src="<% $PATH_CMS %>js/<% $gbl_config.modernizr %>"></script>

</head>
<body <% $document_protection %>>
<div id="savedresult"></div>
<div id="container">

<div id="toplinks">
<% if ($customer.kid>0) %>
 <a href="<% $PATH_CMS %>logout.html">Abmelden</a>|<a href="<%$HTA_CMSFIXLINKS.EB_URL%>">{LBL_PROFIL}</a>
<% else %> 
 <a href="<% $HTA_CMSSSLLINKS.EC_URL %>">{LBL_LOGIN}</a>|<a href="<%$HTA_CMSFIXLINKS.EB_URL%>">{LBL_REGISTER}</a>
<% /if %>
</div>

<div id="header_logo" <% if ($PAGEOBJ.theme_image!="") %>style="background:url(<% $PAGEOBJ.theme_image %>) top left no-repeat;"<%/if%>>
<% if (count($PAGEOBJ.theme_images)>0) %>
    <% include file=$PAGEOBJ.t_themegaltpl %>
<%/if%>
</div>


<div id="header_bar">
<div id="hd_flags"><% include file="flagtable.tpl" %></div>
    <div id="hd_topl">
        <% include file="toplevel.tpl" %> 
    </div>
</div>

<!--MAIN-->
<div id="main_container">
