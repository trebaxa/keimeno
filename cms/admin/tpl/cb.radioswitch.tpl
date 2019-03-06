<% if ($label!="") %>
<div class="row mb-sm">
 <div class="col-md-6">    
    <label><%$label%>:</label>
 </div>   
 <div class="col-md-6 text-right">
<%/if%>
<div class="btn-group" data-toggle="buttons">
  <label class="btn btn-primary btn-sm<% if ($value==1) %> active<%/if%>">
    <input <% if ($value==1) %>checked<%/if%> value="1" type="radio" name="<%$name%>" id="bs-<%$name|md5%>" autocomplete="off"> {LBLA_YES}
  </label>
  <label class="btn btn-primary btn-sm<% if ($value==0) %> active<%/if%>">
    <input <% if ($value==0) %>checked<%/if%> value="0" type="radio" name="<%$name%>" id="bs-<%$name|md5%>2" autocomplete="off"> {LBL_NO}
  </label> 
</div>
<% if ($label!="") %>
 </div>
</div>
<%/if%>