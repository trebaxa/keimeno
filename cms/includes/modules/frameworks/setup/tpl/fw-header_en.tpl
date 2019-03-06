<!DOCTYPE HTML>
<html lang="<% $meta.contentlang %>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><% $meta.title %></title>
<meta name="revisit-after" content="<% $meta.revisit %>">
<meta name="keywords" content="<% $meta.keywords %>">
<meta name="description" content="<% $meta.description %>">
<meta name="author" content="<% $meta.author %>">
<meta name="ROBOTS" content="<% $meta.robots %>">
<meta name="generator" content="Keimeno CMS Engine by trebaxa.com"> 
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<%*<% foreach from=$cssfiles item=cssfile%><link rel="stylesheet" type="text/css" href="<% $PATH_CMS %><%$cssfile%>"><%/foreach%>*%>
<link rel="stylesheet" href="<% $PATH_CMS %>file_data/template/css/template.css">
<link rel="SHORTCUT ICON" type="image/x-icon" href="<% $PATH_CMS %>favicon.ico">
<script type="text/javascript" src="<% $PATH_CMS %>js/<% $gbl_config.jquery_version_script %>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js" type="text/javascript">
<script type="text/javascript" src="<% $PATH_CMS %>cjs/keimeno.js"></script>
<script type="text/javascript" src="<% $PATH_CMS %>cjs/ajax_class.js"></script>
<script type="text/javascript" src="<% $PATH_CMS %>js/jform/jquery.form.js"></script>
<script type="text/javascript" src="<% $PATH_CMS %>js/<% $gbl_config.modernizr %>"></script>

</head>
<body <% $document_protection %>>
<div id="savedresult"></div>
<div class="container">

<div class="row">
    <div class="col-md-6">
        <% include file="flagtable.tpl" %>
    </div>
    <div class="col-md-6 text-right">
        <% if ($customer.kid>0) %>
            <a href="<% $PATH_CMS %>logout.html">Abmelden</a> <a href="{URL_TPL_770}">{LBL_PROFIL}</a>
        <% else %> 
            <a href="#">{LBL_LOGIN}</a> <a href="{URL_TPL_770}">{LBL_REGISTER}</a>
        <% /if %>
        <% if ($customer.kid>0) %> {LBL_WELCOME} <% $customer.vorname %> <% $customer.nachname %><% /if %>
    </div>
</div>    

<div class="row">
    <div class="col-md-12" >
        <div id="header_logo" <% if ($PAGEOBJ.theme_image!="") %>style="background:url(<% $PAGEOBJ.theme_image %>) top left no-repeat;"<%/if%>></div>
        <% if (count($PAGEOBJ.theme_images)>0) %><% include file=$PAGEOBJ.t_themegaltpl %><%/if%>
    </div>
</div>    

<div class="row">
    <div class="col-md-12">
        <nav class="collapse navbar-collapse">
            <%include file=$globl_tree_template %>
        </nav>
    </div>    
</div>

<!--
    <div id="hd_topl">
        <% include file="toplevel.tpl" %> 
    </div>
-->