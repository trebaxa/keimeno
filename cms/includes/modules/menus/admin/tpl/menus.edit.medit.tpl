<div class="form-group ">
    <label>Title</label>
    <input type="text" class="form-control" required="" name="FORM[mm_title]" value="<%$MENUS.item.mm_title|sthsc%>" placeholder="About" />
</div>

<div class="form-group ">
    <label>URL</label>
    <input type="text" class="form-control" required="" name="FORM[mm_url]" value="<%$MENUS.item.mm_url|sthsc%>" placeholder="/index.html#about" />
</div>

<div class="form-group ">
    <label>Attributes</label>
    <input type="text" class="form-control" name="FORM[mm_attr]" value="<%$MENUS.item.mm_attr|sthsc%>" placeholder='data-hash="#start"' />
</div>

<div class="form-group ">
    <label>Classes</label>
    <input type="text" class="form-control" name="FORM[mm_class]" value="<%$MENUS.item.mm_class|sthsc%>" placeholder="" />
</div>

<div class="form-group ">
    <label>Position</label>
    <select class="form-control custom-select" name="FORM[mm_parent]">
       <option value="0">Root</option>
        <% foreach from=$MENUS.menu_selectox item=opt %>
            <%$opt%> 
        <%/foreach%>
    </select>
</div>

<input type="hidden" value="<%$MENUS.item.id%>" name="itemid" />
<% if ($MENUS.item.id>0) %>
    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> {LA_SAVE}</button>
<%else%>
    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> {LA_ADD}</button>
<%/if%>              