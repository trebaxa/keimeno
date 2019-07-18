<div class="form-group">
    <label>Stil-Vorlage:</label>
    <div class="form-group">
        <select class="form-control custom-select" id="js-flextplid" name="PLUGFORM[flxtpl]">
            <% foreach from=$FLEXTEMP.flextpl.tpls item=row %>
                <option <% if ($FLEXTEMP.plugopt.flxtpl==$row.id) %>selected<%/if%> value="<%$row.id%>"><%$row.t_name%></option>
            <%/foreach%>
        </select>
    </div>
    
</div>




<script>
    <% if ($FLEXTEMP.plugopt.flxtpl>0) %>    
        reload_dataset(0,0);
    <%/if%>
</script>