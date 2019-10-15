<!DOCTYPE HTML>
<html lang="de">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><% $gbl_config.adr_firma%> <%$HEADER_PAGE.current_year%> CMS - Manager</title>
        <link rel="shortcut icon" href="../images/favicon.ico" />
       <% if ($DEBUG==1) %>
            <link href="./assets/vendors/DataTables/datatables.min.css" rel="stylesheet">
            <link rel="stylesheet" href="./assets/vendors/jstree/dist/themes/default/style.min.css" />
            <link rel="stylesheet" href="./assets/fonts/fontawesome/css/all.css">
            <link href="./assets/vendors/dropzone/dropzone.css" rel="stylesheet">
            <link href="./assets/css/app.all.min.css" rel="stylesheet">
            <!-- THME 2019-->
            <script src="./assets/js/jquery.min.js"></script><!-- 331 -->
            <script src="./assets/js/app.js"></script>
            <script src="./assets/vendors/jform/jquery.form.js"></script>
            <script src="./assets/vendors/jstree/dist/jstree.min.js"></script>
            <script src="./assets/js/functions.js"></script>
            <script src="./assets/vendors/tinymce/tinymce.min.js"></script>
        <%else%>
            <link rel="stylesheet" href="./assets/fonts/fontawesome/css/all.css">
            <link href="./assets/css/app.all.min.css?<%$smarty.now|date_format:"%d%m%Y"%>" rel="stylesheet">
            <script src="./assets/js/functions.min.js"></script>
            <script src="./assets/vendors/tinymce/tinymce.min.js"></script>
<style>
.menu_section > ul > li .sub-menu li .sub-sub-link a:not(.toggle-btn) {
    flex-basis:calc(100% - 3rem);
    }
</style>
        <%/if%>


    </head>
    <body id="anchortop">
     <div class="grid-container">
        <header class="header">
        <div class="header-section d-flex d-md-none">
            <div class="menu-icon">
              <i class="fas fa-bars header__menu"></i>
            </div>
          </div>
          <div class="header-section d-none d-md-flex d-lg-flex">
            <div class="input-group">
              <input data-php="index" data-hideaj="1" data-cmd="main_search_page" data-target="websearchresult" type="text" class="live_search form-control" placeholder="{LA_SEARCHFOR}" autocomplete="off" aria-label="Suchbegriff..." aria-describedby="button-addon2">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="button-addon2">Suchen</button>
              </div>
            </div>
            <div id="websearchresult"></div>
            <div id="menu_reload_cont">
                <% include file="adminmenu.tpl" %>
            </div>
          </div>

          <div class="header-section d-flex">
            <ul class="topmenu d-none d-lg-flex">
              <li class="top-has-sub">
                <a href="#"><i class="fas fa-rocket"></i></a>
                <ul class="top-sub">
                    <li><a href="//developers.google.com/speed/pagespeed/insights/?url=<%$project_domain|urlencode%>" target="_blank">Google Page Speed Test</a></li>
                    <li><a href="https://www.google.com/webmasters/tools/mobile-friendly/?url=<%$project_domain|urlencode%>" target="_blank">Mobile-Friendly Test</a></li>
                    <li><a href="https://www.google.com/search?q=site:<%$SERVERVARS.HTTP_HOST|replace:"www.":""%>&ie=utf-8&oe=utf-8&client=firefox-b-e" target="_blank">Google Index</a></li>
                    <li><a href="https://search.google.com/structured-data/testing-tool#url=<%$project_domain|urlencode%>" target="_blank">Google Structured Data Test</a></li>
                    <li><a href="//moz.com/researchtools/ose/links?site=<%$project_domain|urlencode%>" target="_blank">Check Inlinks (OSE)</a></li>
            		<li><a href="http://www.zippy.co.uk/keyworddensity/index.php?url=<%$project_domain|urlencode%>&#038;keyword=" target="_blank">Check Keyword Density</a></li>
            		<li><a href="//quixapp.com/headers/?r=<%$project_domain|urlencode%>" target="_blank">Check Headers</a></li>            		
            		<li><a href="//developers.facebook.com/tools/debug/og/object?q=<%$project_domain|urlencode%>" target="_blank">Facebook Debugger</a></li>
            		<li><a href="https://developers.pinterest.com/tools/url-debugger/?link=<%$project_domain|urlencode%>" target="_blank">Pinterest Rich Pins Validator</a></li>
            		<li><a href="//validator.w3.org/check?uri=<%$project_domain|urlencode%>" target="_blank">HTML Validator</a></li>
            		<li><a href="//jigsaw.w3.org/css-validator/validator?uri=<%$project_domain|urlencode%>" target="_blank">CSS Validator</a></li>            		
            		<li><a href="http://wave.webaim.org/report#/<%$project_domain|urlencode%>" target="_blank">WAVE - Barrierefrei?</a></li>                    
                </ul>
              </li>
              <li><a href="../" target="_homepage" title="Frontend"><i class="fas fa-eye"></i></a></li>
            </ul>
    
            <div class="btn-group interactions" role="group" aria-label="Button group with nested dropdown">
              <div class="btn-group" role="group">
                <button id="btnGroupDrop3" type="button" class="btn btn-img dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <img src="<%$GBLEMP.thumb%>" class="round" alt="<%$GBLEMP.mitarbeiter_name|sthsc%>">
                  <span><%$GBLEMP.mi_firstname%></span>
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop3">
                  <a href="./run.php?epage=employee.inc&id=<%$GBLEMP.id%>&aktion=edit&cmd=edit" title=""><i class="fa fa-user pull-right"><!----></i>Mein Profil</a>
                  <a href="./logout.html" title="{LBL_LOGOUT}"><i class="fa fa-sign-out pull-right"><!----></i>{LBL_LOGOUT}</a>
                        <% if (is_array($admin_langs) && count($admin_langs)>1 && 1==2) %>
                            <div>
                                <div class="text-right flag-icons">
                                    <% foreach from=$admin_langs item=lang %>
                                        <a class="pull-right" title="<% $lang.post_lang %>" href="<%$lang.link%>"><img alt="<% $lang.post_lang %>" src="../images/<%$lang.bild%>" class="flag-icon<% if ($lang.id==$alang_id) %> active-lang<%/if%>"></a>
                                    <%/foreach%>
                                </div>
                            </div>
                        <%/if%>
                </div>
              </div>
            </div>
          </div>
        </header>

        <aside class="sidenav menu_section">
            <div class="sidenav__close-icon">
                <i class="fas fa-times sidenav__brand-close"></i>
              </div>
              <a href="<%$PATH_CMS%>admin/welcome.html" title="Keimeno" class="logo">
                <img src="./assets/images/logo.svg">
              </a>
            <!-- sidebar menu -->
                <div id="sidebar-menu">
                  <div class="menu_section">
                    <%include file="sidebar.tpl"%>
                  </div>
                </div><!-- sidebar -->
        </aside>


        <main class="main">
     
     
            <!-- page content -->
            <div class="right_col-" role="main" id="admincontent">
               <%* <% if (count($other_employees_wokring)>1)%>{LBL_WHOISWORKING} <% foreach from=$other_employees_wokring item=row %><%$row.mname%><%/foreach%><%/if%> *%>

                <div id="feedbackmsg">
                     <% if (is_array($GBLPAGE.err) && count($GBLPAGE.err)>0 || count($err_msgs)>0) %>
                       <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                  <% foreach from=$GBLPAGE.err item=err %>
                                    <%$err%><br>
                                  <%/foreach%>
                                  <% foreach from=$err_msgs item=err %>
                                    <%$err%><br>
                                  <%/foreach%>
                              </div>
                            </div>
                        </div>
                      <% /if %>
                      <% if (is_array($ok_msgs) && count($ok_msgs)>0) %>
                      <div class="row">
                            <div class="col-md-12">
                                  <div class="alert alert-success">
                                      <% foreach from=$ok_msgs item=msg %><%$msg%><br><%/foreach%>
                                </div>
                            </div>
                        </div>
                     <% /if %>
                        <% if ($HEADER_PAGE.msg!="")%><div class="alert alert-success"><%$HEADER_PAGE.msg%></div><%/if%>
                        <% if ($HEADER_PAGE.msge!="")%><div class="alert alert-danger"><%$HEADER_PAGE.msge%></div><%/if%>
                </div><!-- feedbacks -->

                <%include file="framew.topbar.tpl"%>

                <noscript><div class="alert alert-danger">{LA_JAVASCRIPTISTINIHREMB}</div></noscript>
