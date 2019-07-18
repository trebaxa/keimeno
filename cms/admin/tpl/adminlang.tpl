<%include file="cb.page.title.tpl" icon="fas fa-language" title="Backend - {LBLA_TRANSLATION_ADMIN}"%>


<div class="btn-group form-inline">
    <% if ($ATRANS.mod!='global_admintrans' || $is_keimeno_domain) %>
        <button class="btn btn-secondary" data-toggle="modal" data-target="#addjokerform">Add placeholder</button>
        <button class="btn btn-secondary" data-toggle="modal" data-target="#js-addxmllang">Upload & Replace XML file</button>
        
    <%/if%>
     <div class="btn-group">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
      - bitte wählen -
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li <% if ($GET.mod=='unset') %>active<%/if%>><a href="<%$PHPSELF%>?epage=<%$epage%>&mod=unset">{LBL_PLEASECHOOSE}</a></li>
      <% foreach from=$mod_list item=ml  %>
      <% if ($ml.mod_allowed==TRUE) %>
	<li <% if ($GET.mod==$ml.mod_id) %>class="active"<%/if%>><a class="ajax-link" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=at_load_xml&mod=<%$ml.mod_id%>"><%$ml.mod_name%></a></li>
	<%/if%>
    <%/foreach%>
    </ul>
  </div>

</div>    

<div class="modal fade" id="addjokerform" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">{LBL_ADDTRANSLATION}</h5>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
      </div>
      <div class="modal-body">
        <p>
            <input type="hidden" name="aktion" value="at_add_translation">
            <input type="hidden" name="orgaktion" value="<%$aktion%>">
            <input type="hidden" name="epage" value="<%$epage%>">
            <table class="table table-striped table-hover">
	           <tr><td>Placeholder</td><td><input type="text" class="form-control" name="FORM[]" value=""></td></tr>
	           <tr><td>Placeholder</td><td><input type="text" class="form-control" name="FORM[]" value=""></td></tr>
	           <tr><td>Placeholder</td><td><input type="text" class="form-control" name="FORM[]" value=""></td></tr>
            </table>
            <textarea class="form-control" name="MULTIADD" rows="6" cols="60"></textarea><br>
                HTML_ENTITY_DECODE: <input type="checkbox" value="1" name="he"> 
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="{LA_SAVE}">
      </div>
      </form>	
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<% if ($aktion=='at_load_xml') %>
<div class="row">
    <div class="col-md-12 form-inline">    
    <%include file="cb.panel.header.tpl" icon="fa-language" title="{LBLA_TRANSLATION_ADMIN}" %>
    <% if (count($ATRANS.all_jokers)>0) %>
    
    <table id="admintranstable" class="table table-striped table-hover" >
    <% foreach from=$ATRANS.all_jokers key=jo item=lng name=gloop %>
    		<% if $smarty.foreach.gloop.iteration == 1 %>
    		<tr class="trsubheader3">
    				<td></td>
    				<td></td>
    				<% foreach from=$lng key=localid item=joker  %>
    					<td><h4><%$localid%></h4>
    					<a  href="<%$PHPSELF%>?mod=<%$ATRANS.mod%>&epage=<%$epage%>&cmd=at_download&localid=<%$localid%>">Download [<%$localid%>]</a>
    					</td>
    			<%/foreach%>
    			</tr>    		        
        <tr>
     
    		<%/if%>
    
        
    <td style="border-right:1px solid black;">&#123;<%$jo%>&#125;</td>
    	<% foreach from=$lng key=localid item=joker name=lngloop %>
    	<% if $smarty.foreach.lngloop.iteration == 1 %>
      	<td class="text-center" width="30">
    <% if ($ATRANS.mod!='global_admintrans' || $is_keimeno_domain) %>
    		 <% foreach from=$joker.icons item=picon name=cicons %><% $picon %><%/foreach%>
    	
    	<%/if%>
    </td>   
    <%/if%>  
        <td width="300">
    	<% if ($localid!="de" || $is_keimeno_domain) %>	
               <% if ($joker.value=="") %>
               <code><span class="clickedit" data-formname="ATLNG[<%$localid%>][<%$joker.joker_sec%>]"><span class="italic">{LA_TRANSLATE}</span></span></code>
               <%else%>
                <code><span class="clickedit" data-formname="ATLNG[<%$localid%>][<%$joker.joker_sec%>]"><%$joker.value|sthsc%></span></code>
               <%/if%> 
    	<%else%>
    	   <%$joker.value|sthsc%>
    	<%/if%>
    	
    	</td>		
    	<%/foreach%>	
    	</tr>
    	<%/foreach%>
    	</table>
    
    <script>
    $('.clickedit').unbind('click');
    $('.clickedit').css('cursor','pointer');
    $('.clickedit').click(function(event) {
        event.preventDefault();
        $('#editinputfield').remove();
        $('.clickedit').show();
        if ($(this).html().length>31) {
            $(this).after('<textarea class="form-control" id="editinputfield" name="'+$(this).data('formname')+'">'+$(this).html()+'</textarea>');
        } else {
            $(this).after('<input class="form-control" autocomplete="off" id="editinputfield" type="text" value="'+$(this).html()+'" name="'+$(this).data('formname')+'">');
        }    
        $(this).hide();
        $('#editinputfield').focus();
        var spanfield =$(this);
        $('#editinputfield').blur(function() {        
            spanfield.after('<img src="./images/axloader.gif" id="editfieldloader" style="width:16px">');
            spanfield.html($(this).val());
            execrequest('<%$eurl%>&cmd=updatexml&'+$(this).attr('name')+'='+$(this).val());
            $('#editinputfield').remove();
            $('.clickedit').show();
            window.setTimeout("$('#editfieldloader').remove()",500);
        });
        $('#editinputfield').keypress(function(e) {
            if(e.which == 13) {
                $( "#editinputfield" ).trigger( "blur" );
            }
        });    
    });
    
    </script>    
    
   
    
    <%else%>
    <div class="alert alert-info">Es liegt noch kein Sprachpaket vor.</div>	
    <% /if %>
    <%include file="cb.panel.footer.tpl"%> 
    </div>
</div>




<div class="modal fade" id="js-addxmllang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
   <form action="<%$PHPSELF%>" method="post" class="form-inline" enctype="multipart/form-data">
    <input type="hidden" name="cmd" value="at_upload_xml">
    <input type="hidden" name="epage" value="<%$epage%>">
      <div class="modal-header">
        <h5 class="modal-title">{LBLA_TRANSLATION_ADMIN}</h5>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>{LBL_LANGUAGEPACKFILE} (.XML)</label>
            <input type="file" name="datei" value="">
            <p class="help-block">Be careful. Overwrites existing XML file.</p>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="{LA_SAVE}">
      </div>
      </form>	
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

	
<br>	
<a href="<%$PHPSELF%>?epage=<%$epage%>&orgaktion=<%$aktion%>&cmd=at_importmissing">{LBL_DOWNLOADALLMISSLNG}</a>	<br>
<a href="<%$PHPSELF%>?epage=<%$epage%>&orgaktion=<%$aktion%>&cmd=at_import_missing_jokers">{LBL_DOWNLOADALLMISSLNGJOKERS}</a>	<br>

	<% /if %>