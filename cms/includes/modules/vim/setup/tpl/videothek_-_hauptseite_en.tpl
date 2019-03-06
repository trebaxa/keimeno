<style type="text/css">
@import "<%$PATH_CMS%>includes/modules/vim/images/videothek.css";
</style>
<script  type="text/javascript" src="<% $PATH_CMS %>includes/modules/vim/images/videothek.js"></script>

<h1>Videothek</h1>
<div style="margin-bottom:10px;">
<div style="float:left">
    Kategorie:
     <select class="form-control" onChange="location.href=this.options[this.selectedIndex].value">
     <option <% if (0==$GET.videocid) %>selected<%/if%> value="0">- nicht zugeordnet -</option>
     <% foreach from=$VIM.cat_selectbox_arr key=catid item=catname %>
       <option <% if ($catid==$GET.videocid) %>selected<%/if%> value="<%$PHPSELF%>?page=<%$page%>&cmd=load_videos_fe&videocid=<%$catid%>"><% $catname %></option>
      <%/foreach%>
      </Select>
 </div>
 <div id="video-searchbox">
 <form role="form" id="video-list-form" action="<% $PHPSELF %>" method="GET">
     <input type="hidden" name="cmd" value="search_videos_fe">
     <input type="hidden" name="page" value="<%$page%>">
     <input autocomplete="off" class="search-input-field float-left" class="form-control" type="text" name="sword">
     <input class="search-button float-left" type="submit" value="">
 </form>
</div>


 
<div class="clearer"></div>
</div>

<div id="video-list">
<% include file="videothek_videoliste.tpl" %>
</div>
