<form action="<%$PHPSELF%>" method="POST" class="jsonform" id="js-callback-form">
    <input type="hidden" value="<%$page%>" name="page">
    <input type="hidden" value="send_callback" name="cmd">
    <input type="hidden" value="" name="email">
    <input type="hidden" name="cont_matrix_id" value="<%$cont_matrix_id%>">
    <div class="form-group">
        <label class="sr-only" for="callback">Ihre Telefonnumer</label>
        <div class="input-group">
            <input name="FORM[telefon]" autocomplete="off" required="" type="text" value="" id="callback" placeholder="Ihre Telefonnummer" class="form-control">
            <span class="input-group-btn">
        <button class="btn btn-default" type="submit">Go!</button>
      </span>
        </div>    
    </div>
</form>
<div class="alert alert-success" style="display:none" id="js-callback-feedback">Wir werden Sie umgehend zurückrufen.</div>
<script>
    function callback_send() {
        $('#js-callback-form').hide();
        $('#js-callback-feedback').fadeIn();
    }
</script>