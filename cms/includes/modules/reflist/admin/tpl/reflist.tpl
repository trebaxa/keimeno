<link rel="stylesheet" href="../includes/modules/reflist/admin/css/style.css" type="text/css"/>

<div class="page-header"><h1>Referenz Links</h1></div>

<div class="btn-group">
    <a class="btn btn-secondary" href="javascript:void(0);" onclick="$('#reflinkid').val('0');$('#reflinkedit').modal('show')">Neu anlegen</a>
</div>

<div id="reflinks">
    <% include file="reflist.table.tpl" %>
</div>



<!-- Modal -->
<div class="modal fade" id="reflinkedit" tabindex="-1" role="dialog" aria-labelledby="reflinkeditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form id="reflistform" method="POST" action="<%$PHPSELF%>" class="jsonform">
      <div class="modal-header">
        <h5 class="modal-title" id="reflinkeditLabel">Configuration</h5>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
      </div>
      <div class="modal-body">

        <div class="form-group">
            <label>Firma</label>
            <input type="text" required value="" class="r_firma form-control" name="FORM[r_firma]">
        </div>     
        <div class="form-group">
            <label>Strasse</label>
            <input type="text" value="" class="r_street form-control" name="FORM[r_street]">
        </div>     
        <div class="form-group">
            <label>PLZ</label>
            <input type="text" value="" class="r_plz form-control" name="FORM[r_plz]">
        </div>    
        <div class="form-group">
            <label>Ort</label>
            <input type="text" value="" class="r_city form-control" name="FORM[r_city]">
        </div>     
        <div class="form-group">
            <label>Tel.</label>
            <input type="text" value="" class="r_tel form-control" name="FORM[r_tel]">
        </div>     
        <div class="form-group">
            <label>Homepage</label>
            <input type="text" value="" class="r_url form-control" name="FORM[r_url]">
        </div>     
        <div class="form-group">
            <label>Text</label>
            <textarea name="FORM[r_text]" class="r_text form-control"></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      <input type="hidden" name="section" value="<%$REQUEST.section%>">
  <input type="hidden" name="cmd" value="save_reflink">
  <input type="hidden" id="reflinkid" class="id" name="id" value="">
  <input type="hidden" name="epage" value="<%$epage%>">
      </form>
    </div>
  </div>
</div>

<script>
function reload_reflinks() {
    simple_load('reflinks','<%$PHPSELF%>?epage=<%$epage%>&cmd=reload_reflinks');
}
</script>