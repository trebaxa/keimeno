<%include file="cb.panel.header.tpl" title="Neuer Datensatz" class="panel-featured-primary"%>

<form action="<%$PHPSELF%>" method="POST" class="jsonform" enctype="multipart/form-data">
    <input type="hidden" value="add_ds_to_db" name="cmd" />
    <input type="hidden" value="<%$epage%>" name="epage" />  
    <input type="hidden" value="<%$GET.flxid%>" name="flxid" />    
    <input type="hidden" value="<%$GET.rowid%>" name="rowid" />
    <input type="hidden" value="<%$GET.content_matrix_id%>" name="content_matrix_id" />
    <% if ($GET.gid>0)%><input type="hidden" value="<%$GET.gid%>" name="FORM[ds_group]" /><%/if%>
    
     <% foreach from=$FLEXTEMP.flextpl.datasetvarsdb item=row %>
      
      <%assign var="column" value=$row.v_col%>
        <div class="form-group">
            <label><%$row.v_descr%></label>
            
            <% if ($row.v_type=='seli') %>
                <select class="form-control custom-select" name="FORM[<%$row.v_col%>]">
                   <% if ($GET.rowid==0) %>
                       <% foreach from=$row.select item=rvol key=rkey %>
                            <option value="<%$rkey%>|<%$rvol%>"><%$rvol%></option>
                       <%/foreach%>
                <%else%>                    
                    <% foreach from=$FLEXTEMP.seldataset.column.$column.select item=rvol key=rkey %>
                            <option <% if ($rvol==$FLEXTEMP.seldataset.row.$column) %>selected<%/if%> value="<%$rkey%>|<%$rvol%>"><%$rvol%></option>
                    <%/foreach%>
                <%/if%>    
                </select>
            <%/if%>
            
            <% if ($row.v_type=='link') %>
                <div class="input-group">
                <select class="form-control custom-select" name="FORM[<%$row.v_col%>]">
                <option value="">- kein Link -</option>
                       <% foreach from=$FLEXTEMP.menu_selectox item=rvol key=rkey %>                            
                            <%assign var="urltpl" value="{URL_TPL_`$rkey`}"%>
                            <option <% if ($urltpl==$FLEXTEMP.seldataset.row.$column) %>selected<%/if%> value="<%$urltpl%>"><%$rvol%></option>
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
                    <% foreach from=$FLEXTEMP.seldataset.column.$column.select item=rvol key=rkey %>
                            <option <% if ($rvol==$FLEXTEMP.seldataset.row.$column) %>selected<%/if%> value="<%$rvol%>"><%$rvol%></option>
                    <%/foreach%>
                <%/if%>    
                </select>
                <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>            
            
            <% if ($row.v_type=='edt') %>
                <div class="input-group">
                 <input type="text" class="form-control" value="<%$FLEXTEMP.seldataset.row.$column|hsc%>" name="FORM[<%$row.v_col%>]" />
                 <div class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button></div>
                </div>
            <%/if%>
            
            <% if ($row.v_type=='faw') %>
                <label>Font Awesome Icon:</label>
                <div class="input-group">
                    <input placeholder="times" type="text" class="form-control js-fawkeypress" name="FORM[<%$row.v_col%>]" value="<%$FLEXTEMP.seldataset.row.$column|hsc%>"/>
                    <div class="input-group-addon"><i class="fa"></i></div>
                </div>
            <%/if%>            
            
            <% if ($row.v_type=='sc') %>
                <textarea data-theme="<%$gbl_config.ace_theme%>" name="FORM[<%$row.v_col%>]" class="form-control se-html"><%$FLEXTEMP.seldataset.row.$column|hsc%></textarea>
            <%/if%>
            
            <% if ($row.v_type=='hedt') %>
                <% if ($GET.rowid==0) %>
                    <%$row.htmleditor%>
                <%else%>
                    <%$FLEXTEMP.seldataset.column.$column.htmleditor%>
                <%/if%>    
            <%/if%> 
              
            <% if ($row.v_type=='img') %>
                <div class="form-group">
                    <label for="datei-<%$row.v_col%>"></label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""/>
                        <input id="datei-<%$row.v_col%>" name="datei[<%$row.v_col%>]" class="xform-control" onchange="this.previousElementSibling.value = this.value" type="file" value="" />
                        <span class="input-group-btn"><button class="btn btn-secondary" type="button">Durchsuchen...</button></span>
                     </div>
                </div>   
                
                <div class="row" id="js-dataset-img-<%$column%>" <% if ($FLEXTEMP.seldataset.row.$column=="") %>style="display:none"<%/if%>>
                    <div class="col-md-4" > 
                        <img src="../file_data/flextemp/images/<%$FLEXTEMP.seldataset.row.$column|hsc%>?a=<%$randid%>" class="img-fluid img-thumbnail" />
                    </div>
                    <div class="col-md-2">
                        <div class="btn-group">
                            <% if ($FLEXTEMP.seldataset.row.$column|pathinfo:$smarty.const.PATHINFO_EXTENSION=='jpg' || $FLEXTEMP.seldataset.row.$column|pathinfo:$smarty.const.PATHINFO_EXTENSION=='jpeg') %>
                                <a class="btn btn-secondary ajax-link" data-target="js-after-plugin-editor" onclick="$('#modal_frame').modal('hide');" href="<%$eurl%>cmd=show_dataset_jcrop&rowid=<%$GET.rowid%>&column=<%$column%>&content_matrix_id=<%$GET.content_matrix_id%>&flxid=<%$GET.flxid%>&gid=<%$GET.gid%>&aid=<%$row.id%>"><i class="fas fa-cut"></i></a>                                
                            <%/if%>  
                            <button class="btn btn-secondary" onclick="execrequest('<%$eurl%>cmd=deldatasetimg&rowid=<%$GET.rowid%>&flxid=<%$GET.flxid%>&column=<%$column%>');$('#js-dataset-img-<%$column%>').fadeOut();" type="button"><i class="fa fa-trash"></i></button>
                        
                        </div>
                                       
                    </div>
                    <div class="col-md-6">    
                    
                    <% if ($row.v_opt.img.foto_resize=='crop') %>  
                     <div class="form-group">
                            <label for="<%$row.v_col%>-v_settings">Individual Crop Position</label>
                            <select class="form-control custom-select" id="<%$row.v_col%>-v_settings" name="FORM[ds_settings][foto][foto_gravity]" >
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='default')%>selected<%/if%> value="default">- default-</option>
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='Center')%>selected<%/if%> value="Center">Center</option>
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='North')%>selected<%/if%> value="North">North</option>
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='NorthEast')%>selected<%/if%> value="NorthEast">NorthEast</option>
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='NorthWest')%>selected<%/if%> value="NorthWest">NorthWest</option>
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='South')%>selected<%/if%> value="South">South</option>
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='SouthEast')%>selected<%/if%> value="SouthEast">SouthEast</option>
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='SouthWest')%>selected<%/if%> value="SouthWest">SouthWest</option>
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='West')%>selected<%/if%> value="West">West</option>
                                    <option <% if ($FLEXTEMP.seldataset.row.ds_settings.foto.foto_gravity=='East')%>selected<%/if%> value="East">East</option>
                            </select>    
                        </div>  
                        <%/if%>                      
                    </div>
                 </div>       
                 
            <%/if%>     

            <% if ($row.v_type=='file') %>
                <div class="form-group">
                    <label for="datei-<%$row.v_col%>"></label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""/>
                        <input id="datei-<%$row.v_col%>" name="fdatei[<%$row.v_col%>]" onchange="this.previousElementSibling.value = this.value" class="xform-control" type="file" value="" />
                        <span class="input-group-btn"><button class="btn btn-secondary" type="button">Durchsuchen...</button></span>
                     </div>
                </div>   
                
                <div class="row" id="js-dataset-img-<%$column%>" <% if ($FLEXTEMP.seldataset.row.$column=="") %>style="display:none"<%/if%>>
                    <div class="col-md-12" > 
                        <a href="../file_data/flextemp/files/<%$FLEXTEMP.seldataset.row.$column|hsc%>"><%$FLEXTEMP.seldataset.row.$column%></a>
                        <a onclick="$('#js-dataset-img-<%$column%>').fadeOut();" href="<%$eurl%>cmd=deldatasetfile&rowid=<%$GET.rowid%>&flxid=<%$GET.flxid%>&column=<%$column%>" class="json-link"><i class="fa fa-trash text-danger"></i></a>
                    </div><%*
                    <div class="col-md-9">
                        <button class="btn btn-secondary" onclick="execrequest('<%$eurl%>cmd=deldatasetfile&rowid=<%$GET.rowid%>&flxid=<%$GET.flxid%>&column=<%$column%>');$('#js-dataset-img-<%$column%>').fadeOut();" type="button"><i class="fa fa-trash"></i></button>
                    </div>
                    *%>
                 </div>       
                 
            <%/if%> 
                        
            <p class="help-block"><%$row.v_name%></p>
        </div>
     <%/foreach%>
  <div class="btn-group">
    <button type="button" class="btn btn-secondary" onclick="reload_dataset(1);">schließen</button>
    <% if ($GET.rowid>0) %>
        <%$subbtn%>
    <%else%>
        <button type="submit" class="btn btn-primary">hinzufügen</button>
    <%/if%>        
  </div>  
</form>

<%include file="cb.panel.footer.tpl"%>

<script>
$( document ).ready(function() {
    $( ".js-fawkeypress" ).keyup(function() {
       $(this).next('div').find('i').removeClass().addClass('fa fa-'+$(this).val());
    });
    $('.js-fawkeypress').next('div').find('i').removeClass().addClass('fa fa-'+$(this).val());
  });    
</script>