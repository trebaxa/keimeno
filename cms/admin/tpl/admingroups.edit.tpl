<h3>{LBL_GROUP} Editor</h3>
    <form method="post" action="<%$PHPSELF%>" class="">
        <input type="hidden" name="id" value="<% $REQUEST.id %>">
        <input type="hidden" name="cmd" value="save_group">
        <input type="hidden" name="epage" value="<%$epage%>">

        <%include file="cb.panel.header.tpl" title="System"%>
            <div class="form-group">
                <label for="grpname">{LBL_EMPLOYEE} {LBL_GROUP}</label>
                <input id="grpname" type="text" class="form-control" name="FORM[mgname]" value="<%$AGROUP.loaded_group.mgname|sthsc%>">
            </div><!-- /.form-group -->
            <%$subbtn%>
        <%include file="cb.panel.footer.tpl"%>    

        <% if ($REQUEST.id > 0) %>
            <div class="row">
              <div class="col-md-4">
            <%include file="cb.panel.header.tpl" title="System"%>
            <ul>
            <% function name="menutreegroupsys" %>
                <%foreach from=$items item=element%>
                    <% if ($element.id|in_array:$allowed_menu_items) %>
                        <li>
                            <div class="checkbox">
                                <label>                        
                                    <input data-ident="<%$element.id%>" class="js-agroupclick" value="<%$element.id%>" <% if ($element.id|in_array:$AGROUP.allowed) %>checked<%/if%> type="checkbox" name="menue_id[<%$element.id%>]">
                                    <span><%$element.mname%></span>&nbsp;<i class="fa fa-check" id="js-agroupacc-<%$element.id%>"></i>
                                </label>
                            </div>                                    
                            <%if !empty($element.children)%>
                                <ul><%call name="menutreegroup" items=$element.children%></ul>
                            <%/if%>
                        </li>
                    <%/if%>
                <%/foreach%>
            <%/function%>
            <% call name="menutreegroupsys" items=$system_menu %>
            </ul>  
            <%$subbtn%>
            <%include file="cb.panel.footer.tpl"%>          
            </div>
                       <div class="col-md-4">
            <%include file="cb.panel.header.tpl" title="Apps"%>   
            <ul>
            <% function name="menutreegroupapp" %>
                <%foreach from=$items item=element%>
                    <% if ($element.id|in_array:$allowed_menu_items) %>
                        <li>
                            <div class="checkbox">
                                <label>
                                    <input data-ident="<%$element.id%>" class="js-agroupclick" value="<%$element.id%>" <% if ($element.id|in_array:$AGROUP.allowed) %>checked<%/if%> type="checkbox" name="menue_id[<%$element.id%>]">
                                    <span><%$element.mname%></span>&nbsp;<i class="fa fa-check" id="js-agroupacc-<%$element.id%>"></i>
                                   </label>
                            </div>
                            <%if !empty($element.children)%>
                                <ul><%call name="menutreegroup" items=$element.children%></ul>
                            <%/if%>
                        </li>
                    <%/if%>
                <%/foreach%>
            <%/function%>
            <% call name="menutreegroupapp" items=$app_menu %>
            </ul>
            <%$subbtn%>
            <%include file="cb.panel.footer.tpl"%>
            </div>
            <div class="col-md-4">
            <%include file="cb.panel.header.tpl" title="{LBL_CHOOSEAREAS}"%>
            <ul>
            <% function name="menutreegroup" %>
                <%foreach from=$items item=element%>
                    <% if ($element.id|in_array:$allowed_menu_items) %>
                        <li>
                            <div class="checkbox">
                                <label>
                                    <input data-ident="<%$element.id%>" class="js-agroupclick" value="<%$element.id%>" <% if ($element.id|in_array:$AGROUP.allowed) %>checked<%/if%> type="checkbox" name="menue_id[<%$element.id%>]">
                                    <span><%$element.mname%></span>&nbsp;<i class="fa fa-check" id="js-agroupacc-<%$element.id%>"></i>
                                </label>
                            </div>
                            <%if !empty($element.children)%>
                                <ul><%call name="menutreegroup" items=$element.children%></ul>
                            <%/if%>
                        </li>
                    <%/if%>
                <%/foreach%>
            <%/function%>
            <% call name="menutreegroup" items=$adminmenu %>
            </ul>
            <%$subbtn%>
            <%include file="cb.panel.footer.tpl"%>
            </div>
 
          
            </div>
        <%/if%>
        
    </form>

<script>
function set_agroupaccess_status(obj) {
  if (obj.prop('checked')==false) {
    $('#js-agroupacc-'+obj.data('ident')).removeClass('fa-check text-success').addClass('fa-ban text-danger');
    $('#js-agroupacc-'+obj.data('ident')).prev('span').removeClass('text-success').addClass('text-danger');
  } else {
    $('#js-agroupacc-'+obj.data('ident')).addClass('fa-check text-success').removeClass('fa-ban text-danger');
    $('#js-agroupacc-'+obj.data('ident')).prev('span').addClass('text-success').removeClass('text-danger');
  }    
}

$( ".js-agroupclick" ).click(function() {
    set_agroupaccess_status($(this));
});

$( ".js-agroupclick" ).each(function( index ) {
    set_agroupaccess_status($(this));
});
</script>    