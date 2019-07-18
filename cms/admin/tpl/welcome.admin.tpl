<div id="dashboard">

<div class="main-cards">
    <% if ($PERM.core_acc_statistic_dash==true) %>  
        <%include file="cb.panel.header.tpl" title="Besucher" class="chart"%>
                    <div class="filter">
                      <div id="reportrange" class="d-flex justify-content-end zeitraum" >
                        <div class="dropdown">
                                  <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                    Zeitraum
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
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
                <div class="flotchart-container" id="visitor-counter-cont">
                    <div id="visitor-counter" class="flot-placeholder"></div>
                </div><!-- /.flotchart-container #visitor-counter-cont -->
        <%include file="cb.panel.footer.tpl"%>
    <%/if%>

    <%include file="cb.panel.header.tpl" title="{LBL_SEARCHENGINES}" class="searchbots"%>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-lg" id="js-spiderpie"></div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                            <table class="table table-striped">                           
                                    <tbody>
                                       <% foreach from=$cmsinfo.spiders item=row %>
                                        <tr>
                                            <td><% $row.searchengine %></td>
                                            <td class="text-right"><% if ($row.todaytrue==true) %><b><% $row.lasthit %></b><%else%><% $row.lasthit %><%/if%></td>
                                        </tr>
                                        <% /foreach %>
                                    </tbody>
                            </table>
                    </div>
                  </div>  
            </div>
    <%include file="cb.panel.footer.tpl"%>    

    
    <%include file="cb.panel.header.tpl" title="Aktive Apps"%>
            <div class="table-responsive">
                    <table class="table table-striped apps-installed">                           
                            <tbody>
                               <% foreach from=$cmsinfo.active_modules item=row %>
                                <tr>
                                    <td><% $row.mod_name %></td>
                                    <td class="text-right"><i class="far fa-check-circle"></i></td>
                                </tr>
                                <% /foreach %>
                            </tbody>
                    </table>
            </div>
    <%include file="cb.panel.footer.tpl"%>
    
    <%include file="cb.panel.header.tpl" title="System"%>    
            <div class="table-responsive">
                    <table class="table table-striped apps-installed">                            
                        <tbody>
                            <% if ($gbl_config.debug_mode==1) %>
                                <tr><td>Debug Mode:</td><td class="text-right"><i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i><span class="badge  badge-danger">eingeschaltet</span></td></tr>
                             <%/if%>
                             <tr><td>{LBL_PAGECOUNT}:</td><td class="text-right"><%$cmsinfo.page_count_pers%></td></tr>
                             <!-- <tr><td>{LBL_INLAYCOUNT}:</td><td class="text-right"><%$cmsinfo.inlay_count%></td></tr> -->
                             <tr><td>{LBL_CUSTOMERCOUNT}:</td><td class="text-right"><%$cmsinfo.cust_count%></td></tr>
                             <%*<tr><td>{LBL_WEBSPACE} belegt:</td><td class="text-right"><%$cmsinfo.webspace%></td></tr>*%>
                             <tr><td>{LBL_DBSIZE}:</td><td class="text-right"><%$cmsinfo.dbspace%></td></tr>                     
                             <tr><td>{LBL_NOWONLINE}:</td><td class="text-right">{LBL_NOWONLINE} <%$cmsinfo.nowonline%></td></tr>        
                             <tr><td>CMS Pfad:</td><td class="text-right"><%$cmsinfo.path_cms%></td></tr>
                             <tr><td>Visitor Count seit <%$cmsinfo.visitor_since%>:</td><td class="text-right"><%$cmsinfo.visitorcount%></td></tr>
                             <tr><td>Visitor Count/pro Tag:</td><td class="text-right"><%$cmsinfo.visitorcount_perday%></td></tr>
                             <tr><td>Max. file Upload-Size:</td><td class="text-right"><%$ADMIN.max_file_upload_size%></td></tr>
                             <tr><td>PHP Version:</td><td class="text-right"><%$ADMIN.phpversion%></td></tr>                             
                        </tbody>
                     </table>
            </div>
    <%include file="cb.panel.footer.tpl"%>
    
</div>


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

