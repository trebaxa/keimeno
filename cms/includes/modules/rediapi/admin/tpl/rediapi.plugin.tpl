<section class="plugin">
<div id="js-add-redi">
    <% foreach from=$WEBSITE.node.tm_plugform.awelements item=row %>
        <input type="hidden" class="js-<%$row.type%>-<%$row.id%>" name="PLUGFORM[awelements][<%$row.id%>][id]" value="<%$row.id%>">
        <input type="hidden" class="js-<%$row.type%>-<%$row.id%>" name="PLUGFORM[awelements][<%$row.id%>][label]" value="<%$row.label%>">
        <input type="hidden" class="js-<%$row.type%>-<%$row.id%>" name="PLUGFORM[awelements][<%$row.id%>][type]" value="<%$row.type%>">
    <%/foreach%>
    
</div>  
<div class="row">
    <div class="form-group col-md-6">
        <label>Redimero System:</label>
        <select class="form-control custom-select" id="js-api-id" name="PLUGFORM[api_id]">
            <% foreach from=$WEBSITE.PLUGIN.result.api item=row %>
                <option <% if ($WEBSITE.node.tm_plugform.api_id==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
            <%/foreach%>
        </select>
    </div>   

    <div class="form-group col-md-6">
        <label>Template:</label>
        <select class="form-control custom-select" name="PLUGFORM[tpl_name]">
            <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
                <option <% if ($WEBSITE.node.tm_plugform.tpl_name==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
            <%/foreach%>
        </select>
    </div>
  </div>          
 
    <div class="form-group">
        <label>Function:</label>
        <select class="form-control custom-select" id="js-func-change" name="PLUGFORM[func_name]">
            <% foreach from=$WEBSITE.PLUGIN.result.functions item=row %>
                <option <% if ($WEBSITE.node.tm_plugform.func_name==$row.function) %>selected<%/if%> value="<%$row.function%>"><%$row.label%></option>
            <%/foreach%>
        </select>
    </div>
    
    
    <div id="js-get_specified" class="js-func-cont">
        <div class="form-group">
            <label>Artikel oder Warengruppen suchen</label>
            <div class="input-group">
                <input type="text" class="form-control" id="js-art-ser" name="SEARCH[article]" value="" />
                <div class="input-group-btn">
                    <button type="button" class="btn btn-primary" id="js-ser-btn"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
        <div id="js-ser-result"></div>
        <div class="row">
            <div class="col-md-6">
                Auswahl
                <table class="table table-hover">
                 <thead>
                 <tr>
                     <th>ID</th>
                     <th>Name</th>
                     <th>Type</th>
                     <th class="col-md-1">Sort.</th>
                     <th></th>
                 </tr>
                 </thead>
                  <tbody>
                    <% foreach from=$WEBSITE.node.tm_plugform.awelements|sortby:"order" item=row name=aweloop %>
                        <tr>
                            <td><%$row.id%></td>
                            <td><%$row.label%></td>
                            <td><%$row.type%></td>
                            <td><input type="text" class="js-num-field input-sm form-control" name="PLUGFORM[awelements][<%$row.id%>][order]" value="<%$smarty.foreach.aweloop.iteration%>"></td>
                            <td class="text-right"><button type="button" data-id="<%$row.id%>" data-type="<%$row.type%>" class="btn btn-secondary btn-sm js-pdel-click"><i class="fa fa-trash"></i></button></td>
                        </tr>               
                    <%/foreach%>
                    </tbody>
                 </table>
              </div>
              <div class="col-md-6">
              </div>   
        </div>
    </div>
    
    
     <div class="row"> 
        <div class="form-group col-md-4">
            <label>Sort:</label>
            <select class="form-control custom-select" name="PLUGFORM[column]">
                    <option <% if ($WEBSITE.node.tm_plugform.column=='pname') %>selected<%/if%> value="pname">Artikel Titel</option>                
                    <option <% if ($WEBSITE.node.tm_plugform.column=='vk') %>selected<%/if%> value="vk">Preis</option>                
                    <option <% if ($WEBSITE.node.tm_plugform.column=='order') %>selected<%/if%> value="order">manuelle Sortierung</option>
            </select>
        </div>
        
        <div class="form-group col-md-4">
            <label>Sort. Richtung:</label>
            <select class="form-control custom-select" name="PLUGFORM[sort]">
                    <option <% if ($WEBSITE.node.tm_plugform.sort=='SORT_ASC') %>selected<%/if%> value="SORT_ASC">aufsteigend</option>
                    <option <% if ($WEBSITE.node.tm_plugform.sort=='SORT_DESC') %>selected<%/if%> value="SORT_DESC">absteigend</option>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Sort. Type:</label>
            <select class="form-control custom-select" name="PLUGFORM[sort_type]">
                    <option <% if ($WEBSITE.node.tm_plugform.sort_type=='SORT_REGULAR') %>selected<%/if%> value="SORT_REGULAR">Standard</option>
                    <option <% if ($WEBSITE.node.tm_plugform.sort_type=='SORT_STRING') %>selected<%/if%> value="SORT_STRING">Zeichen</option>
                    <option <% if ($WEBSITE.node.tm_plugform.sort_type=='SORT_NUMERIC') %>selected<%/if%> value="SORT_NUMERIC">Zahl</option>
            </select>
        </div>    
    </div>
    <div class="row">
        <div class="form-group col-md-4">
            <label>Thumb Breite:</label>
            <input maxlength="3" type="text" class="form-control" name="PLUGFORM[thumb_width]" value="<% $WEBSITE.node.tm_plugform.thumb_width|sthsc %>">
        </div>    
        
        <div class="form-group col-md-4">
            <label>Thumb Höhe:</label>
            <input maxlength="3" type="text" class="form-control" name="PLUGFORM[thumb_height]" value="<% $WEBSITE.node.tm_plugform.thumb_height|sthsc %>">
        </div>  
    
        <div class="form-group col-md-4">
            <label>Methode:</label>
            <select class="form-control custom-select" name="PLUGFORM[thumb_type]">        
                    <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='crop') %>selected<%/if%> value="crop">zuschneiden (crop)</option>        
                    <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resize') %>selected<%/if%> value="resize">verkleinern (resize)</option>
                    <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resizetofit') %>selected<%/if%> value="resizetofit">verkleinern (fit)</option>
                    <option <% if ($WEBSITE.node.tm_plugform.thumb_type=='resizetofitpng') %>selected<%/if%> value="resizetofitpng">verkleinern (fit PNG)</option>
            </select>
        </div>
   </div>       
               
          
    
</section>

<script>
$( "#js-func-change" ).change(function() {
  $('.js-func-cont').hide();
  $('#js-'+$(this).val()).show();
});
$( "#js-func-change" ).trigger('change');

$( "#js-ser-btn" ).click(function() {
  simple_load('js-ser-result','<%$PATH_CMS%>admin/run.php?epage=redimeroapi.inc&cmd=article_search&api='+$('#js-api-id').val()+'&q='+$('#js-art-ser').val());
});

$( ".js-pdel-click" ).click(function() {
    $('.js-'+$(this).data('type')+'-'+$(this).data('id')).remove();
    $(this).closest('tr').fadeOut();
});
</script>