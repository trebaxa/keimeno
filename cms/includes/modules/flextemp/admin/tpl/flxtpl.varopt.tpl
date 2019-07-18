<div class="varopt notshown" id="<%$ident%>-tplvar-opt-seli">
    <label>Selectbox Values:</label>
    <input type="text" class="form-control" placeholder="1;blau|2;grau|3;rot" id="<%$ident%>-tvaropt-seli-values" name="FORMOPT[seli][values]" value="<%$FLEXTEMP.flxvaredit.v_opt.seli.values|sthsc%>">
    <div class="alert-info alert mt-lg">Werte getrennt durch ";" und "|". z.B: 1;blau|2;grau|3;rot</div>
</div>

<div class="varopt notshown" id="<%$ident%>-tplvar-opt-sel">
    <label>Selectbox Values:</label>
    <input type="text" class="form-control" id="<%$ident%>-tvaropt-sel-values" name="FORMOPT[sel][values]" value="<%$FLEXTEMP.flxvaredit.v_opt.sel.values|sthsc%>">
    <div class="alert-info alert mt-lg">Werte getrennt durch "|"</div>
</div>

<div class="varopt notshown" id="<%$ident%>-tplvar-opt-img">   
    <label>Width</label>
    <input type="text" class="form-control js-num-field" id="<%$ident%>-tvaropt-img-foto_width" name="FORMOPT[img][foto_width]" value="<%$FLEXTEMP.flxvaredit.v_opt.img.foto_width|sthsc|intval%>">
    <label>Height</label>
    <input type="text" class="form-control js-num-field" id="<%$ident%>-tvaropt-img-foto_height" name="FORMOPT[img][foto_height]" value="<%$FLEXTEMP.flxvaredit.v_opt.img.foto_height|sthsc|intval%>">
    <label>Resize Method</label>
    <select class="form-control custom-select" id="<%$ident%>-tvaropt-img-foto_resize" name="FORMOPT[img][foto_resize]" >            
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_resize=='resize')%>selected<%/if%> value="resize">resize</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_resize=='resizetofit')%>selected<%/if%> value="resizetofit">resizetofit</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_resize=='resizetofitpng')%>selected<%/if%> value="resizetofitpng">resizetofitpng</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_resize=='boxed')%>selected<%/if%> value="boxed">boxed</option>            
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_resize=='crop')%>selected<%/if%> value="crop">crop</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_resize=='none' || $FLEXTEMP.flxvaredit.v_opt.img.foto_resize=='')%>selected<%/if%> value="none">none</option>
    </select>
    <label>Crop Position</label>
    <select class="form-control custom-select" id="<%$ident%>-tvaropt-img-foto_resize" name="FORMOPT[img][foto_gravity]" >
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_gravity=='Center')%>selected<%/if%> value="Center">Center</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_gravity=='North')%>selected<%/if%> value="North">North</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_gravity=='NorthEast')%>selected<%/if%> value="NorthEast">NorthEast</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_gravity=='NorthWest')%>selected<%/if%> value="NorthWest">NorthWest</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_gravity=='South')%>selected<%/if%> value="South">South</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_gravity=='SouthEast')%>selected<%/if%> value="SouthEast">SouthEast</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_gravity=='SouthWest')%>selected<%/if%> value="SouthWest">SouthWest</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_gravity=='West')%>selected<%/if%> value="West">West</option>
        <option <% if ($FLEXTEMP.flxvaredit.v_opt.img.foto_gravity=='East')%>selected<%/if%> value="East">East</option>
    </select>    
</div>

<script>
$( document ).ready(function() {
    $('.varopt').hide();
    $( "#<%$ident%>" ).change(function() {
       $('.varopt').hide();
       var ident = $(this).val();
       $('#<%$ident%>-tplvar-opt-'+ident).show();
    });
    $( "#<%$ident%>" ).trigger('change');   
});
</script>