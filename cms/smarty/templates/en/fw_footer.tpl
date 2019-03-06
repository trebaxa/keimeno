<%* include file="member_loginbox.tpl" *%>

<!-- FOOTER -->
<footer id="footer_bank">
  <div class="container">
    <a href="#start" data-hash="#start" id="scrollTop" class="page-scroll btn-round"><i class="fa fa-chevron-up"><span class="sr-only">Nach Oben</span></i></a>
    <div class="row mt-lg">
        <div class="col-md-4">
          <h3>Impressum</h3>
          <i class="fa fa-map-marker pull-left marker"><!----></i>
          <address itemscope itemtype="https://schema.org/LocalBusiness">
            <meta itemprop="openingHours" content="Mo,Tu,We,Th,Fr 09:00-18:00">
            <meta itemprop="priceRange" content="10€ - 900000€">
            <meta itemprop="email" content="<% $gbl_config.adr_service_email %>">
            <meta itemprop="legalName" content="<% $gbl_config.adr_firma %>">
            <meta itemprop="image" content="<%$PATH_CMS%>file_data/assets/img/logo.png">
            <a itemprop="url" class="hidden" href="/" title="Startseite"><img itemprop="logo" src="<%$PATH_CMS%>file_data/assets/img/logo.png" alt="Logo"></a>
              <span itemprop="name"><% $gbl_config.adr_general_firmname %></span><br>
              <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                  <span itemprop="streetAddress"><% $gbl_config.adr_street %></span><br>
                  <span itemprop="postalCode"><% $gbl_config.adr_plz %></span> <span itemprop="addressLocality"><% $gbl_config.adr_town %></span>
              </div>    
          </address>
              <ul >
                <li>
                    <i class="fa fa-phone-square"><!----></i>&nbsp;<span itemprop="telephone" content="<% $gbl_config.adr_telefon %>" title="Telefon"><% $gbl_config.adr_telefon %></span>
                </li>
                <li>
                    <i class="fa fa-print"><!----></i>&nbsp;<% $gbl_config.adr_fax %>
                </li>
                <li>
                    <i class="fa fa-envelope"><!----></i>&nbsp;<%mailto address=$gbl_config.adr_service_email encode="hex"%>
                </li>
            </ul><!-- /.feet-contacts -->
        </div>
        <div class="col-md-4 text-center">
            <h3>Links</h3>
            <ul>
              <li><a title="Impressum" href="<% $PATH_CMS %>impressum.html">Impressum</a></li>
              <li><% $cmsinfo %></li>
          </ul>  
        </div>
        <div class="col-md-4 text-right">
          <h3>Social</h3>
            <a href="#"><i class="fa fa-facebook fa-3x"></i></a>
            <a href="#"><i class="fa fa-twitter fa-3x"></i></a>
            
        </div>
    </div>
  </div>  
</footer>
<% include file="fe_cookie-datenschutz.tpl" %>
    
<script src="<% $PATH_CMS %>file_data/template/js/jform/jquery.form.js"></script>
<script src="<% $PATH_CMS %>file_data/template/js/masonry/masonry.pkgd.min.js"></script>
<script src="<% $PATH_CMS %>file_data/template/js/masonry/imagesloaded.pkgd.min.js"></script>
<script src="<% $PATH_CMS %>file_data/template/js/simplelightbox/simpleLightbox.min.js"></script>
<script src="<% $PATH_CMS %>file_data/template/js/theme.js"></script>
<script src="<% $PATH_CMS %>cjs/keimeno.min.js"></script>
<!--[if lte IE 8]><script language="javascript" src="<% $PATH_CMS %>file_data/template/js/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" src="<% $PATH_CMS %>file_data/template/js/flot/jquery.flot.js"></script>
<script language="javascript" src="<% $PATH_CMS %>file_data/template/js/flot/jquery.flot.time.js"></script>
<script  src="https://maps.googleapis.com/maps/api/js?key=" ></script>

</body>
</html>