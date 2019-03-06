<form class="jsonform form-inline" action="<%$PHPSELF%>" method="POST">
 <textarea data-theme="<%$gbl_config.ace_theme%>" class="se-html css form-control" name="FORM[layout]" rows="60" cols="125"><% $LAY.cssfilecontent %></textarea>
 <input type="hidden" name="cmd" value="savecssfile">
 <input type="hidden" name="epage" value="<%$epage%>">
 <input type="hidden" name="file" value="<%$GET.file%>">
<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
         <%$subbtn%>
</div>
</form>
