<% if ($label!="") %>
<div class="row mb-sm">
 <div class="col-md-6">    
    <label><%$label%>:</label>
 </div>   
 <div class="col-md-6 text-right">
<%/if%>
<div class="switch-field">
    <input type="radio"  name="<%$name%>" id="bs-<%$name|md5%>" value="1" autocomplete="off" <% if ($value==1) %>checked<%/if%>/>
    <label for="bs-<%$name|md5%>">{LBLA_YES}</label>
    <input type="radio" name="<%$name%>" id="bs-<%$name|md5%>2" value="0" autocomplete="off" <% if ($value==0) %>checked<%/if%>/>
    <label for="bs-<%$name|md5%>2">{LBL_NO}</label>
</div>
<% if ($label!="") %>
 </div>
</div>
<%/if%>
