<h3>Page Access</h3>
<p class="alert alert-info">Markieren, um Zugriff zu <b>verweigern</b>.</p>
<form method="post" action="<%$PHPSELF%>" class="jsonform form-inline">
    <input type="hidden" name="id" value="<% $REQUEST.id %>">
    <input type="hidden" name="cmd" value="save_pageaccess">
    <input type="hidden" name="epage" value="<%$epage%>">
    <ul>
    <% function name="websitetree" %>
        <%foreach from=$items item=element%>                    
                <li>
                    <div class="checkbox">
                        <label>
                            <input data-ident="<%$element.id%>" class="js-paccesclick" value="<%$element.id%>" <% if ($element.id|in_array:$AGROUP.page_noaccess) %>checked<%/if%> type="checkbox" name="FORM[<%$element.id%>][p_noaccess]">
                            <span><%$element.description%></span>&nbsp;<i class="fa fa-check" id="js-pacfa-<%$element.id%>"></i>
                        </label>
                    </div>
                    <%if !empty($element.children)%>
                        <ul><%call name="websitetree" items=$element.children%></ul>
                    <%/if%>
                </li>                    
        <%/foreach%>
    <%/function%>
    <% call name="websitetree" items=$AGROUP.websitetree %>
    </ul>
  <%$subbtn%>
</form>

<script>
function set_pageaccess_status(obj) {
  if (obj.prop('checked')==true) {
    $('#js-pacfa-'+obj.data('ident')).removeClass('fa-check text-success').addClass('fa-ban text-danger');
    $('#js-pacfa-'+obj.data('ident')).prev('span').removeClass('text-success').addClass('text-danger');
  } else {
    $('#js-pacfa-'+obj.data('ident')).addClass('fa-check text-success').removeClass('fa-ban text-danger');
    $('#js-pacfa-'+obj.data('ident')).prev('span').addClass('text-success').removeClass('text-danger');
  }    
}

$( ".js-paccesclick" ).click(function() {
    set_pageaccess_status($(this));
});

$( ".js-paccesclick" ).each(function( index ) {
    set_pageaccess_status($(this));
});
</script>