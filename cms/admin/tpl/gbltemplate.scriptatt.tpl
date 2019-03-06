<div class="row">
    <div class="col-md-12">
        <h3>JavaScript Editor</h3>
         <form class="jsonform form" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
            <input type="hidden" name="tpl_id" value="<% $GET.id %>">
            <input type="hidden" name="cmd" value="save_script_att">
            <input type="hidden" name="epage" value="<% $epage %>">            
            
            <div class="form-group">
                <label>Editor</label>
                <textarea name="FORM[t_java]" class="form-control se-html js" data-theme="<%$gbl_config.ace_theme%>"><% $TPLOBJ.t_java|hsc %></textarea>
            </div>
            
            <%$subbtn%>
        </form>
    </div>
</div>