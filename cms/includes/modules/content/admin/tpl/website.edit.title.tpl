<!-- TAB Inhalt > TITEL -->
<% if ($TPLOBJ.url_redirect!="")%>
    <p class="alert alert-danger">Weiterleitung auf <%$TPLOBJ.url_redirect%></p>
<%/if%>
<div class="row">
    <div class="col-md-6">
       <% if ($TPLOBJ.is_framework==0) %>
        <div class="form-group">
            <label for="wf-desc">admin. {LBL_DESCRIPTION}</label>
            <input id="wf-desc" required="" type="text" class="form-control" name="FORM_TEMPLATE[description]"  value="<% $TPLOBJ.description|hsc %>">
        </div><!-- /.form-group -->
        <div class="form-group">
            <label for="wf-link">{LBLA_TITLE_LINK}</label>
            <input id="wf-link" required="" type="text" class="form-control" name="FORM[linkname]" value="<% if ($TPLOBJ.formcontent.linkname=='') %><% $TPLOBJ.description|hsc %><%else%><% $TPLOBJ.formcontent.linkname|hsc %><%/if%>">
            <span class="help-block">Template Integration: &lt;%$PAGEOBJ.linkname%&gt;</span>
        </div><!-- /.form-group -->
        <div class="form-group">
            <label for="wf-link">Alternativer Titel</label>
            <input id="wf-link" type="text" class="form-control" name="FORM[t_alt_title]" value="<% $TPLOBJ.formcontent.t_alt_title|hsc %>">
            <span class="help-block">Template Integration: &lt;%$PAGEOBJ.t_alt_title%&gt;</span>
        </div><!-- /.form-group -->        
       <%/if%> 
    </div><!-- /.col-md-6 -->
    <div class="col-md-6">
        <% if ($TPLOBJ.is_framework==0) %>
            <div class="form-group">
                <label for="wf-htalink">Bezeichnung in URL</label>
                <input id="wf-htalink" type="text" class="form-control" name="FORM[t_htalinklabel]" value="<% $TPLOBJ.formcontent.t_htalinklabel|onlyalpha|sthsc %>">
                <span class="help-block">z.B.: <% if ($TPLOBJ.formcontent.t_htalinklabel=="") %>/<% $TPLOBJ.formcontent.linkname|onlyalpha %>-<% $TPLOBJ.formcontent.tid %>.html<%else%>/<% $TPLOBJ.formcontent.t_htalinklabel|onlyalpha %>.html<%/if%> | <% $TPLOBJ.urltpl %></span>
            </div><!-- /.form-group -->
        <%/if%>
        
        <div class="form-group">
            <label>{LBL_LANGUAGE}:</label>
            <select class="form-control" id="js-lang-change-editor">
            <%foreach from=$TPLOBJ.langfe item=row%>
                <option <% if ($GET.uselang==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.local%> - <%$row.post_lang%></option>
            <%/foreach%>
            </select>
        </div><!-- /.form-group -->
        <div class="row">        
            <div class="form-group col-md-6">
                <!--<label for="">Replizieren</label> -->
                <a class="btn btn-default json-link" href="<%$eurl%>cmd=replicatelang&id=<% $TPLOBJ.formcontent.tid %>&uselang=<% $GET.uselang %>"><i class="fa fa-language" aria-hidden="true"></i> {LA_INHALTAUFALLESPRACHEN}</a>
            </div><!-- /.form-group -->
            <div class="col-md-6 text-right">
                <% $subbtn %>
            </div>
        </div>
        
    </div><!-- /.col-md-6 -->
</div><!-- /.row -->
<script>$('.previewlink').attr('href','../'+$('#wf-htalink').val()+'.html');

$( "#js-lang-change-editor" ).unbind('change');
$( "#js-lang-change-editor" ).change(function() {
    var url = '<%$PHPSELF%>?epage=websitemanager.inc&cmd=page_axedit&id=<%$GET.id%>&uselang='+$(this).val();
    simple_load('admincontent',url);    
});
</script>