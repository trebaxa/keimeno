<%include file="cb.panel.header.tpl" title="Bereiche bearbeiten" class="panel-featured-primary"%>
<div class="form-group">
    <label>Inhaltsbereich:</label>
    <select class="form-control" id="js-flx-groupid">
        <% foreach from=$FLEXTEMP.flextpl.groups item=row %>
                <option <% if ($FLEXTEMP.flextpl.group.id==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.g_name%></option>
         <%/foreach%> 
    </select>
</div>

<div class="tc-tabs-box" id="js-flex-tabs">
    <ul class="nav nav-tabs bar_tabs" role="tablist">
        <% if (count($FLEXTEMP.flextpl.flexvars)>0)%><li <% if ($GET.showtab==0)%>class="active"<%/if%>><a data-ident="#tab-flxtpl-1" class="tc-link" href="javascript:void(0);">Inhalt</a></li><%/if%>
        <% if (count($FLEXTEMP.flextpl.datasetvarsdb)>0)%><li <% if ($GET.showtab==1)%>class="active"<%/if%>><a data-ident="#tab-flxtpl-2" class="tc-link" href="javascript:void(0);">Datensätze</a></li><%/if%>                        
    </ul>
</div>


<div class="tabs tab-content">
<% if (count($FLEXTEMP.flextpl.flexvars)>0)%>
    <div id="tab-flxtpl-1" class="tabvisi" style="display:block">  
        <div class="mt-lg">      
            <%include file="flxtpl.plugin.flxvars.tpl"%>
        </div>        
    </div>
<%/if%>

<% if (count($FLEXTEMP.flextpl.datasetvarsdb)>0)%>
<div id="tab-flxtpl-2" class="tabvisi" <% if (count($FLEXTEMP.flextpl.flexvars)==0 && count($FLEXTEMP.flextpl.datasetvarsdb)>0)%>style="display:block"<%/if%>>
   <div class="mt-lg">
    <div class="btn-group">
        <button class="btn btn-default" type="button" onclick="reload_dataset(1,<%$FLEXTEMP.flextpl.group.id%>)"><i class="fa fa-refresh"></i>&nbsp;alle anzeigen</button>
        <button class="btn btn-default" type="button" onclick="simple_load('js-after-plugin-editor','<%$eurl%>cmd=show_addds&gid=<%$FLEXTEMP.flextpl.group.id%>&content_matrix_id=<% $GET.content_matrix_id %>&flxid=<%$GET.flxid%>')"><i class="fa fa-plus"></i>&nbsp;Neuer Datensatz</button>    
    </div>
    
    <% if (count($FLEXTEMP.flextpl.dataset)>0)%>
          <form action="<%$PHPSELF%>" method="POST" class="jsonform">
              <input type="hidden" value="save_dataset_table" name="cmd" />
              <input type="hidden" value="<%$epage%>" name="epage" />
              <input type="hidden" value="<%$GET.flxid%>" name="flxid" />
              <input type="hidden" value="<%$FLEXTEMP.flextpl.group.id%>" name="gid" />
              <input type="hidden" value="<%$GET.content_matrix_id%>" name="content_matrix_id" />
      <div class="table-responsive">   
      
          <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        
                        <th class="col-md-1">Sort.</th>
                        <% foreach from=$FLEXTEMP.flextpl.dataset_header item=dsvalue %>
                          <% if ($dsvalue!="")%><th><%$dsvalue%></th><%/if%>
                        <%/foreach%>  
                        <th class="col-md-2"></th>  
                    </tr>
                </thead>
                
                <% foreach from=$FLEXTEMP.flextpl.dataset item=row %>
                    <tr>                     
                        <td class="col-md-1">                
                            <input type="text" value="<%$row.ds_order%>" name="FORM[<%$row.row.id%>][ds_order]" class="form-control input-sm" />
                        </td>            
                        <% foreach from=$row.row key=dskey item=dsvalue %>
                            <% if ($dskey!='id' && $dskey!='ds_order')%>
                             <td <% if ($row.column.$dskey.v_type=='img') %>class="col-md-1"<%/if%>>                           
                                <% if ($row.column.$dskey.v_type=='img') %>
                                    <img class="img-responsive" src="<%$row.column.$dskey.thumb%>?a=<%$randid%>" />
                                <% elseif ($row.column.$dskey.v_type=='faw') %>
                                    <i class="fa fa-<%$dsvalue|sthsc%> fa-lg"></i>                                    
                                <% elseif ($row.column.$dskey.v_type=='seli') %>
                                       <%$dsvalue|sthsc|truncate:50%>                                 
                                <%else%>
                                   <%$dsvalue|sthsc|unescape:"html"|truncate:50%>
                                <%/if%>
                               </td> 
                            <%/if%>    
                        <%/foreach%>
                       <td class="col-md-2 text-right">
                         <div class="btn-group">
                            <button type="button" class="btn btn-default" onclick="simple_load('js-after-plugin-editor','<%$eurl%>rowid=<%$row.row.id%>&cmd=show_edit_dataset&content_matrix_id=<% $GET.content_matrix_id %>&flxid=<%$GET.flxid%>&gid=<%$FLEXTEMP.flextpl.group.id%>')"><i class="fa fa-pencil-square-o"></i></button>
                            <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                         </div>   
                        </td>       
                    </tr>
                <%/foreach%>
            </table>
       </div>     
       
       <%$subbtn%>
       </form>
      </div><!-- mt-lg --> 
    <%else%>
       <%* <div class="alert alert-info mt-lg">Keine Datensätze vorhanden.</div> *%>
    <%/if%>  
</div>
<%/if%>
</div>    

<%include file="cb.panel.footer.tpl"%>

<script>
<% if ($GET.showtab==1) %>
 tab_visi_by_ident('js-flex-tabs','tab-flxtpl-2'); 
<%/if%>
 $( document ).ready(function() {
    $( "#js-flx-groupid" ).change(function() {
       reload_dataset(0, $(this).val());
    });
    
 });
 </script>