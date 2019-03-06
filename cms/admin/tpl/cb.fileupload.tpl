<div class="form-group">
    <label for="datei"><%$label%></label>
    <div class="input-group">
        <input type="text" name="" value="" class="form-control" readonly="" placeholder="Keine Datei ausgewählt">
        <input id="datei" type="file" name="<%$name%>" value="" class="xform-control" onchange="this.previousElementSibling.value = this.files[0].name">
        <span class="input-group-btn">
            <button class="btn btn-default" type="button">Durchsuchen...</button>
  
        </span>
    </div><!-- /input-group -->
</div><!-- /.form-group -->

