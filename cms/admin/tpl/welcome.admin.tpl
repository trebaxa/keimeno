<div class="container-fluid" id="dashboard">

   <%* <div class="page-header"><h1><i class="fa fa-home"><!----></i>Dashboard</h1></div>*%>    
    <% if ($PERM.core_acc_statistic_dash==true) %>    
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">                                                   
                    <h2>Besucher</h2>                            
                    <div class="filter">
                      <div id="reportrange" class="pull-right" >
                        <div class="dropdown">
                                  <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                    Zeitraum
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_visitor_chart(31);">31 Tage</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_visitor_chart(90);">3 Monate</a></li>                        
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_visitor_chart(180);">6 Monate</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_visitor_chart(365);">1 Jahr</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_visitor_chart(730);">2 Jahre</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_visitor_chart(1095);">3 Jahre</a></li>
                                  </ul>
                                </div><!-- dropdown -->
                              </div>
                           </div><!-- filter -->
                           <div class="clearfix"></div>
                    </div><!-- /.panel-heading -->
                    <div class="x_content">                    
                        <div class="flotchart-container" id="visitor-counter-cont">
                            <div id="visitor-counter" class="flot-placeholder"></div>
                        </div><!-- /.flotchart-container #visitor-counter-cont -->
                    </div><!-- /.panel-body -->
                </div><!-- /.panel .panel-default -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    <%/if%>
    <div class="row">
        
        <div class="col-md-4 col-sm-12 col-xs-12">
            <%include file="cb.panel.header.tpl" title="System"%>
                <table class="table table-striped table-hover">
                     <% if ($gbl_config.debug_mode==1) %>
                        <tr><td>Debug Mode:</td><td class="text-right"><i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i><span class="badge  badge-danger">eingeschaltet</span></td></tr>
                     <%/if%>
                     <tr><td>{LBL_PAGECOUNT}:</td><td class="text-right"><%$cmsinfo.page_count_pers%></td></tr>
                     <tr><td>{LBL_INLAYCOUNT}:</td><td class="text-right"><%$cmsinfo.inlay_count%></td></tr>
                     <tr><td>{LBL_CUSTOMERCOUNT}:</td><td class="text-right"><%$cmsinfo.cust_count%></td></tr>
                     <%*<tr><td>{LBL_WEBSPACE} belegt:</td><td class="text-right"><%$cmsinfo.webspace%></td></tr>*%>
                     <tr><td>{LBL_DBSIZE}:</td><td class="text-right"><%$cmsinfo.dbspace%></td></tr>                     
                     <tr><td>{LBL_NOWONLINE}:</td><td class="text-right">{LBL_NOWONLINE} <%$cmsinfo.nowonline%></td></tr>        
                     <tr><td>CMS Pfad:</td><td class="text-right"><%$cmsinfo.path_cms%></td></tr>
                     <tr><td>Visitor Count seit <%$cmsinfo.visitor_since%>:</td><td class="text-right"><%$cmsinfo.visitorcount%></td></tr>
                     <tr><td>Visitor Count/pro Tag:</td><td class="text-right"><%$cmsinfo.visitorcount_perday%></td></tr>
                </table>
            <%include file="cb.panel.footer.tpl"%>
        </div><!-- /.col-md-4 -->
    
        <div class="col-md-4 col-sm-12 col-xs-12">
            <%include file="cb.panel.header.tpl" title="Ihre aktiven Apps"%>                
                <table class="table table-striped table-hover">
                    <% foreach from=$cmsinfo.active_modules item=row %>
                    <tr>
                        <td><% $row.mod_name %></td>
                        <td class="text-right"><i class="fa fa-check-circle-o fa-lg fa-green"><!----></i></td>
                    </tr>
                    <% /foreach %>
                    
                </table>
                
            <%include file="cb.panel.footer.tpl"%>
        </div><!-- .col-md-4 -->
    
        <div class="col-md-4 col-sm-12 col-xs-12">            
            <%include file="cb.panel.header.tpl" title="{LBL_SEARCHENGINES}"%>            
                <table class="table table-striped table-hover">                    
                    <% foreach from=$cmsinfo.spiders item=row %>
                        <tr>
                            <td><% $row.searchengine %></td>
                            <td><% if ($row.todaytrue==true) %><b><% $row.lasthit %></b><%else%><% $row.lasthit %><%/if%></td>
                        </tr>
                    <% /foreach %>
                    
                </table>
                
                    <div id="js-spiderpie"></div>
                
                <div class="panel-footer">
                    <p>Suchmaschinen-Verteilung. Dieses Diagramm zeigt Ihnen die Häufigkeit der Suchmaschinen Zugriffe.</p>
                </div><!-- /.panel-footer -->
            <%include file="cb.panel.footer.tpl"%>
        </div><!-- .col-md-4 -->
    
    </div><!-- .row -->

</div> <!-- .container-fluid #dashboard -->

<script type="text/javascript">
function load_visitor_chart(days) {
    load_flot_chart("<%$PATH_CMS%>admin/run.php?epage=welcome.inc&cmd=load_visitor_chart&days="+days,"visitor-counter","100%","230px",1,"");
}    
$( document ).ready(function() {
    load_visitor_chart(31);
    load_flot_pie('<%$eurl%>cmd=load_se_chart', 'js-spiderpie', '100%', '250px', false, true,0.5);
});
</script>

