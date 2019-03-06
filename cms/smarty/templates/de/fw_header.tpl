<!DOCTYPE HTML>
<html lang="<% $meta.contentlang %>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><% $meta.title %></title>
<meta name="keywords" content="<% $meta.keywords %>">
<meta name="description" content="<% $meta.description %>">
<meta name="author" content="<% $meta.author %>">
<meta name="ROBOTS" content="<% $meta.robots %>">
<meta name="generator" content="Keimeno CMS"> 
<%*<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">*%>
<!--[if lt IE 9]>
       <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
       <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="<% $PATH_CMS %>file_data/template/css/template.css">
<link rel="icon" type="image/x-icon" href="<% $PATH_CMS %>favicon.ico">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js" ></script>
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet"> 
<link href="https://fonts.googleapis.com/css?family=Caveat" rel="stylesheet"> 

</head>
<body <% $document_protection %> id="start">
<% include file="menu_tree.tpl" %>
<% if ($PAGEOBJ.theme_image!="") %>
  <div id="header_logo" style="background:url(<% $PAGEOBJ.theme_image %>) no-repeat center center ; 
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;"></div>
<%/if%>  

<%*
<div class="row">
    <div class="col-md-6">
        <% include file="flagtable.tpl" %>
    </div>
    <div class="col-md-6 text-right">
        <% if ($customer.kid>0) %>
            <a href="<% $PATH_CMS %>logout.html">Abmelden</a> <a href="{URL_TPL_770}">{LBL_PROFIL}</a>
        <% else %> 
            <a href="javascript:void(0);" data-toggle="modal" data-target="#login-modal">{LBL_LOGIN}</a> <a href="{URL_TPL_770}">{LBL_REGISTER}</a>
        <% /if %>
        <% if ($customer.kid>0) %> {LBL_WELCOME} <% $customer.vorname %> <% $customer.nachname %><% /if %>
    </div>
</div>    
<div id="hd_topl">
    <% include file="toplevel.tpl" %> 
</div>
*%>