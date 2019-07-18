<i class="fa fa-times fa-lg searchclose" onclick="$('#websearchresult').fadeOut()"><!----></i>
<% if (count($ADMIN.searchresult)>0) %>
<% foreach from=$ADMIN.searchresult item=group  %>
    <div class="resultgroup">
        <label><%$group.label%></label>
        <ul>
        <% foreach from=$group.items item=row %>
            <li><a title="{LBLA_EDIT}" href="<%$row.edit_link%>"><%$row.description%></a></li>		
            <%/foreach%>
        </ul>
        <div class="clearfix"></div>
    </div>
<%/foreach%>
<%else%>
<div class="alert alert-info">{LNL_NOSEARCHRESULT}</div>
<%/if%>