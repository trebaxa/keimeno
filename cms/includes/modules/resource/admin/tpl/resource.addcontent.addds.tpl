

<form action="<%$PHPSELF%>" method="POST" class="jsonform mt-lg" enctype="multipart/form-data">
    <input type="hidden" value="add_ds_to_db" name="cmd" />
    <input type="hidden" value="<%$epage%>" name="epage" />  
    <input type="hidden" value="<%$GET.flxid%>" name="flxid" />    
    <input type="hidden" value="<%$GET.rowid%>" name="rowid" />
    <input type="hidden" value="<%$GET.content_matrix_id%>" name="content_matrix_id" />
    <input type="hidden" value="<%$GET.langid%>" name="langid" />
    <input type="hidden" value="<%$GET.table%>" name="table" />
   
   
    <%*$RESOURCE.seldataset|echoarr*%>
    
     <% foreach from=$RESOURCE.flextpl.datasetvarsdb item=row %>
      
      <%assign var="column" value=$row.v_col%>
        <div class="form-group">
            <label><%$row.v_descr%></label>
            
            <% if ($row.v_type=='seli') %>
            <div class="input-group">
                <select class="form-control custom-select" name="FORM[<%$row.v_col%>]">                  
                       <% foreach from=$row.select item=rvol key=rkey %>
                            <%assign var="selisel" value="`$rkey`|`$rvol`"%>
                            <option <% if ($RESOURCE.seldataset.row.$column==$selisel) %>selected<%/if%> value="<%$rkey%>|<%$rvol%>"><%$rvol%></option>
                       <%/foreach%>                 
                </select> <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>
            
            <% if ($row.v_type=='resid') %>        
                <%*$row|echoarr*%>    
                <div class="input-group">
                <select class="form-control custom-select" name="FORM[<%$row.v_col%>]">                  
                   <% foreach from=$row.resrc_table item=rvol %>
                        <option <% if ($rvol.id==$RESOURCE.seldataset.row.$column) %>selected<%/if%> value="<%$rvol.id%>"><%$rvol.c_label%></option>
                   <%/foreach%>                
                </select> <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>
            
            <% if ($row.v_type=='link') %>
                <div class="input-group">
                <select class="form-control custom-select" name="FORM[<%$row.v_col%>]">
                       <% foreach from=$RESOURCE.menu_selectox item=rvol key=rkey %>                            
                            <%assign var="urltpl" value="{URL_TPL_`$rkey`}"%>
                            <option <% if ($urltpl==$row.value) %>selected<%/if%> value="<%$urltpl%>"><%$rvol%></option>
                       <%/foreach%>              
                </select>
                    <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>
            
            <% if ($row.v_type=='sel') %>
            <div class="input-group">
                <select class="form-control custom-select" name="FORM[<%$row.v_col%>]">
                   <% if ($GET.rowid==0) %>
                       <% foreach from=$row.select item=rvol key=rkey %>
                            <option value="<%$rvol%>"><%$rvol%></option>
                       <%/foreach%>
                <%else%>                    
                    <% foreach from=$RESOURCE.seldataset.column.$column.select item=rvol key=rkey %>
                            <option <% if ($rvol==$RESOURCE.seldataset.row.$column) %>selected<%/if%> value="<%$rvol%>"><%$rvol%></option>
                    <%/foreach%>
                <%/if%>    
                </select><div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>            
            
            <% if ($row.v_type=='radio') %>
                <input <% if ($RESOURCE.seldataset.row.$column==1) %>checked<%/if%> type="radio" value="1" name="FORM[<%$row.v_col%>]" /> Ja&nbsp;&nbsp;&nbsp;
                <input <% if ($RESOURCE.seldataset.row.$column==0) %>checked<%/if%> type="radio" value="0" name="FORM[<%$row.v_col%>]" /> Nein
            <%/if%>
            
            <% if ($row.v_type=='edt') %>
                <input type="text" class="form-control" value="<%$RESOURCE.seldataset.row.$column|hsc%>" name="FORM[<%$row.v_col%>]" />
            <%/if%>
            
            <% if ($row.v_type=='rdate') %>
                <input type="text" class="form-control" value="<%$RESOURCE.seldataset.row.$column|date_format:"%d.%m.%Y"%>" name="FORM[<%$row.v_col%>]" />
            <%/if%>
            
            <% if ($row.v_type=='faw') %>
                <label>Font Awesome Icon:</label>
                <div class="input-group">
                    <input placeholder="times" type="text" class="form-control js-fawkeypress" name="FORM[<%$row.v_col%>]" value="<%$RESOURCE.seldataset.row.$column|hsc%>"/>
                    <div class="input-group-addon"><i class="fa"></i></div>
                </div>
            <%/if%>            
            
            <% if ($row.v_type=='sc') %>
                <textarea data-theme="<%$gbl_config.ace_theme%>" name="FORM[<%$row.v_col%>]" class="form-control se-html"><%$RESOURCE.seldataset.row.$column|hsc%></textarea>
            <%/if%>
            
            <% if ($row.v_type=='hedt') %>
                <% if ($GET.rowid==0) %>
                    <%$row.htmleditor%>
                <%else%>
                    <%$RESOURCE.seldataset.column.$column.htmleditor%>
                <%/if%>    
            <%/if%> 
              
            <% if ($row.v_type=='img') %>
                <div class="form-group">
                    <label for="datei-<%$row.v_col%>"></label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""/>
                        <input id="datei-<%$row.v_col%>" name="datei[<%$row.v_col%>]" class="xform-control" onchange="this.previousElementSibling.value = this.value" type="file" value="" /></input>
                        <span class="input-group-btn"><button class="btn btn-secondary" type="button">Durchsuchen...</button></span>
                     </div>
                </div>   
                <div class="row">
                    <div class="col-md-12 help-block">Server-Einstellung "File Max Uploadsize": <%$RESOURCE.max_file_upload_size%></div>
                </div>
                <div class="row" id="js-dataset-img-<%$column%>" <% if ($RESOURCE.seldataset.row.$column=="") %>style="display:none"<%/if%>>
                    <div class="col-md-3" > 
                        <img src="../file_data/resource/images/<%$RESOURCE.seldataset.row.$column|hsc%>" class="img-fluid img-thumbnail" />
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-secondary" onclick="execrequest('<%$eurl%>cmd=deldatasetimg&rowid=<%$GET.rowid%>&flxid=<%$GET.flxid%>&column=<%$column%>&langid=<%$GET.langid%>&table=<%$GET.table%>');$('#js-dataset-img-<%$column%>').fadeOut();" type="button"><i class="fa fa-trash"></i></button>               
                    </div>
                    <div class="col-md-8">    
                     <div class="form-group">
                            <label for="<%$row.v_col%>-v_settings">Individual Crop Position</label>                           
                            <select class="form-control custom-select" id="<%$row.v_col%>-v_settings" name="FORM[ds_settings][foto][foto_gravity]" >
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='default')%>selected<%/if%> value="default">- default-</option>
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='Center')%>selected<%/if%> value="Center">Center</option>
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='North')%>selected<%/if%> value="North">North</option>
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='NorthEast')%>selected<%/if%> value="NorthEast">NorthEast</option>
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='NorthWest')%>selected<%/if%> value="NorthWest">NorthWest</option>
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='South')%>selected<%/if%> value="South">South</option>
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='SouthEast')%>selected<%/if%> value="SouthEast">SouthEast</option>
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='SouthWest')%>selected<%/if%> value="SouthWest">SouthWest</option>
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='West')%>selected<%/if%> value="West">West</option>
                                    <option <% if ($RESOURCE.seldataset.ds_settings.foto.foto_gravity=='East')%>selected<%/if%> value="East">East</option>
                            </select>    
                        </div>                        
                    </div>
                 </div>       
                 
            <%/if%>     

            <% if ($row.v_type=='file') %>
                <div class="form-group">
                    <label for="datei-<%$row.v_col%>"></label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""/></input>
                        <input id="datei-<%$row.v_col%>" name="fdatei[<%$row.v_col%>]" onchange="this.previousElementSibling.value = this.value" class="xform-control" type="file" value="" /></input>
                        <span class="input-group-btn"><button class="btn btn-secondary" type="button">Durchsuchen...</button></span>
                     </div>
                </div>  
                <div class="row">
                    <div class="col-md-12 help-block">Server-Einstellung "File Max Uploadsize": <%$RESOURCE.max_file_upload_size%></div>
                </div>
                
                <div class="row" id="js-dataset-img-<%$column%>" <% if ($RESOURCE.seldataset.row.$column=="") %>style="display:none"<%/if%>>
                    <div class="col-md-3" > 
                        <a href="../file_data/resource/files/<%$RESOURCE.seldataset.row.$column|hsc%>"><%$RESOURCE.seldataset.row.$column%></a>
                    </div>
                    <div class="col-md-9">
                        <button class="btn btn-secondary" onclick="execrequest('<%$eurl%>cmd=deldatasetfile&rowid=<%$GET.rowid%>&flxid=<%$GET.flxid%>&column=<%$column%>');$('#js-dataset-img-<%$column%>').fadeOut();" type="button"><i class="fa fa-trash"></i></button>
                    </div>
                 </div>       
                 
            <%/if%> 
                        
            <p class="help-block"><%$row.v_name%></p>
        </div>
     <%/foreach%>
  <div class="btn-group">
   <%* <button type="button" class="btn btn-secondary" onclick="reload_dataset(1);">schließen</button> *%>
    <% if ($GET.rowid>0) %>
        <%$subbtn%>
    <%else%>
        <button type="submit" class="btn btn-primary">hinzufügen</button>
    <%/if%>        
  </div>  
</form>


<%*
<script>
$( document ).ready(function() {
    $( ".js-fawkeypress" ).keyup(function() {
       $(this).next('div').find('i').removeClass().addClass('fa fa-'+$(this).val());
    });
    $('.js-fawkeypress').next('div').find('i').removeClass().addClass('fa fa-'+$(this).val());
  });    
</script>
*%>