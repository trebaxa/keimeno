   <div class="form-group"> 
    <label>Template</label> 
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
  </div>
  <div class="form-group">  
    <label>Empfänger Email</label>
    <input type="email" class="form-control" name="PLUGFORM[email]" value="<%$WEBSITE.node.tm_plugform.email%>" required>
  </div>  
