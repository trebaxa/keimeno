<div class="container" id="js-formsend">
  <h2><%$plginopt.title%></h2>
  <form action="<%$PHPSELF%>" class="jsonform-" method="post">
    <input type="hidden" name="cmd" value="formsend">
    <input type="hidden" name="page" value="<%$page%>">
    <input type="hidden" name="cont_matrix_id" value="<%$cont_matrix_id%>">
    <div class="row">
      <div class="form-group col-md-6">
        <label for="">Vorname</label>
        <input type="text" class="form-control" required="" value"" name="FORM[vorname]">
      </div>
      <div class="form-group col-md-6">
        <label for="">Nachname</label>
        <input type="text" class="form-control" required="" value"" name="FORM[nachname]">
      </div>
    </div>
    <div class="form-group">
        <label for="">E-Mail</label>
        <input type="email" class="form-control" required="" value"" name="FORM[email]">
      </div>
    <button class="btn btn-primary" type="submit">senden</button>
  </form>
</div>

<div class="container" style="margin:120px auto;display:none" id="js-formsend-answer">
  <h2>Vielen Dank Ihre Anmeldung!</h2>
</div>