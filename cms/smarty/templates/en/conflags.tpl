<% if ($useflags==true) %>
<% foreach from=$content_flags item=myflag name=loop %>
<a href="<% $myflag.link %>">
<img <% if ($clid==$myflag.id) %> style="border:3px #FF0000;" <% /if %> title="<% $myflag.post_lang %>" alt="<% $myflag.post_lang %>" src="<% $PATH_CMS %>images/<% $myflag.bild %>" >
</a>&nbsp;
<% /foreach %>
<input type="hidden" name="FORM_CON[lang_id]" value="<% $clid %>">

<% else %>

{LBL_LANGUAGE}:<select class="form-control" name="FORM_CON[lang_id]">
<% foreach from=$content_flags item=myflag name=loop %>
<option value="<% $myflag.id %>"><% $myflag.post_lang %></option>
<% /foreach %>
</select>

<% /if %>