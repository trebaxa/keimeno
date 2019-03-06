<link rel="stylesheet" type="text/css" href="<%$forum_path%>css/layout.css">
<link href="<%$forum_path%>bbcode/editor.css" rel="Stylesheet" type="text/css" />
<style type="text/css">   @import url(<%$PATH_CMS%>js/lytebox/lytebox.css);</style>
<script type="text/javascript" src="<%$PATH_CMS%>js/lytebox/lytebox.js"></script>
<div id="forum">
<div class="bread">Sie sind hier: <a href="{URL_TPL_40}">Foren-Überischt</a>
<% if ($forumobj.id>0) %>
&raquo; <a name="topanker" href="<%$forumobj.forumlink%>"><%$forumobj.fn_name%></a>
<%/if%>
<% if ($forumtheme.id>0) %>
&raquo; <a href="<%$forumtheme.themelink%>"><%$forumtheme.t_name%></a>
<%/if%>
</div>

<% include file="forum_latest_threads.tpl" %>
<% include file="forum_list.tpl" %>
<% include file="forum_themes.tpl" %>
<% include file="forum_threads.tpl" %>
<% include file="forum_editor.tpl" %>
</div>
