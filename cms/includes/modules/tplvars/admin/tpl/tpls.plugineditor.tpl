<style>
label {
    font-weight:bold;
    display:block;
    margin-top:10px;
}

img.delplugimg {
    margin-left: -20px;margin-top: 5px;cursor: pointer;position: absolute;
}
</style>

<% foreach from=$TPLVARS.addedvars item=row %>
    <% assign var=formkey value=$row.m_placeholder %>
    <div class="form-group">
    <label><%$row.var_desc%><% if ($row.m_hint!="") %><small class="text-info pull-right">&nbsp;<i class="fa fa-info-circle"></i>&nbsp;<%$row.m_hint%></small><%/if%></label>  
      
    <% if ($row.var_type=='editfield') %>
        <input type="text" class="form-control" name="PLUGOPT[<%$row.m_placeholder%>]" value="<% $TPLVARS.tm_plugopt.$formkey|hsc %>">            
    <%/if%>
    <% if ($row.var_type=='htmledit') %>
       <%$row.htmleditor%>
    <%/if%> 
    <% if ($row.var_type=='select') %>
    <select class="form-control" name="PLUGOPT[<%$row.m_placeholder%>]">
        <% foreach from=$TPLVARS.selectboxes.$formkey item=value %>
            <option <% if ($value==$TPLVARS.tm_plugopt.$formkey) %>selected<%/if%> value="<%$value%>"><%$value%></option>
        <%/foreach%>
        </select>
    <%/if%>
     <% if ($row.var_type=='script') %>
        <textarea class="se-html form-control" style="width:100%;" rows="30" name="PLUGOPT[<%$row.m_placeholder%>]"><% $TPLVARS.tm_plugopt.$formkey|hsc %></textarea>
    <%/if%>
    </div>
    
    <% if ($row.var_type=='imgfile') %>    
    <div class="form-group">
        <label for="datei-<%$row.m_placeholder%>"></label>
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""></input>
                <input id="datei-<%$row.m_placeholder%>" name="<%$row.m_placeholder%>" class="xform-control" type="file" value="<% $TPLVARS.tm_plugopt.$formkey|hsc %>"  onchange="$('#plugin-submit-btn').click()"></input>
                <span class="input-group-btn"><button class="btn btn-default" type="button">Durchsuchen...</button></span>
            </div>
    </div>
 
            <div class="img-<%$formkey%>" <% if ($TPLVARS.thumbs.$formkey=="") %>style="display:none"<%/if%>>
                <img src="<%$TPLVARS.thumbs.$formkey%>" class="tplvarthumbimg img-thumbnail" data-cmid="<% $GET.content_matrix_id%>" data-optkey="<%$formkey%>">
                <i data-cmid="<% $GET.content_matrix_id%>" data-optkey="<%$formkey%>" class="fa fa-trash delplugimg"></i>
            </div>
            <input id="img-hidden-<%$formkey%>" type="hidden" name="PLUGOPT[<%$row.m_placeholder%>]" value="<% $TPLVARS.tm_plugopt.$formkey|hsc %>">
    <%/if%>      
   
<%/foreach%>
<script>
set_script_editor();
$('.delplugimg').click(function() {     
    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=delimg&cmid='+$(this).data('cmid')+'&optkey='+$(this).data('optkey'));
    $('.img-'+$(this).data('optkey')).slideUp();
    $('#img-hidden-'+$(this).data('optkey')).val(''); 
});

function after_submit_plugin_form() {
$(".tplvarthumbimg").each(function() {  
  var img = $(this);
    $.getJSON('<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_img&cmid='+$(this).data('cmid')+'&optkey='+$(this).data('optkey'), function(data) {
                if (data.thumb!="") {                 
                  img.attr('src',data.thumb+'?a='+Math.random(10000));
                  img.parent().slideDown(); 
                  $('#img-hidden-'+img.data('optkey')).val(data.image); 
                } else {
                    img.parent().slideUp();
                    $('#img-hidden-'+img.data('optkey')).val(''); 
                }
        });
  });    
	    
} 
</script>