
        <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <% if ($GET.v_con==1)%>
            <input type="hidden" value="add_flexvars_var" name="cmd" />
          <%else%>
            <input type="hidden" value="add_dataset_var" name="cmd" />          
          <%/if%>
          <input type="hidden" value="<%$epage%>" name="epage" />
          <input type="hidden" value="<%$RESOURCE.flextpl.FID%>" name="FORM[v_ftid]" />
          <input type="hidden" value="<%$GET.varid%>" name="varid" />
          <input type="hidden" value="<%$GET.v_con%>" name="FORM[v_con]" />
          <% if ($GET.varid>0) %>
          <input type="hidden" value="<%$RESOURCE.flxvaredit.v_type%>" name="FORM[v_type]" />
          <%/if%>
          <% if ($cmd!="show_flxvar_editor")%>
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="new-flex-var-modalLabel">Neue HTML Vorlage</h4>
              </div>
          <%/if%>
          <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="js-vname">Feld Name</label>
                        <input type="text" id="js-vname" required="" value="<%$RESOURCE.flxvaredit.v_name|sthsc%>" name="FORM[v_name]" class="form-control" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="js-vdesr">Feld Beschreibung</label>
                        <input type="text" id="js-vdesr" required="" value="<%$RESOURCE.flxvaredit.v_descr|sthsc%>" name="FORM[v_descr]" class="form-control" />
                    </div>    
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Feld Typ:</label>
                        <select <% if ($GET.varid>0) %>disabled=""<%/if%> class="form-control" name="FORM[v_type]" id="js-flxvar-type">
                            <% foreach from=$RESOURCE.tplvars item=value key=vkey %>
                                <option <% if ($vkey==$RESOURCE.flxvaredit.v_type) %>selected<%/if%> value="<%$vkey%>"><%$value%></option>
                            <%/foreach%>
                        </select>
                    </div> 
                    <% if (count($RESOURCE.flextpl.groups)>0) %>
                    <div class="form-group col-md-6">
                        <label>Gruppe:</label>
                            <select name="FORM[v_gid]" class="form-control">
                               <%* <option <% if ($RESOURCE.flxvaredit.v_gid==0) %>selected<%/if%> value="0">- keine -</option>*%>
                               <% foreach from=$RESOURCE.flextpl.groups item=group %>
                                <option <% if ($RESOURCE.flxvaredit.v_gid==$group.id) %>selected<%/if%> value="<%$group.id%>"><%$group.g_name%></option>
                               <%/foreach%> 
                            </select>                
                     </div>           
                    <%/if%>
                </div>                    
             
            <%include file="resource.varopt.tpl" ident="js-flxvar-type"%>
                
          </div><!--body-->
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <%$subbtn%>
          </div>
      </form>
      
<script>
$( "#js-vname" ).blur(function() {
  if ($('#js-vdesr').val()=="") {
    $('#js-vdesr').val($( "#js-vname" ).val());
  }
});

</script>      
