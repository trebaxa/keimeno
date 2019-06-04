<form action="<%$PHPSELF%>" method="POST" class="jsonform" enctype="multipart/form-data">
          <input type="hidden" value="save_flxvar_for_plugin" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          <input type="hidden" value="<%$RESOURCE.flextpl.FID%>" name="flxid" />
          <input type="hidden" value="<%$RESOURCE.flextpl.FID%>" name="CM[c_ftid]" />
          <input type="hidden" value="<%$GET.content_matrix_id%>" name="content_matrix_id" />
          <input type="hidden" value="<%$GET.langid%>" name="langid" />
          
        <div class="form-group">
            <label>Beschriftung:</label>
            <input class="form-control" name="CM[c_label]" type="text" required="" value="<%$RESOURCE.content.c_label|sthsc%>" />
        </div>  
        
        <hr />
          
    <% foreach from=$RESOURCE.flextpl.flexvars item=row %>      
        <div class="form-group">
            <label><%$row.v_descr%></label>
            
            <% if ($row.v_type=='sel') %>
                <select class="form-control" name="FORMFLEXVAR[<%$row.id%>]">
                       <% foreach from=$row.select item=rvol key=rkey %>
                            <option <% if ($rvol==$row.value) %>selected<%/if%> value="<%$rvol%>"><%$rvol%></option>
                       <%/foreach%>              
                </select>
            <%/if%>
            
            <% if ($row.v_type=='seli') %>
                <select class="form-control" name="FORMFLEXVAR[<%$row.id%>]">                  
                   <% foreach from=$row.select item=rvol key=rkey %>
                        <%assign var="selisel" value="`$rkey`|`$rvol`"%>
                        <option <% if ($row.value==$selisel) %>selected<%/if%> value="<%$rkey%>|<%$rvol%>"><%$rvol%></option>
                   <%/foreach%>                
                </select>
            <%/if%>
            
            <% if ($row.v_type=='radio') %>
                <input <% if ($row.value==1) %>checked<%/if%> type="radio" value="1" name="FORMFLEXVAR[<%$row.id%>]" /> Ja&nbsp;&nbsp;&nbsp;
                <input <% if ($row.value==0) %>checked<%/if%> type="radio" value="0" name="FORMFLEXVAR[<%$row.id%>]" /> Nein
            <%/if%>
            
            <% if ($row.v_type=='resid') %>            
                <select class="form-control" name="FORMFLEXVAR[<%$row.id%>]">                  
                   <% foreach from=$RESOURCE.flextpl.resrc_table item=rvol %>
                        <option <% if ($rvol.id==$row.value) %>selected<%/if%> value="<%$rvol.id%>"><%$rvol.c_label%></option>
                   <%/foreach%>                
                </select>
            <%/if%>
            
            <% if ($row.v_type=='link') %>
                <div class="input-group">
                <select class="form-control" name="FORMFLEXVAR[<%$row.id%>]">
                    <option <% if ($row.value=="") %>selected<%/if%> value="">- kein Link -</option>
                       <% foreach from=$RESOURCE.menu_selectox item=rvol key=rkey %>                            
                            <%assign var="urltpl" value="{URL_TPL_`$rkey`}"%>
                            <option <% if ($urltpl==$row.value) %>selected<%/if%> value="<%$urltpl%>"><%$rvol%></option>
                       <%/foreach%>              
                </select>
                    <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>
            
            <% if ($row.v_type=='edt') %>
                <input type="text" class="form-control" value="<%$row.value|hsc%>" name="FORMFLEXVAR[<%$row.id%>]" />
            <%/if%>
            
            <% if ($row.v_type=='rdate') %>
                <input type="text" class="form-control" value="<%$row.value|hsc%>" name="FORMFLEXVAR[<%$row.id%>]" />
            <%/if%>
            
            <% if ($row.v_type=='sc') %>
                <textarea data-theme="<%$gbl_config.ace_theme%>" name="FORMFLEXVAR[<%$row.id%>]" class="form-control se-html"><%$row.value|hsc%></textarea>
            <%/if%>
            
            <% if ($row.v_type=='hedt') %>
                    <%$row.htmleditor%>
            <%/if%> 
            
              <% if ($row.v_type=='file') %>
                <div class="form-group">
                    <label for="datei-<%$row.id%>"></label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""/>
                        <input id="datei-<%$row.id%>" name="fdatei[<%$row.id%>]" onchange="this.previousElementSibling.value = this.value" class="xform-control" type="file" value="" />
                        <span class="input-group-btn"><button class="btn btn-default" type="button">Durchsuchen...</button></span>
                     </div>
                </div> 
                <div class="row">
                    <div class="col-md-12 help-block">Server-Einstellung "File Max Uploadsize": <%$RESOURCE.max_file_upload_size%></div>
                </div>  
                
                <div class="row" id="js-dataset-img-<%$row.id%>" <% if ($row.value=="") %>style="display:none"<%/if%>>
                    <div class="col-md-12" > 
                        <a target="_blank" href="../file_data/resource/files/<%$row.value|hsc%>?a=<%$randid%>"><%$row.value%></a>
                        <a onclick="$('#js-dataset-img-<%$row.id%>').fadeOut();" href="<%$eurl%>cmd=del_resrc_var_file&content_matrix_id=<%$GET.content_matrix_id%>&rowid=<%$row.id%>&flxid=<%$GET.flxid%>&langid=<%$GET.langid%>" class="json-link"><i class="fa fa-trash text-danger"></i></a>                        
                    </div>                    
                 </div> 
            <%/if%> 
              
            <% if ($row.v_type=='img') %>
                <div class="form-group">
                    <label for="datei-<%$row.id%>"></label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""/>
                        <input id="datei-<%$row.id%>" name="datei[<%$row.id%>]" onchange="this.previousElementSibling.value = this.value" class="xform-control" type="file" value="" /></input>
                        <span class="input-group-btn"><button class="btn btn-default" type="button">Durchsuchen...</button></span>
                     </div>
                </div> 
                <div class="row">
                    <div class="col-md-12 help-block">Server-Einstellung "File Max Uploadsize": <%$RESOURCE.max_file_upload_size%></div>
                </div>  
                
                <div class="row" id="js-dataset-img-<%$row.id%>" <% if ($row.value=="") %>style="display:none"<%/if%>>
                    <div class="col-md-3" > 
                      <a href="../file_data/resource/images/<%$row.value%>" target="_blank"><img src="../file_data/resource/images/<%$row.value%>?a=<%$randid%>" class="img-responsive" /></a>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-default" onclick="execrequest('<%$eurl%>cmd=delflexvarimg&content_matrix_id=<%$GET.content_matrix_id%>&rowid=<%$row.id%>&flxid=<%$GET.flxid%>&langid=<%$GET.langid%>');$('#js-dataset-img-<%$row.id%>').fadeOut();" type="button"><i class="fa fa-trash"></i></button>
                    </div>
                    <div class="col-md-8">    
                     <div class="form-group">
                            <label for="<%$row.id%>-v_settings">Individual Crop Position</label>
                            <select class="form-control" id="<%$row.id%>-v_settings" name="SETTINGS[<%$row.id%>][foto][foto_gravity]" >
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
            <p class="help-block"><%$row.v_name%></p>
        </div>
     <%/foreach%>        
     <button type="submit" class="btn btn-primary">{LA_SAVE}</button>
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
</form>   