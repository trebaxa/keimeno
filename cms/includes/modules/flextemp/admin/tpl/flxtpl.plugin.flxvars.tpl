<%*$FLEXTEMP.resources.columns|echoarr*%>
<% if (count($FLEXTEMP.flextpl.flexvars)>0)%>

<form action="<%$PHPSELF%>" method="POST" class="jsonform" enctype="multipart/form-data">
          <input type="hidden" value="save_flxvar_for_plugin" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          <input type="hidden" value="<%$GET.flxid%>" name="flxid" />
          <input type="hidden" value="<%$GET.content_matrix_id%>" name="content_matrix_id" />
          
    <% foreach from=$FLEXTEMP.flextpl.flexvars item=row %>      
        <div class="form-group">
            <label><%$row.v_descr%></label>
            
            <% if ($row.v_type=='sel') %>
                <div class="input-group">
                    <select class="form-control custom-select" name="FORMFLEXVAR[<%$row.id%>]">
                           <% foreach from=$row.select item=rvol key=rkey %>
                                <option <% if ($rvol==$row.value) %>selected<%/if%> value="<%$rvol%>"><%$rvol%></option>
                           <%/foreach%>              
                    </select>
                    <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>
            
            <% if ($row.v_type=='link') %>
                <div class="input-group">
                <select class="form-control custom-select" name="FORMFLEXVAR[<%$row.id%>]">
                <option value="">- kein Link -</option>
                       <% foreach from=$FLEXTEMP.menu_selectox item=rvol key=rkey %>                            
                            <%assign var="urltpl" value="{URL_TPL_`$rkey`}"%>
                            <option <% if ($urltpl==$row.value) %>selected<%/if%> value="<%$urltpl%>"><%$rvol%></option>
                       <%/foreach%>              
                </select>
                    <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>
            
            <% if ($row.v_type=='resrc') %>
            <div class="row"> 
                <div class="form-group col-md-4">
                    <label>Auswahl</label>
                    <select class="form-control custom-select" name="SETTINGS[<%$row.id%>][resrc][cid][]" multiple="" style="min-height:300px;">
                        <option <% if (0==count($row.v_settings.resrc.cid)) %>selected<%/if%> value="0">- alle -</option>
                       <% foreach from=$row.resrc_table item=rvol key=rkey %>
                            <option <% if ($rvol.CID|inarray:$row.v_settings.resrc.cid) %>selected<%/if%> value="<%$rvol.CID%>"><%$rvol.c_label%></option>
                       <%/foreach%>              
                    </select>
                </div>                
                <div class="col-md-8">
                
                    <div class="form-group col-md-4">
                        <label>Anzahl (wenn "alle" - 0 bedeutet alle)</label>
                        <input required="" type="text" class="js-num-field form-control" name="SETTINGS[<%$row.id%>][resrc][quantity]" value="<%$row.v_settings.resrc.quantity|sthsc|intval%>" />
                    </div>
                    <div class="form-group col-md-4">
                        <label>Sortierung:</label>
                        <select class="form-control custom-select" name="SETTINGS[<%$row.id%>][resrc][sort]">
                             <optgroup label="Standard">
                                <option <% if ($row.v_settings.resrc.sort==0) %>selected<%/if%> value="0">- manuelle Sortierung -</option>
                                <option <% if ($row.v_settings.resrc.sort==-1) %>selected<%/if%> value="-1">- zufällig -</option>
                             </optgroup>   
                             <optgroup label="Felder">
                               <% foreach from=$FLEXTEMP.resources.columns item=rvol key=rkey %>
                                    <option <% if ($row.v_settings.resrc.sort==$rvol.id) %>selected<%/if%> value="<%$rvol.id%>"><%$rvol.v_name%></option>
                               <%/foreach%>
                             </optgroup>                   
                        </select>
                    </div>   
                    <div class="form-group col-md-4">
                        <label>Sort. Richtung:</label>
                        <select class="form-control custom-select" name="SETTINGS[<%$row.id%>][resrc][direc]">
                            <option <% if ($row.v_settings.resrc.direc=='ASC')%>selected<%/if%> value="ASC">aufsteigend</option>      
                            <option <% if ($row.v_settings.resrc.direc=='DESC')%>selected<%/if%> value="DESC">absteigend</option>
                    </select>
                    </div>   
                  
                   <div class="checkbox col-md-4">
                        <label><input <% if ($row.v_settings.resrc.paging==1) %>checked<%/if%> type="checkbox" name="SETTINGS[<%$row.id%>][resrc][paging]" value="1" />
                        Paging aktivieren
                        </label>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Elemente pro Seite beim Paging:</label>
                        <input type="text" class="form-control js-num-field" value="<%$row.v_settings.resrc.items_per_page|sthsc|intval%>" name="SETTINGS[<%$row.id%>][resrc][items_per_page]" />
                    </div>
                  
                  </div>  
              </div>  
            <%/if%>
            
            <% if ($row.v_type=='edt') %>
            <div class="input-group">
                <input type="text" class="form-control" value="<%$row.value|hsc%>" name="FORMFLEXVAR[<%$row.id%>]" />
                <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>
            
            <% if ($row.v_type=='rdate') %>
                <input type="text" class="form-control" value="<%$row.value|date_format:"%%d.%m.%Y"%>" name="FORMFLEXVAR[<%$row.id%>]" />
            <%/if%>
            
            <% if ($row.v_type=='sc') %>
                <textarea data-theme="<%$gbl_config.ace_theme%>" name="FORMFLEXVAR[<%$row.id%>]" class="form-control se-html"><%$row.value|hsc%></textarea>
            <%/if%>
            
            <% if ($row.v_type=='hedt') %>
                    <%$row.htmleditor%>
            <%/if%> 
              
            <% if ($row.v_type=='img') %>
                <div class="form-group">
                    <label for="datei-<%$row.id%>"></label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""/>
                        <input id="datei-<%$row.id%>" name="datei[<%$row.id%>]" onchange="this.previousElementSibling.value = this.value" class="xform-control" type="file" value="" />
                        <span class="input-group-btn"><button class="btn btn-secondary" type="button">Durchsuchen...</button></span>
                     </div>
                </div>   
                
                <div class="row" id="js-dataset-img-<%$row.id%>" <% if ($row.value=="") %>style="display:none"<%/if%>>
                    <div class="col-md-3" > 
                       <a href="../file_data/flextemp/images/<%$row.value%>?a=<%$randid%>" target="_blank"><img src="../file_data/flextemp/images/<%$row.value%>?a=<%$randid%>" class="img-fluid" /></a>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-secondary" onclick="execrequest('<%$eurl%>cmd=delflexvarimg&content_matrix_id=<%$GET.content_matrix_id%>&rowid=<%$row.id%>&flxid=<%$GET.flxid%>');$('#js-dataset-img-<%$row.id%>').fadeOut();" type="button"><i class="fa fa-trash"></i></button>
                    </div>
                    <div class="col-md-8">    
                     <div class="form-group">
                            <label for="<%$row.id%>-v_settings">Individual Crop Position</label>
                            <select class="form-control custom-select" id="<%$row.id%>-v_settings" name="SETTINGS[<%$row.id%>][foto][foto_gravity]" >
                                    <option <% if ($row.v_settings.foto.foto_gravity=='default')%>selected<%/if%> value="default">- default-</option>
                                    <option <% if ($row.v_settings.foto.foto_gravity=='Center')%>selected<%/if%> value="Center">Center</option>
                                    <option <% if ($row.v_settings.foto.foto_gravity=='North')%>selected<%/if%> value="North">North</option>
                                    <option <% if ($row.v_settings.foto.foto_gravity=='NorthEast')%>selected<%/if%> value="NorthEast">NorthEast</option>
                                    <option <% if ($row.v_settings.foto.foto_gravity=='NorthWest')%>selected<%/if%> value="NorthWest">NorthWest</option>
                                    <option <% if ($row.v_settings.foto.foto_gravity=='South')%>selected<%/if%> value="South">South</option>
                                    <option <% if ($row.v_settings.foto.foto_gravity=='SouthEast')%>selected<%/if%> value="SouthEast">SouthEast</option>
                                    <option <% if ($row.v_settings.foto.foto_gravity=='SouthWest')%>selected<%/if%> value="SouthWest">SouthWest</option>
                                    <option <% if ($row.v_settings.foto.foto_gravity=='West')%>selected<%/if%> value="West">West</option>
                                    <option <% if ($row.v_settings.foto.foto_gravity=='East')%>selected<%/if%> value="East">East</option>
                            </select>    
                        </div>                        
                    </div>
                 </div>       
                 
            <%/if%>  
            
            <% if ($row.v_type=='file') %>
                <div class="form-group">
                    <label for="datei-<%$row.id%>"></label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""/>
                        <input id="datei-<%$row.id%>" name="fdatei[<%$row.id%>]" onchange="this.previousElementSibling.value = this.value" class="xform-control" type="file" value="" />
                        <span class="input-group-btn"><button class="btn btn-secondary" type="button">Durchsuchen...</button></span>
                     </div>
                </div>   
                
                <div class="row" id="js-dataset-img-<%$row.id%>" <% if ($row.value=="") %>style="display:none"<%/if%>>
                    <div class="col-md-12" > 
                        <a target="_blank" href="../file_data/flextemp/files/<%$row.value|hsc%>?a=<%$randid%>"><%$row.value%></a>
                        <a onclick="$('#js-dataset-img-<%$row.id%>').fadeOut();" href="<%$eurl%>cmd=delflexvarfile&content_matrix_id=<%$GET.content_matrix_id%>&rowid=<%$row.id%>&flxid=<%$GET.flxid%>" class="json-link"><i class="fa fa-trash text-danger"></i></a>                        
                    </div>                    
                 </div> 
            <%/if%>    
            <p class="help-block"><%$row.v_name%></p>
        </div>
     <%/foreach%>        
    <button type="submit" class="btn btn-primary" onclick="$('#plugin-submit-btn').trigger('click');">{LA_SAVE}</button>
</form>     
<%/if%>