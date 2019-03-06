<style type="text/css">@import url("<%$PATH_CMS%>includes/modules/tcblog/css/style.css");</style>

<% if (count($themes)>1) %>
<div class="btn-group">
<% foreach from=$themes item=theme name=mt %>
 <a class="btn btn-group <% $theme.class %>" href="<% $theme.link %>"><% $theme.theme %></a>
<% /foreach %>
</div>
<% /if %>

<% if ($GET.year>0) %><h3><%$BLOG.daterange%></h3><%/if%>
<% if ($gblitem.perm.add==true) %>
<div class="row">
    <div class="col-md-12 text-right">
        <a class="btn btn-default" href="javascript:void(0)" class="cmsbtnhref" onclick="dc_show('tcblog-ins',900)">Beitrag hinzufügen</a>
    </div>
</div>
<% /if %>

<div class="row">
    <div class="col-md-9">
        <% if ($cmd=='load_blog_item') %>
 	        <% include file="pin_detail.tpl" %>
        <%else%>
            <% if (count($BLOG.items)>0) %>
            <article class="row">
                <% foreach from=$BLOG.items item=pinitem name=mt %>
                    <% include file="pin_table.tpl" %>
                <% /foreach %>
            </article> 
            <%else%>
                Keine Einträge
            <% /if %>
        <%/if%>    
    </div>

<div class="blog_nav col-md-3">
<h3>Artikel Tags</h3>
<div class="blog-tags"><% foreach from=$BLOG.tags item=tag %><a href="<% $SCRIPT_URI %>?cmd=load_by_tag&tag=<% $tag.tag %>" title="Blog Tags <%$tag.tag%>"><span class="badge pull-right"><%$tag.count%></span><%$tag.tag%></a><%/foreach%></div>
<h3>Letzte Artikel</h3>
<% foreach from=$BLOG.latest_items_by_blog item=row %>
    <div>
        <a href="<% $SCRIPT_URI %>?cmd=load_blog_item&id=<% $row.DID %>"><%$row.title%></a><br>
        <span class="gray">Beitrag vom <% $row.date %></span>
    </div>
<% /foreach %>

<h3>Date Archives</h3>
<% foreach from=$BLOG.yearfilter item=row key=year %>
<ul id="blog-nav">
<li class="blog-nav-item">
<span class="blog-nav-year"><%$year%></span>
    <ul class="nav nav-pills nav-stacked">
    <% foreach from=$row item=month key=numm %>
    <% if ($month.count>0) %>
        <li class="blog-month-"><a href="<% $SCRIPT_URI %>?month=<%$numm%>&year=<% $year %>&cmd=load_ym"><%$month.label%><span class="badge pull-right"><%$month.count%></span></a></li>
    <%/if%>    
    <%/foreach%>
    </ul>
</li>
<%/foreach%>
</ul>
</div>
</div>



<div style="display:none" id="tcblog-ins" class="divframe">
    <% include file="pin_insertform.tpl" %>
</div>