<%include file="cb.page.title.tpl" icon="fas fa-language" title="{LBL_LANGUAGE} Manager"%>

<div class="tab-content">

    <div class="btn-group">
        <a class="btn btn-primary" href="<%$PHPSELF%>?admin=<%$REQUEST.admin%>&epage=<%$epage%>&cmd=edit"><i class="fa fa-plus"></i> {LBL_ADD_LANGUAGE}</a>
    </div>
    
 <%*   <h3>
    <% if ($lng_obj.options.type=='yes') %>BackEnd<%/if%>
    <% if ($lng_obj.options.type=='no') %>FrontEnd<%/if%>
    <% if ($lng_obj.options.type=='cust') %>Customized<%/if%>
    </h3>*%>
    
    <% if ($cmd=='showall') %>
    <%include file="cb.panel.header.tpl" icon="fa-language" title="Sprachen"%>
    <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data" class="jsonform form-inline">
    	<input type="hidden" name="cmd" value="lng_savetable">
    	<input type="hidden" name="epage" value="<%$epage%>">
    		<table  class="table table-striped table-hover">
    		 <thead><tr>
    		 <th></th>
    		 <th>{LBL_LANGUAGE}</th>
    		 <th>Sort.</th>
    		 <th>Local ID</th>
    		 <th></th>
    		 </tr></thead>
    	<% foreach from=$lng_obj.languages item=lang %>	
    	<tr>
    		<td><img title="<% $lang.post_lang %>" src="<% $lang.thumb %>"></td>
    		<td><% $lang.post_lang %></td>
    		<td><input type="text" class="form-control" name="FORM[<% $lang.id %>][s_order]" value="<% $lang.s_order %>">
            <input type="hidden" name="FORM[<% $lang.id %>][id]" value="<% $lang.id %>"></td>
    		<td><% $lang.local %></td>
    		<td class="text-right"><div class="btn-group"><% foreach from=$lang.icons item=picon name=cicons %><% $picon %><%/foreach%></div></td>
    	</tr>		
    	<%/foreach%>		
    </table>
    <div class="subright"><%$subbtn%></div>
    	</form>
        <%include file="cb.panel.footer.tpl"%> 
    <%/if%>
    
    
    
    <% if ($cmd=='edit') %>
    <div class="row">
        <div class="col-md-6">
        <%include file="cb.panel.header.tpl" title="bearbeiten"%>
            <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data" class="jsonform form">
               <div class="form-group"> 
                <label>{LBL_LANGUAGE}</label>
                <input type="text" class="form-control" name="FORM[post_lang]" <%$LNGFORM.post_lang|ts%> value="<%$lng_obj.lng_loaded.post_lang|sthsc%>">
               </div> 
               <div class="form-group">
                <label>Language Code (ISO 639-1)</label>	
                <select class="form-control custom-select" name="FORM[local]">
            			<% foreach from=$lng_obj.iso_list item=iso %>	 
            				<option <% if ($lng_obj.lng_loaded.local==$iso.localid) %> selected <%/if%>  value="<%$iso.localid%>"><%$iso.lname%> [<%$iso.localid%>]</option>
            			<%/foreach%>    
                </select>
               </div>
               <div class="form-group"> 
                <label>{LBL_FLAG}:</label>
            	<input type="file" name="attfile" size="30" id="imgflaglng" class="submit" value="search" />
                <% if ($lng_obj.lng_loaded.thumbtrue) %>
                 <img  title="<% $lng_obj.lng_loaded.post_lang %>" src="<% $lng_obj.lng_loaded.thumb %>">
                <%/if%>
               </div> 
            <% if ($GET.id==0) %>
             <div class="form-group">
                <label>based on {LBL_LANGUAGE}:</label>	
            		<select class="form-control custom-select"  name="basedon">
            			<% foreach from=$lng_obj.languages item=lang %>	 
            				<option value="<%$lang.id%>"><%$lang.post_lang%></option>
            			<%/foreach%>	
            		</select>
                </div>    
            <%/if%>
            
            <div class="subright"><%$subbtn%></div>
                <input type="hidden" name="cmd" value="lng_savelang">
            	<input type="hidden" name="epage" value="<%$epage%>">
            	<input type="hidden" name="id" id="lng_id" value="<%$REQUEST.id%>">
            </form>
         
                <% if ($GET.id==0) %>
                    <div class="alert alert-info">
                        Legen Sie nun bequem eine neue Sprache basierend auf einer bestehenden an.
                    </div> 
                <%/if%>
            
          <%include file="cb.panel.footer.tpl" text="Verwalten Sie hier Ihre Sprachen für das Backend und Frontend."%>  
        </div>
        <div class="col-md-6">
            
        </div>
    </div>
</div><!--tab-content-->
<script>
function set_lng_id(id,src){
    $('#lng_id').val(id);
    if (src!="") {
        $('#imgflaglng').after('<img src="../images/'+src+'" width="20">');
    }
}
</script>
<%/if%>