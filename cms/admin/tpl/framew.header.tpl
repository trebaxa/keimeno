<!DOCTYPE HTML>
<html lang="de">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">         
        <title><% $gbl_config.adr_firma%> <%$HEADER_PAGE.current_year%> CMS - Manager</title>        
       <% if ($DEBUG==1) %> 
        <link rel="shortcut icon" href="../images/favicon.ico" />
         <!-- Bootstrap -->
        <link href="./theme/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">        
        <!-- Font Awesome -->
        <link href="./theme/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <!-- NProgress -->
        <link href="./theme/vendors/nprogress/nprogress.css" rel="stylesheet">
        <!-- iCheck -->
        <link href="./theme/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
        <!-- bootstrap-progressbar -->
        <link href="./theme/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
        <!-- JQVMap -->
        <link href="./theme/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
        <link href="./theme/vendors/animate.css/animate.min.css" rel="stylesheet">
        <!-- Datatables -->
        <link href="./theme/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="./theme/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="./theme/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="./theme/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="./theme/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="./theme/build/css/custom.min.css" rel="stylesheet">
        <!-- keimeno -->                          
        <link rel="stylesheet" href="./js/jstree/dist/themes/default/style.css" />
        <link rel="stylesheet" href="./js/scroller/jquery.mCustomScrollbar.min.css">
        <link rel="stylesheet" href="./js/dropzone/dropzone.css">                
        <link rel="stylesheet" href="./css/layout-theme.css">
         <!-- jQuery -->
        <script src="./theme/vendors/jquery/dist/jquery.min.js"></script>        
        <!-- Bootstrap -->
        <script src="./theme/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="./js/jquery.form.js"></script>
        <script src="./js/scroller/jquery.mCustomScrollbar.js"></script>                
        <script src="./js/jstree/dist/jstree.min.js"></script>
        <script src="./js/dropzone/dropzone.js"></script>        
        <script src="./js/tiny_mce4012/tinymce.min.js"></script>
        <script src="./js/functions.js"></script>
                    
        <%else%>
        <link href="./theme/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">        
        <link href="./theme/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="./css/keimeno.min.css" rel="stylesheet">            
        <link rel="stylesheet" href="./js/jstree/dist/themes/default/style.css" />
        <script src="./js/functions.min.js"></script>            
        <script src="./js/tiny_mce4012/tinymce.min.js"></script>
        <%/if%>
    </head>
    <body id="anchortop" class="nav-md">
     <div class="container body">
          <div class="main_container">
          
          <div class="col-md-3 left_col menu_fixed">
              <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                  <a href="<%$PATH_CMS%>admin/welcome.html" class="site_title"><img style="width:45px" src="./images/logo-cms-keimeno-small.png" alt="Keimeno"> <span>Keimeno</span></a>
                </div>
    
                <div class="clearfix"></div>
                
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                  <div class="menu_section">
                    <%include file="sidebar.tpl"%>                
                  </div>
                </div><!-- sidebar -->      
                
            </div><!-- left_col -->
          </div><!-- col-md3 sidebar -->
          
          <!-- top navigation -->
            <div class="top_nav" id="adminheader">
              <div class="nav_menu">
                <nav>
                  <div class="nav toggle">
                    <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                  </div>               
                
                <div class="navbar-form navbar-left hidden-sm hidden-xs">  
                    <div class="form-group top_search" role="search">
                        <div class="input-group">
                            <input data-php="index" data-hideaj="1" data-cmd="main_search_page" data-target="websearchresult" type="text" class="live_search form-control" placeholder="{LA_SEARCHFOR}" autocomplete="off">
                            <span class="input-group-btn"><span class="btn search-icon"><i class="fa fa-search fa-fw"><!----></i></span></span>
                        </div>
                        <div id="websearchresult"></div>
                    </div>
                </div>               
                  
                <ul class="nav navbar-nav navbar-left hidden-sm hidden-xs" id="menu_reload_cont">                
                    <% include file="adminmenu.tpl" %>
                </ul><!-- .nav .navbar-nav .navbar-right #menu_reload_cont -->
                  
                <ul class="nav navbar-nav navbar-right">
                    <li class="">
                      <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                         <img src="<%$GBLEMP.thumb%>" class="empprofilimg" alt="<%$GBLEMP.mitarbeiter_name|sthsc%>"><%$GBLEMP.mi_firstname%>
                        <span class=" fa fa-angle-down"></span>
                      </a>
                      <ul class="dropdown-menu dropdown-usermenu pull-right">                   
                        <li><a href="./run.php?epage=employee.inc&id=<%$GBLEMP.id%>&aktion=edit&cmd=edit" title=""><i class="fa fa-user pull-right"><!----></i>Mein Profil</a></li>
                        <li><a href="./logout.html" title="{LBL_LOGOUT}"><i class="fa fa-sign-out pull-right"><!----></i>{LBL_LOGOUT}</a></li>
                        <% if (is_array($admin_langs) && count($admin_langs)>1) %>
                            <li>                            
                                <div class="text-right flag-icons">                             
                                    <% foreach from=$admin_langs item=lang %>
                                        <a class="pull-right" title="<% $lang.post_lang %>" href="<%$lang.link%>"><img alt="<% $lang.post_lang %>" src="../images/<%$lang.bild%>" class="flag-icon<% if ($lang.id==$alang_id) %> active-lang<%/if%>"></a>
                                    <%/foreach%>
                                </div>
                            </li>
                        <%/if%>
                      </ul>
                    </li>
                    <li>
                        <a href="../index.html" title="Homepage Frontend" target="_homepage"><i class="fa fa-eye fa-lg"><!----></i></a>
                    </li>
                    <li>
                        <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-rocket"></i></a>
                         <ul class="dropdown-menu dropdown-usermenu pull-right">                            
                            <li id="wp-admin-bar-wpseo-inlinks-ose"><a href="//moz.com/researchtools/ose/links?site=<%$project_domain|urlencode%>" target="_blank">Check Inlinks (OSE)</a></li>
                    		<li><a href="http://www.zippy.co.uk/keyworddensity/index.php?url=<%$project_domain|urlencode%>&#038;keyword=" target="_blank">Check Keyword Density</a></li>
                    		<li><a href="//quixapp.com/headers/?r=<%$project_domain|urlencode%>" target="_blank">Check Headers</a></li>
                    		<li><a href="https://search.google.com/structured-data/testing-tool#url=<%$project_domain|urlencode%>" target="_blank">Google Structured Data Test</a></li>
                    		<li><a href="//developers.facebook.com/tools/debug/og/object?q=<%$project_domain|urlencode%>" target="_blank">Facebook Debugger</a></li>
                    		<li><a href="https://developers.pinterest.com/tools/url-debugger/?link=<%$project_domain|urlencode%>" target="_blank">Pinterest Rich Pins Validator</a></li>
                    		<li><a href="//validator.w3.org/check?uri=<%$project_domain|urlencode%>" target="_blank">HTML Validator</a></li>
                    		<li><a href="//jigsaw.w3.org/css-validator/validator?uri=<%$project_domain|urlencode%>" target="_blank">CSS Validator</a></li>
                    		<li><a href="//developers.google.com/speed/pagespeed/insights/?url=<%$project_domain|urlencode%>" target="_blank">Google Page Speed Test</a></li>
                    		<li><a href="https://developer.microsoft.com/en-us/microsoft-edge/tools/staticscan/?url=<%$project_domain|urlencode%>" target="_blank">Microsoft Edge Site Scan</a></li>
                    		<li><a href="https://www.google.com/webmasters/tools/mobile-friendly/?url=<%$project_domain|urlencode%>" target="_blank">Mobile-Friendly Test</a></li>
                         </ul>
                    </li>       
                  </ul>
                </nav>
              </div>
            </div>
            <!-- /top navigation --> 
          
            <!-- page content -->
            <div class="right_col" role="main" id="admincontent">
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
                                      <% foreach from=$ok_msgs item=msg %>              <%$msg%><br>              <%/foreach%>
                                </div>
                            </div>
                        </div>                                             
                     <% /if %>
                        <% if ($HEADER_PAGE.msg!="")%><div class="alert alert-success"><%$HEADER_PAGE.msg%></div><%/if%>
                        <% if ($HEADER_PAGE.msge!="")%><div class="alert alert-danger"><%$HEADER_PAGE.msge%></div><%/if%>
                </div><!-- feedbacks -->
                
                <%include file="framew.topbar.tpl"%>
    
                <noscript><div class="alert alert-danger">{LA_JAVASCRIPTISTINIHREMB}</div></noscript>            
                
           