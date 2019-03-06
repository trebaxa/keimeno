<div class="row">
    <div class="col-md-12">
<% if ($pinitem.image_exists==true) %>
<div class="post-image">
   <a href="<%$pinitem.detail_link %>"><img class="zoomin img-thumbnail" src="<%$PATH_CMS%>file_data/tcblog/<%$pinitem.b_image%>" alt="<% $pinitem.title|sthsc %>"></img></a>
</div>
<%/if%>
<div class="row">
<div class="col-md-3 blog-post">
    <div class="post-meta">
        <date class="post-date">
            <span><% $pinitem.ndate|date_format:"%d" %></span>
            <% $pinitem.ndate|date_format:"%b %Y" %>
        </date>
        <% if ($pinitem.perm.edit==true) %>
    <a href="<% $PHPSELF %>?page=<% $page %>&id=<% $pinitem.DID %>&aktion=pininsertshow">
    <img src="<% $PATH_CMS %>images/opt_edit.gif" ></a>
<% /if %>
<% if ($pinitem.perm.del==true) %>
    <a onClick="return confirm('Sind Sie sicher?')" href="<% $PHPSELF %>?page=<% $page %>&id=<% $pinitem.DID %>&aktion=a_delpin">
    <img src="<% $PATH_CMS %>images/opt_waste.gif" ></a>
<% /if %>
    </div>

</div>

<div class="col-md-9">
    <h3 class="post-title"><% $pinitem.title %></h3>
    <div class="post-meta">Geschrieben von <a class="blog-tag-a" href="<% $SCRIPT_URI %>?cmd=load_by_user&user=<% $pinitem.username %>"><% $pinitem.username %></a> | Tags: <% foreach from=$pinitem.tags item=tag %><a class="blog-tag-a" href="<% $SCRIPT_URI %>?cmd=load_by_tag&tag=<% $tag %>" title="Blog Tags <%$tag%>"><%$tag%></a><%/foreach%></div>
    <div class="post-content"><% $pinitem.content|st|truncate:360 %> <!--<br><a href="<% $SCRIPT_URI %>?cmd=load_blog_item&id=<% $pinitem.DID %>">mehr lesen</a>-->
        <br><a href="<%$pinitem.detail_link %>">mehr lesen</a>
    </div>
</div>
</div>

</div>
</div>