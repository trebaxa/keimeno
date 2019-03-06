<form method="post" action="<% $PHPSELF %>" enctype="multipart/form-data">
    <input type="hidden" name="epage" value="<%$epage%>"><input type="hidden" name="cmd" value="a_file_update">
    <input type="hidden" name="ftarget" value= "<% $doc_center.submit_btn %>" />
    <input type="hidden" name="id" value="<%$smarty.get.id%>">
    
    
    <h3>Edit <% $doc_center.FORM.file %></h3>
    <div class="row">
        <div class="col-md-6">                
            <div class="form-group">
                <label>{LBLA_TITLE}</label>
                <input type="text" class="form-control" name="FORM[title]"  value="<% $doc_center.FORM.title|sthsc %>">
            </div>
            <div class="form-group">
                <label>{LBLA_DESCRIPTION}</label>
                <% $doc_center.FORM.editor %>
            </div>
        </div>
        <div class="col-md-6">    
            <%*<div class="form-group">
                <label>{LBLA_ICON}</label>
                <input type="file" name="datei_icon"  value="{LBL_DC_SEARCH}"><br>
                <% $doc_center.FORM.icon_gen_thumb_picture %>
            </div>
            *%>                            
            <div class="form-group">
                <label>{LBL_FILE}</label>
                <input type="file" name="datei"  value="{LBL_DC_SEARCH}" /><br> 
                <% $doc_center.FORM.file_gen_thumb_picture %>                    
            </div>
       </div>
    </div>        
    <%$subbtn%>
</form>
      