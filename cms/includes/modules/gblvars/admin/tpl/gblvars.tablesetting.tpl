<td>
 <% if ($row.var_type=='switch') %>
  <div class="radio">
    <label>
        <input <% if ($row.var_value==1) %>checked<%/if%> type="radio" name="FORM[<%$row.var_name%>][var_value]" value="1" /> <%$row.var_settings.radio_value_1|sthsc%>
    </label>
    <label>    
        <input <% if ($row.var_value==0) %>checked<%/if%> type="radio" name="FORM[<%$row.var_name%>][var_value]" value="0" /> <%$row.var_settings.radio_value_2|sthsc%>
    </label>
  </div>
 <%elseif $row.var_type=='list'%>
    <select class="form-control custom-select" name="FORM[<%$row.var_name%>][var_value]">
        <% foreach from=$row.var_settings.list item=listvalue %>
            <option <% if ($row.var_value==$listvalue) %>selected<%/if%> value="<%$listvalue%>"><%$listvalue%></option>
        <%/foreach%>
    </select>
<%elseif $row.var_type=='password'%>
    <input class="form-control" value="<%$row.var_value|hsc%>" type="password" name="FORM[<%$row.var_name%>][var_value]" />                   
<%elseif $row.var_type=='date'%>
    <input class="form-control" value="<%$row.var_value|hsc%>" type="date" name="FORM[<%$row.var_name%>][var_value]" />                   
<%elseif $row.var_type=='mail'%>
    <input class="form-control" value="<%$row.var_value|hsc%>" type="email" name="FORM[<%$row.var_name%>][var_value]" />    
 <%else%>
    <input class="form-control" value="<%$row.var_value|hsc%>" type="text" name="FORM[<%$row.var_name%>][var_value]" />
 <%/if%>
</td>  