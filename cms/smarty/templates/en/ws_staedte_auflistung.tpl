<form action="<%$PHPSELF%>" class="form-inline">
  <input type="hidden" name="cmd" value="load_workshops">
  <input type="hidden" name="page" value="<%$page%>">
  <div class="form-group">
    <label for="bs-city">Stadt:</label>
    <select id="bs-city" name="city" class="form-control">
    <% foreach from=$WORKSHOP.cities item=row %>
      <option value="<%$row.id%>"><%$row.c_city%></option>
    <%/foreach%>
    </select>
  </div>
  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
  
</form>
