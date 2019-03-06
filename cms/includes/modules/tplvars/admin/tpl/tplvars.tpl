<div class="page-header">
    <h1><i class="fa fa-file-o"></i>Template Vorlagen</h1>
</div>

<div class="tc-tabs-box tc-tabs-right" id="tplvartabs">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a data-ident="#tabvartpl1" class="tc-link active" href="javascript:void(0);">Templates</a></li>
        <li><a data-ident="#tabvartpl2" class="tc-link" href="javascript:void(0);">Template Variablen</a></li>
    </ul>
</div>

<div class="tabs">
<!-- TAB1 -->
<div id="tabvartpl1" class="tabvisi" style="display:block">
    <div class="btn-group"><a class="btn btn-default" href="javascript:void(0)" onclick="$('#tpleditor').slideDown(200)">Neue Vorlage</a></div>
    
    <div id="tpleditor" style="display:none">
        <form action="<%$PHPSELF%>" method="POST" class="jsonform">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="cmd" value="addtpl"> 
            <div class="form-group"> 
                <label>Vorlage Name:</label>
                <input type="text" class="form-control" name="FORM[tpl_name]" value="">
            </div>
            <%$subbtn%>
        </form>
    </div>
  <div id="tplist"></div>
  <% if ($cmd=='edittpl' || $cmd=='ax_edittpl') %>
    <%include file="tpl.editor.tpl"%>
  <%/if%>
</div><!-- TAB! END -->

<div id="tabvartpl2" class="tabvisi">
<div class="row">
    <div class="col-md-6">
    <h3>Template Variablen</h3>
     <div class="btn-group"><a class="btn btn-default" href="javascript:void(0)" onclick="$('#tplvar-var-form').trigger('reset');$('#tvar-id').val('');">Neue Variable</a></div>
    <form action="<%$PHPSELF%>" method="POST" class="jsonform" id="tplvar-var-form">
    <input type="hidden" name="epage" value="<%$epage%>">
    <input type="hidden" name="cmd" value="savever">
    <input type="hidden" name="id" id="tvar-id" value="">
            <label>Var.-Typ:</label>
            <select class="form-control" name="FORM[var_type]" id="var-type">
                <option value="editfield">Edit Field</option>
                <option value="htmledit">WYSIWYG Editor</option>
                <option value="script">HTML Script</option>                        
                <option value="select">Select Box</option>
                <option value="imgfile">Image File Upload</option>
               <!-- <option value="systpl">System Tempalte</option> -->
            </select>
            <label>Var.-Bezeichnung:</label>
            <span class="help-block">(z.B. Farbenauswahl)</span>
            <input type="text" class="form-control" id="tvar-var_name" name="FORM[var_name]" value="">
            <label>Var. Info Text:</label>
            <input type="text" class="form-control" id="tvar-var_desc" name="FORM[var_desc]" value="">
    
    <div class="varopt notshown" id="tplvar-opt-select">
        <label>Select Box Values:</label>
        <input type="text" class="form-control" id="tvaropt-select-values" name="FORMOPT[select][values]" value="">
        <div class="bg-info text-info">Werte getrennt durch "|"</div>
    </div>
    
    <div class="varopt notshown" id="tplvar-opt-imgfile">   
        <label>Width</label>
        <input type="text" class="form-control" id="tvaropt-imgfile-foto_width" name="FORMOPT[imgfile][foto_width]" value="">
        <label>Height</label>
        <input type="text" class="form-control" id="tvaropt-imgfile-foto_height" name="FORMOPT[imgfile][foto_height]" value="">
        <label>Resize Method</label>
        <select class="form-control" id="tvaropt-imgfile-foto_resize" name="FORMOPT[imgfile][foto_resize]" >            
            <option value="resize">resize</option>
            <option value="resizetofit">resizetofit</option>
            <option value="resizetofitpng">resizetofitpng</option>
            <option value="boxed">boxed</option>            
            <option value="crop">crop</option>
            <option value="none">none</option>
        </select>
        <label>Crop Position</label>
        <select class="form-control" id="tvaropt-imgfile-foto_resize" name="FORMOPT[imgfile][foto_gravity]" >
            <option value="center">center</option>
            <option value="north">north</option>
            <option value="south">south</option>
            <option value="west">west</option>
            <option value="east">east</option>
        </select>    
    </div>
    <div class="subright"><%$subbtn%></div>
    
    </form>
    </div>  
    <div class="col-md-6">
        <div id="tplvarslist"></div>
    </div>    
</div><!-- row -->

</div><!-- TAB 2 -->
</div>



<script>
$( "#var-type" ).change(function() {
   $('.varopt').hide();
   var ident = $(this).val();
   $('#tplvar-opt-'+ident).show();
});
function reloadvars() {
    simple_load('tplvarslist','<%$PHPSELF%>?epage=<%$epage%>&cmd=loadvars');
}
reloadvars();

function reloadtpls() {
    simple_load('tplist','<%$PHPSELF%>?epage=<%$epage%>&cmd=loadtpls');
}
reloadtpls();

<% if ($cmd=='edittpl' || $cmd=='ax_edittpl') %>
    $('#tpleditoredit').fadeIn(200);
    $('#tpleditor').hide();
    $('#tplist').html('');
<%/if%>
</script>