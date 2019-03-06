<%* <link rel="stylesheet" href="<% $PATH_CMS %>includes/modules/gmap/css/style.css"> *%>
<section>
  <h2>Google Maps</h2>
  <div id="gmap-<%$gmap.cont_matrix_id%>" class="gmap js-load-gmap" data-name="<%$gmap.name%>" data-str="<%$gmap.str%>" data-plz=<%$gmap.plz%>"" data-city="<%$gmap.city%>" data-width="<%$gmap.width%>" data-height="<%$gmap.height%>"></div>
</section>
<%*<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIjarJhx2tGFVQ_HsU2PAmCVTqFfRemug&callback=init_gmap_app" ></script>*%>