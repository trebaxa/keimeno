<h3>Workshop <%$WORKSHOP.ws.ws_title%>&nbsp;<small>bearbeiten</small></h3>

<form action="<%$PHPSELF%>" class="jsonform" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="cmd" value="save_workshop"/>    
    <input type="hidden" name="epage" value="<%$epage%>"/>
    <input type="hidden" name="id" value="<%$GET.id%>"/>
    <div class="row">
        <div class="col-md-6">
                    <div class="form-group">
                        <label>Workshop Name:</label>
                        <input type="text" value="<%$WORKSHOP.ws.ws_title|sthsc%>" name="FORM[ws_title]" class="form-control" required="" autocomplete="off" />
                    </div>
    
                    <div class="form-group">
                        <label>Findet statt am:</label>
                        <input type="text" value="<%$WORKSHOP.ws.date_ger|sthsc%>" name="FORM[ws_date]" class="form-control" required="" autocomplete="off" />
                        <p class="help-block">Format: d.m.Y</p>
                    </div>                
                    
                    <div class="form-group">
                        <label>Uhrzeit von:</label>
                        <input type="text" value="<%$WORKSHOP.ws.ws_time|sthsc%>" name="FORM[ws_time]" class="form-control" required="" autocomplete="off" />
                        <p class="help-block">Format: H:i</p>
                    </div>

                    <div class="form-group">
                        <label>Uhrzeit bis:</label>
                        <input type="text" value="<%$WORKSHOP.ws.ws_time_to|sthsc%>" name="FORM[ws_time_to]" class="form-control" required="" autocomplete="off" />
                        <p class="help-block">Format: H:i</p>
                    </div>       
                    
                    <div class="form-group">
                        <label>Workshop Preis (brutto):</label>
                        <input type="text" value="<%$WORKSHOP.ws.ws_price_br|sthsc%>" name="FORM[ws_price_br]" class="form-control" required="" autocomplete="off" />
                        <p class="help-block">Format: 0,00</p>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" value="<%$WORKSHOP.ws.ws_location|sthsc%>" name="FORM[ws_location]" required="" class="form-control" autocomplete="off" />
                    </div>           
                    
                    <div class="form-group">
                        <label>Strasse mit Hausnr:</label>
                        <input type="text" value="<%$WORKSHOP.ws.ws_street|sthsc%>" name="FORM[ws_street]" class="form-control" autocomplete="off" />                        
                    </div>    
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PLZ:</label>
                                <input type="text" value="<%$WORKSHOP.ws.ws_plz|sthsc%>" name="FORM[ws_plz]" class="form-control" autocomplete="off" />                                
                            </div>            
                        </div>
                        <div class="col-md-6">                                
                    
                            <div class="form-group">
                                <label>Stadt:</label>
                                <select name="FORM[ws_city]" class="form-control">
                                    <% foreach from=$WORKSHOP.cities item=row %>
                                        <option <% if ($WORKSHOP.ws.ws_city==$row.id)%>selected<%/if%> value="<%$row.id%>"><%$row.c_city%></option>
                                    <%/foreach%>
                                </select>
                            </div>
                           </div>   
                    </div>
                    <div class="form-group">
                        <label>Zielgruppe:</label>
                        <input type="text" value="<%$WORKSHOP.ws.ws_zielgruppe|sthsc%>" name="FORM[ws_zielgruppe]" class="form-control" required="" autocomplete="off" />
                    </div>    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Teilnemher Anzahl von:</label>
                                <input type="text" value="<%$WORKSHOP.ws.ws_teilnvon|sthsc%>" name="FORM[ws_teilnvon]" class="form-control" autocomplete="off" />                                
                            </div>            
                        </div>
                        <div class="col-md-6">                                
                    
                            <div class="form-group">
                                <label>Teilnemher Anzahl bis:</label>
                                <input type="text" value="<%$WORKSHOP.ws.ws_teilnbis|sthsc%>" name="FORM[ws_teilnbis]" class="form-control" autocomplete="off" /> 
                            </div>
                           </div>   
                    </div>
                    
                    <div class="form-group">
                        <label for="datei">Theme Bild</label>
                        <div class="input-group">
                            <input type="text" name="" value="" class="form-control" readonly="" placeholder="Keine Datei ausgewählt"/>
                            <input id="datei" type="file" name="datei" value="" class="xform-control autosubmit" onchange="this.previousElementSibling.value = this.value">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">Durchsuchen...</button>          
                            </span>
                        </div><!-- /input-group -->
                    </div><!-- /.form-group -->   
                    <div id="js-ws-theme-img"></div>                 
                    
             <%$subbtn%>    
        </div>           
        <div class="col-md-6">
                    <div class="form-group">
                        <label>Kurz-Beschreibung:</label>
                        <textarea name="FORM[ws_shortdesc]" rows="6" class="form-control"><%$WORKSHOP.ws.ws_shortdesc|sthsc%></textarea>
                    </div>     
                    <div class="form-group">
                        <label>Zielsetzung:</label>
                        <textarea name="FORM[ws_zielsetzung]" rows="6" class="form-control"><%$WORKSHOP.ws.ws_zielsetzung|sthsc%></textarea>
                    </div> 
                    <div class="form-group">
                        <label>Das müssen Sie mitbringen:</label>
                        <textarea name="FORM[ws_mitbringen]" rows="6" class="form-control"><%$WORKSHOP.ws.ws_mitbringen|sthsc%></textarea>
                    </div>         
                        
            <div class="form-group">
                <label>Sonstiges:</label>
                <textarea name="FORM[ws_sonstiges]" rows="6" class="form-control"><%$WORKSHOP.ws.ws_sonstiges|sthsc%></textarea>
            </div>             
            <div class="form-group">
                <label>Durchführung:</label>
                <textarea name="FORM[ws_durchfuehrung]" rows="6" class="form-control"><%$WORKSHOP.ws.ws_durchfuehrung|sthsc%></textarea>
            </div> 

            <div class="form-group">
                <label>Im Preis enthalten sind:</label>
                <textarea name="FORM[ws_enthalten]" rows="6" class="form-control"><%$WORKSHOP.ws.ws_enthalten|sthsc%></textarea>
            </div>    
            <div class="form-group">
                <label>Bildrechte:</label>
                <textarea name="FORM[ws_bildrechte]" rows="6" class="form-control"><%$WORKSHOP.ws.ws_bildrechte|sthsc%></textarea>
            </div>  
            <%$subbtn%>                                
        </div>
    </div>    
</form>
<hr>
<h3>Bilder hinzufügen</h3>
<div class="row">
    <div class="col-md-6">
        <form action="<%$PHPSELF%>" enctype="multipart/form-data" class="jsonform" method="POST">
            <input type="hidden" name="cmd" value="add_image"/>    
            <input type="hidden" name="epage" value="<%$epage%>"/>
            <input type="hidden" name="id" value="<%$GET.id%>"/>
               <div class="form-group">
                    <label for="datei">Datei</label>
                    <div class="input-group">
                        <input type="text" name="" value="" class="form-control" readonly="" placeholder="Keine Datei ausgewählt">
                        <input id="datei" type="file" name="datei" value="" class="xform-control autosubmit" onchange="this.previousSibling.value = this.value">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">Durchsuchen...</button>          
                        </span>
                    </div><!-- /input-group -->
                </div><!-- /.form-group -->
            <%$subbtn%>                                
        </form>
        <div id="js-ws-bilder"><%include file="workshop.images.tpl"%></div>        
    </div>        
    <div class="col-md-6">
        <h3>Kunden, die gebucht haben</h3>
        <% if (count($WORKSHOP.ws.bookings)>0) %>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>gebucht am</th>            
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <% foreach from=$WORKSHOP.ws.bookings item=row %>
                <tr>
                    <td><%$row.vorname%> <%$row.nachname%></td>
                    <td><%$row.booking_date_ger%></td>
                    <td class="text-right"><div class="btn-group"><% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>            
                </tr>
            <%/foreach%>
            </tbody>
        </table>
        <%else%>
            <p class="alert alert-info">Es liegen noch keine Buchungen vor.</p>
        <%/if%>
    </div>
</div>


<script>
function reloadtheme() {
    simple_load('js-ws-theme-img','<%$PHPSELF%>?epage=<%$epage%>&cmd=reloadtheme&id=<%$GET.id%>');
} 
reloadtheme();
</script>

