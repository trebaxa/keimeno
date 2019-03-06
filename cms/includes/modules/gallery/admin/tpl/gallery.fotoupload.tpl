<!-- Modal -->
<div class="modal fade" id="galfotoup" tabindex="-1" role="dialog" aria-labelledby="galfotoupLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data" >
        <input type="hidden" value="<%$epage%>" name="epage">
        <input type="hidden" name="gid" value="<%$EDITOR.gid%>">
        <input type="hidden" name="cmd" value="insertpic">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="galfotoupLabel">Foto Upload</h4>
      </div>
      
      <div class="modal-body">      
      <div class="form-group">
		<label>Gallery:</label>
        <select class="form-control" id="js-fotoupl-galgroup" name="FORM[group_id]">
            <% foreach from=$GALADMIN.tree_select key=catid item=cname %>
                <option value="<%$catid%>"><%$cname%></option>
            <%/foreach%>
        </select>
	   </div>
	   <div class="form-group">
	   	   <label>Datei:</label>
		   <input type="file" name="attfile" accept="gif|jpg|png|jpeg" maxlength="10" >
	   </div>
   	   <div class="form-group">
		  <label>Screenshot URL:</label>
		  <input type="text" class="form-control" name="FORM[pic_scrurl]" placeholder="http://www...">
	   </div>    
	   <div class="form-group">
		  <label>admin. {LBL_TITLE}:</label>
          <input type="text" class="form-control"  name="FORM[pic_title]" size="30" value="<%$EDITOR.FORM.pic_title|hsc%>">
	   </div>	
	   <div class="form-group">
		  <label>{LBL_PUBLISHEDAT}:</label>
            <input type="text" class="form-control"  name="FORM[post_time_int]" size="10" maxlength="10" value="<%$EDITOR.FORM.post_time_int|hsc%>">(dd.mm.YYYY)
	   </div>		
	   <div class="form-group">
		  <label>{LBLA_FOTOSOURCEURL}:</label>
            <input type="text" class="form-control"  name="FORM[fotoquelle]" size="21" value="<%$EDITOR.FORM.fotoquelle|hsc%>">
	   </div>
	  </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form>
    </div>
  </div>
</div>