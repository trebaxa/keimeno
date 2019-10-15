
<div class="btn-group">
        <button class="btn btn-secondary" type="button" onclick="reload_dataset(<%$GET.content_matrix_id%>,<%$GET.langid%>,'<%$GET.table%>')"><i class="fa fa-refresh"></i>&nbsp;alle anzeigen</button>
        <button class="btn btn-secondary" type="button" onclick="simple_load('js-after-plugin-editor','<%$eurl%>cmd=show_addds&table=<%$GET.table%>&gid=<%$RESOURCE.flextpl.group.id%>&content_matrix_id=<% $GET.content_matrix_id %>&flxid=<%$GET.flxid%>&langid=<%$GET.langid%>')"><i class="fa fa-plus"></i>&nbsp;Neuer Datensatz</button>    
    </div>

<div id="js-after-plugin-editor">
</div>
    
    <% if (count($RESOURCE.flextpl.dataset)>0)%>
          <form action="<%$PHPSELF%>" method="POST" class="jsonform">
              <input type="hidden" value="save_dataset_table" name="cmd" />
              <input type="hidden" value="<%$epage%>" name="epage" />
              <input type="hidden" value="<%$GET.flxid%>" name="flxid" />
              <input type="hidden" value="<%$RESOURCE.flextpl.group.id%>" name="gid" />
              <input type="hidden" value="<%$GET.content_matrix_id%>" name="content_matrix_id" />
              <input type="hidden" value="<%$GET.langid%>" name="langid" />
              <input type="hidden" value="<%$GET.table%>" name="table" />
      <div class="table-responsive">   
      <%*$RESOURCE.flextpl.dataset|echoarr*%>
          <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        
                        <th class="col-md-1">Sort.</th>
                        <% foreach from=$RESOURCE.flextpl.dataset_header item=dsvalue %>
                          <% if ($dsvalue!="")%><th><%$dsvalue%></th><%/if%>
                        <%/foreach%>  
                        <th class="col-md-2"></th>  
                    </tr>
                </thead>
                <tbody>
                <% foreach from=$RESOURCE.flextpl.dataset item=row %>
                    <tr>                     
                        <td class="col-md-1">                
                            <input type="text" value="<%$row.ds_order%>" name="FORM[<%$row.row.id%>][ds_order]" class="form-control input-sm" />
                        </td>            
                        <% foreach from=$row.row key=dskey item=dsvalue %>
                            <% if ($dskey!='id' && $dskey!='ds_order' && $dskey!='ds_group')%>
                             <td <% if ($row.column.$dskey.v_type=='img') %>class="col-md-1"<%/if%>>                           
                                <% if ($row.column.$dskey.v_type=='img') %>
                                    <img class="img-fluid" src="<%$row.column.$dskey.thumb%>?r=<%$randid%>" />
                                <% elseif ($row.column.$dskey.v_type=='faw') %>
                                    <i class="fa fa-<%$dsvalue|sthsc%> fa-lg"></i>                                    
                                <% elseif ($row.column.$dskey.v_type=='seli') %>
                                       <%$dsvalue|sthsc|truncate:50%>
                                <% elseif ($row.column.$dskey.v_type=='rdate') %>
                                       <%$dsvalue|date_format:"%d.%m.%Y"%>                                                                          
                                <%else%>
                                   <%$dsvalue|sthsc|unescape:"html"|truncate:50%>
                                <%/if%>
                               </td> 
                            <%/if%>    
                        <%/foreach%>
                       <td class="col-md-2 text-right">
                         <div class="btn-group">
                            <button type="button" class="btn btn-secondary" onclick="simple_load('js-after-plugin-editor','<%$eurl%>rowid=<%$row.row.id%>&cmd=show_edit_dataset&content_matrix_id=<% $GET.content_matrix_id %>&flxid=<%$GET.flxid%>&table=<%$GET.table%>&langid=<%$GET.langid%>')"><i class="far fa-edit"></i></button>
                            <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                         </div>   
                        </td>       
                    </tr>
                <%/foreach%>
                </tbody>
            </table>
       </div>     
       
       <%$subbtn%>
       <button type="button" onclick="reload_dataset(<%$GET.content_matrix_id%>,<%$GET.langid%>,'<%$GET.table%>');" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
       </form>
       
      <%/if%>
      
        <% if ($RESOURCE.active_lang>1 && count($RESOURCE.languages)>0) %>     
        <hr />
           <div class="form-group">
                <label>Sprache importieren</label>
                <div class="input-group">
                    <select id="js-lang-import" class="form-control" id="js-lang-resrc-change">
                        <% foreach from=$RESOURCE.languages item=row %>
                            <% if ($row.approval==1 && $GET.langid!=$row.id) %>
                                <option value="<%$row.id%>"><%$row.post_lang%></option>
                            <%/if%>
                        <%/foreach%> 
                    </select>
                    <div class="input-group-btn"><button onclick="import_lang(<%$GET.content_matrix_id%>,<%$GET.langid%>,'<%$GET.table%>');" class="btn btn-primary" type="button" >GO</button></div>
                </div>
            </div>
        <%/if%>
        <script>
            function import_lang(content_matrix_id, langid, table) {                   
                var url ='<%$eurl%>content_matrix_id='+content_matrix_id+'&cmd=import_datasets_by_lang&flxid=<%$GET.flxid%>&langid='+langid+'&table='+table+'&importlang='+$('#js-lang-import').val();
                simple_load('js-resrc-content', url);                        
            }
        </script>