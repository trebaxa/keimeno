<% if (count($PAGEOBJ.t_breadcrumb_arr)>1)%>
<div class="breadcrumb-wrapper">
  <div class="container">
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
        <%*<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="/index.html"><span itemprop="name">Startseite</span></a><meta itemprop="position" content="0" /></li>*%>
        <% foreach from=$PAGEOBJ.t_breadcrumb_arr item=bread name=breadloop %>
            <% if ($bread.id!=$page) %>
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="<%$bread.label|hsc%>" href="<%$bread.link%>"><span itemprop="name"><%$bread.label%></span></a><meta itemprop="position" content="<%$smarty.foreach.breadloop.iteration%>" /></li>
            <%else%>    
                <li class="active"><%$bread.label%></li>
            <%/if%>
        <%/foreach%>
    </ol>
  </div>  
</div>
<%/if%>