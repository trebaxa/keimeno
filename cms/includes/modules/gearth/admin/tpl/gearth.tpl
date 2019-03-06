<style>
.gkmlframe {
    width:100%;
    height:500px;
}
</style>
<div class="page-header"><h1><i class="fa fa-file-code-o"></i>Google Earth Implementation</h1></div>

<form class="jsonform form-inline" method="POST" action="<%$PHPSELF%>">
 
        
    <div class="panel-heading">
        <h3 class="panel-title">Geo Koordinaten</h3><!-- /.panel-title -->
        <input type="hidden" name="cmd" value="gen_kml"/>
        <input type="hidden" name="epage" value="<%$epage%>"/>
    </div><!-- /.panel-heading -->
    
    
    <div class="form-group"> 
        <label for="changelanggbltpl"><a class="btn btn-default" target="_kml" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=open_and_view">KML Datei anzeigen</a></label>
       
    </div><!-- /.form-group -->
    
    <div class="form-group"> 
        <label for="kmltitle">Address:</label>        
        <input type="text" class="form-control" id="kmltitle" name="FORM[title]" value="<%$KML_OBJ.xml_array.PLACEMARK.NAME|hsc%>" size="21"/>
    </div><!-- /.form-group -->
    
    
    <div class="form-group"> 
        <label for="form-control">Beschreibung:</label>
        <input type="text" class="form-control" name="FORM[description]" value="<%$KML_OBJ.xml_array.PLACEMARK.DESCRIPTION|hsc%>" size="30"/>
    </div><!-- /.form-group -->
    
        
    <div class="form-group"> 
        <label for="form-control">Latitude:</label>
       <input type="text" class="form-control" id="kmllat" name="FORM[lat]" value="<%$KML_OBJ.coords.lat|hsc%>" size="21"/>
    </div><!-- /.form-group -->
    
    
    <div class="form-group"> 
        <label for="form-control">Longitude:</label>
       <input type="text" class="form-control" id="kmllong" name="FORM[lon]" value="<%$KML_OBJ.coords.lon|hsc%>" size="21"/>
    </div><!-- /.form-group -->
    
    <div class="form-group"> 
        <label for="changelanggbltpl"><%$subbtn%></label>
    </div><!-- /.form-group -->
</form>
    
<!-- ende Olsi Bearbeitung -->
    


<div class="bg-info text-info">Registrieren Sie die Geo Sitemap XML Datei in "Google Webmaster Tools" unter "Sitemaps"<br>
<a target="_kml" href="<%$KML_OBJ.link%>"><%$KML_OBJ.link%></a>
<br><a target="_kml" href="<%$KML_OBJ.sitemaplink%>"><%$KML_OBJ.sitemaplink%></a>
<br><br>
Ihre Geo Position k&ouml;nnen Sie hier erfahren: <a target="_geo" href="http://www.mygeoposition.com/">http://www.mygeoposition.com/</a>
 </div>

<%$KML_OBJ.gm_frame%>
<script>
function reloadkml() {
  $('.gkmlframe').attr('src', "https://www.trebaxa.com/gmgen.php?height:500px&width=100%&zoom=9&amp;point="+$('#kmllong').val()+","+$('#kmllat').val()+",0&address="+$('#kmltitle').val()+"&a="+Math.random());  
}
</script>