<form action="<%$PHPSELF%>" method="POST" class="jsonform">
  <input type="hidden" value="save_htmltpl" name="cmd" />
  <input type="hidden" value="<%$epage%>" name="epage" />
  <input type="hidden" value="<%$GET.id%>" name="id" />
    <div class="form-group">
        <label>Vorlagen Name</label>
        <input type="text" required="" value="<%$RESOURCE.flxedit.t_name|sthsc%>" name="FORM[t_name]" class="form-control" />
    </div>
    <div class="form-group">
        <label>HTML Code:</label>
        <textarea data-theme="<%$gbl_config.ace_theme%>" name="FORM[t_tpl]" class="form-control se-html"><%$RESOURCE.flxedit.t_tpl|hsc%></textarea>
    </div>
    <div class="form-group">
        <label>JavaScript Code:</label>
        <textarea name="FORM[t_java]" class="form-control se-html"><%$RESOURCE.flxedit.t_java|hsc%></textarea>
    </div>
    <div class="btn-group">
        <a class="btn btn-secondary" href="#" onclick="reload_htmltpl();">Abbruch</a>
        <%$subbtn%>
    </div>       
</form>