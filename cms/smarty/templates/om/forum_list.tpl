<% if ($aktion=='') %> 
<% if (count($ftable)>0) %>
<table class="tab_std" width="100%">
    <% foreach from=$ftable item=fgroup %>
    <tr class="header" >
        <td></td>
        <td><% $fgroup.fg_name %></td>   
        <td>Themen</td>
        <td>BeitrÃ¤ge</td>    
        <td>letzter Beitrag</td> 
    </tr>
    <% foreach from=$fgroup.foren item=forum %> <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %>
    <% else %> <% assign var=sclass value="row1" %> <% /if %>
    <tr class="<%$sclass%>">
        <td width="26">
         <% if ($forum.hastodaythread==true) %>
            <img src="<%$forum_path%>images/forumnew.png" >
         <%else%>   
            <img src="<%$forum_path%>images/forum.png" >
         <%/if%>
        </td>
        <td><a href="<%$forum.forumlink%>"><% $forum.fn_name %></a><br>
        <span class="small"><%$forum.fn_shortdesc%></span></td>        
        <td><%$forum.THEMECOUNT%></td>        
        <td><%$forum.THREADCOUNT%></td>
        <td>
        <% if ($forum.lastthread.f_time>0) %>
        <%$forum.lastthread.datetime%>
        <br><span class="small">von <b><%$forum.lastthread.user.username%></b></span>
        <%else%>
        -
        <%/if%></td>
    </tr>
    <%/foreach%> <%/foreach%>
</table>
<%else%><div class="infobox">
    Keine Gruppen gefunden.</div>
<%/if%> 
<%/if%>
