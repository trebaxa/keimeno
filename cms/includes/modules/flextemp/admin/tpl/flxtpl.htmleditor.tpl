<div class="row">
      <div class="col-md-6">
        <%include file="flxtpl.htmltpl.edit.tpl"%>
      </div>
      <div class="col-md-6">
        <% if (count($FLEXTEMP.flextpl.groups)>0) %>
        <div class="form-group">
            <label>Gruppen-Filter:</label>
                <select name="FORM[v_gid]" class="form-control" id="js-change-flex-htmlfilter-group">
                    <%*<option <% if ($FLEXTEMP.flxvaredit.v_gid==0) %>selected<%/if%> value="0">- keine -</option>*%>
                   <% foreach from=$FLEXTEMP.flextpl.groups item=group %>
                    <option <% if ($GET.gid==$group.id) %>selected<%/if%> value="<%$group.id%>"><%$group.g_name%></option>
                   <%/foreach%> 
                </select>                
         </div>           
        <%/if%>       
       <div id="js-htmledit-help">
          
          
          
       </div>
       
      </div>      
</div>     

<script>
    $( "#js-change-flex-htmlfilter-group" ).change(function() {
      simple_load('js-htmledit-help','<%$eurl%>cmd=reload_html_help&flxid=<%$GET.flxid%>&id=<%$GET.id%>&gid='+$(this).val());
    });
    $('#js-change-flex-htmlfilter-group').change();
</script>   