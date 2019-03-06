<h3>{LBL_GROUP} Editor</h3>
    <form method="post" action="<%$PHPSELF%>" class="form-inline">
        <input type="hidden" name="id" value="<% $REQUEST.id %>">
        <input type="hidden" name="cmd" value="save_group">
        <input type="hidden" name="epage" value="<%$epage%>">

        <div class="form-group">
            <label for="grpname">{LBL_EMPLOYEE} {LBL_GROUP}</label>
            <input id="grpname" type="text" class="form-control" name="FORM[mgname]" value="<%$AGROUP.loaded_group.mgname|sthsc%>">
        </div><!-- /.form-group -->

        <%if ($REQUEST.id > 0) %>
            <h3>{LBL_CHOOSEAREAS}</h3>
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
            <h3>Apps</h3>
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
            <h3>System</h3>
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