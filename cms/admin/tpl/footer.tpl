
<% if ($REQUEST.axcall!=1)%>

                        
                    <div class="footer">                      
                        <div class="row">
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="./images/small_icon_11x11.jpg" style="margin-right:3px;border:0px;width:11px">keimeno CMS Administrator 
                                <br>Version Build : <%$ADMIN.vars.CMSVERSION%>|<%$ADMIN.vars.DB_DATABASE%>|<%$ADMIN.vars.PREFIX%>|<%$ADMIN.server.bitversion%> Server|Max.File Upload-Size:<%$ADMIN.max_file_upload_size%>|PHP:<%$ADMIN.phpversion%>
                                <br>&copy; <a href="https://www.trebaxa.com" target="_blank">Trebaxa GmbH & Co. KG</a>
                                <br />
                                <p>This work is licensed under <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0" target="_blank">GNU GENERAL PUBLIC LICENSE Version 2 or higher</a>.</p>
                                <p class="text-center"><a class="ajax-link" href="run.php?epage=about.inc" title="Abount Keimeno" target="_homepage"><i class="fa fa-info-circle fa-sm"><!----></i> About Keimeno Software</a></p>
                            </div>
                            <div class="col-md-4">
                            
                            </div>
                        </div>   
                    </div>
                


                </div><!-- right_col -->
             </div><!-- main_container-->
        </div><!-- container body -->
              
        <script>
            set_ajaxapprove_icons();
            
            <% if ($ADMIN.vars.TOPMENU!="") %>
                $("#ah_content_table").css("margin-top","141px");
            <%/if%>
            set_ajaxdelete_icons('{LBL_CONFIRM}','<%$epage%>');
        </script>
    <% if ($DEBUG==1) %>  
    <!-- FastClick -->
    <script src="./theme/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="./theme/vendors/nprogress/nprogress.js"></script>
    <%*<!-- Dropzone.js -->
    <script src="./theme/vendors/dropzone/dist/min/dropzone.min.js"></script>*%>
    <!-- Chart.js -->
    <script src="./theme/vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="./theme/vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="./theme/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="./theme/vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="./theme/vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="./theme/vendors/Flot/jquery.flot.js"></script>
    <script src="./theme/vendors/Flot/jquery.flot.pie.js"></script>
    <script src="./theme/vendors/Flot/jquery.flot.time.js"></script>
    <script src="./theme/vendors/Flot/jquery.flot.stack.js"></script>
    <script src="./theme/vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="./theme/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="./theme/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="./theme/vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="./theme/vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="./theme/vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="./theme/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <%*<script src="./theme/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>*%>
    <!-- bootstrap-daterangepicker -->
    <script src="./theme/vendors/moment/min/moment.min.js"></script>
    <%*<script src="./theme/vendors/daterangepicker/daterangepicker.js"></script>*%>
    
    <!-- Datatables -->
    <script src="./theme/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="./theme/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="./theme/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="./theme/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="./theme/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="./theme/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="./theme/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="./theme/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="./theme/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="./theme/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="./theme/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="./theme/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    
    <!-- Custom Theme Scripts -->
    <script src="./theme/build/js/custom.min.js"></script>
    <%else%>
    <script src="./js/ace123/emmet/emmet.js"></script>
    <script src="./js/ace123/src-min-noconflict/ace.js"></script>
    <script src="./js/ace123/src-min-noconflict/ext-emmet.js"></script>
    <script src="./js/footer.min.js"></script>
    <%/if%>
    
    <script src="js/jquery-ui-1.12.1/jquery-ui.js"></script>
    <%*<link rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.css">*%>
        </body>
    </html>
<%/if%>
