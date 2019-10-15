<% if ($REQUEST.axcall!=1)%>


                 <%*   <div class="footer">
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
                    *%>



                </div><!-- right_col -->
             </main><!-- main_container-->
            <footer class="footer">
                <div class="footer__copyright">
                  <small>
                            &copy; <% 'Y'|date %> <a href="https://www.trebaxa.com" target="_blank">Trebaxa GmbH & Co. KG</a>
                            <br />
                            <a class="ajax-link" href="run.php?epage=about.inc" title="Abount Keimeno" target="_homepage"><i class="fa fa-info-circle fa-sm"><!----></i> About Keimeno Software</a>
                  </small>
                </div>
                <div class="footer__signature text-right">
                  <small>Version Build : <%$ADMIN.vars.CMSVERSION%>|<%$ADMIN.vars.DB_DATABASE%>|<%$ADMIN.vars.PREFIX%>|<%$ADMIN.server.bitversion%> Server|Max.File Upload-Size:<%$ADMIN.max_file_upload_size%>|PHP:<%$ADMIN.phpversion%>
                    <br>
                        This work is licensed under <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0" target="_blank">GNU GENERAL PUBLIC LICENSE Version 2 or higher</a>.
                  </small>
                </div>
            </footer>
        </div><!-- container body -->

        <script>
            set_ajaxapprove_icons();
            set_ajaxdelete_icons('{LBL_CONFIRM}','<%$epage%>');
            function sleep (time) {
              return new Promise((resolve) => setTimeout(resolve, time));
            }
        </script>
    <% if ($DEBUG==1) %>
        <!-- FastClick -->
        <script src="./assets/vendors/fastclick/lib/fastclick.js"></script>
        <script src="./assets/vendors/dropzone/dropzone.js"></script>

        <!-- Flot -->
        <script src="./assets/vendors/Flot/jquery.flot.js"></script>
        <script src="./assets/vendors/Flot/jquery.flot.pie.js"></script>
        <script src="./assets/vendors/Flot/jquery.flot.time.js"></script>
        <script src="./assets/vendors/Flot/jquery.flot.stack.js"></script>
        <script src="./assets/vendors/Flot/jquery.flot.resize.js"></script>
        <!-- Flot plugins -->
        <script src="./assets/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
        <script src="./assets/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
        <script src="./assets/vendors/flot.curvedlines/curvedLines.js"></script>

        <!-- Datatables -->
        <script src="./assets/vendors/DataTables/datatables.min.js"></script>
        <script src="./assets/vendors/moment/moment-with-locales.js"></script>

        <!-- Custom Theme Scripts -->
        <script src="./assets/vendors/ace123/emmet/emmet.js"></script>
        <script src="./assets/vendors/ace123/src-min-noconflict/ace.js"></script>
        <script src="./assets/vendors/ace123/src-min-noconflict/ext-emmet.js"></script>
        <script src="./assets/vendors/jcrop/dist/jcrop.js"></script>
        <link rel="stylesheet" href="./assets/vendors/jcrop/dist/jcrop.css" type="text/css" />
    <%else%>
        <script src="./assets/vendors/ace123/emmet/emmet.js"></script>
        <script src="./assets/vendors/ace123/src-min-noconflict/ace.js"></script>
        <script src="./assets/vendors/ace123/src-min-noconflict/ext-emmet.js"></script>
                
    <%/if%>

        <script src="./assets/vendors/jquery-ui-1.12.1/jquery-ui.js"></script>        
        <script src="./assets/js/footer.min.js"></script>

        </body>
    </html>
<%/if%>
