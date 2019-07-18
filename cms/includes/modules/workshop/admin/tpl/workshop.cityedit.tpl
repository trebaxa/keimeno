<h3>Stadt <%$WORKSHOP.city.c_city%>&nbsp;<small>bearbeiten</small></h3>

<form action="<%$PHPSELF%>" class="jsonform" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="cmd" value="save_city"/>    
    <input type="hidden" name="epage" value="<%$epage%>"/>
    <input type="hidden" name="id" value="<%$GET.id%>"/>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Stadt:</label>
                <input type="text" value="<%$WORKSHOP.city.c_city|sthsc%>" name="FORM[c_city]" class="form-control" required="" autocomplete="off" />                
            </div>
            
            <div class="form-group">
                <label>Beschreibung:</label>
                <textarea rows="10" name="FORM[c_text]" class="form-control"><%$WORKSHOP.city.c_text|sthsc%></textarea>                
            </div>
              
            <div class="form-group">
                <label for="datei">Stadt Themen Bild</label>
                <div class="input-group">
                    <input type="text" name="" value="" class="form-control" readonly="" placeholder="Keine Datei ausgewählt"/>
                    <input id="datei" type="file" name="datei" value="" class="xform-control autosubmit" onchange="this.previousElementSibling.value = this.value">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="button">Durchsuchen...</button>          
                    </span>
                </div><!-- /input-group -->
            </div><!-- /.form-group -->   
            <% if ($WORKSHOP.city.c_image!="") %>
                <img src="../file_data/workshop/<%$WORKSHOP.city.c_image%>" id="js-city-image" class="img-fluid" />
                <br>
                <button class="btn btn-secondary" type="button" onclick="ws_delete_city_image(<%$GET.id%>);"><i class="fa fa-trash"></i></button>
            <%/if%>
  <%$subbtn%> 
  </div>
  </div>
 </form>                          
 
 <script>
 function ws_delete_city_image(id) {
    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=ws_delete_city_image&id='+id);
    $('#js-city-image').remove();
 }
 </script>