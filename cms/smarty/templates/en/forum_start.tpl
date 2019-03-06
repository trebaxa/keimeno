<link rel="stylesheet" href="<%$forum_path%>js/sceditor/minified/themes/default.min.css" type="text/css" media="all" />
<script src="<%$forum_path%>js/sceditor/minified/jquery.sceditor.bbcode.min.js"></script>
<link rel="stylesheet" type="text/css" href="<%$forum_path%>css/layout.css">
<style type="text/css">   @import url(<%$forum_path%>js/lytebox/lytebox.css);</style>
<script src="<%$forum_path%>js/lytebox/lytebox.js"></script>
<div id="forum" class="container">
  <div class="row mb-lg">
    <div class="col-md-6">
      <h2>keimeno.de Support Forum</h2>
      <small>Support Forum für das Keimeno CMS System. Wer nicht fragt, bekommt auch keine Antworten! ;-)</small>
    </div>
    <div class="col-md-6 text-right">
      <form action="" class="form-inline" id="js-forum-search-form">
        <div class="form-group">
          <div class="input-group">
            <input type="text" name="FORM[sword]" class="form-control">
            <div class="input-group-addon"><a href="#" id="js-forum-search"><i class="fa fa-search"></i></a></div>
          </div>
        </div>
      </form>
    </div>
  </div>

 <% if ($forumobj.fg_name!="") %>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item"><a itemprop="item" href="<% $gbl_config.forum_start_url %>" title="Forum"><span itemprop="name">Forum</span></a><meta itemprop="position" content="1" /></li>
    
      <% if ($forumobj.fg_name!="") %>
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item"><span itemprop="item"><span itemprop="name"><%$forumobj.fg_name%></span></span><meta itemprop="position" content="2" /></li>
      <%/if%>
      <% if ($forumobj.fn_name!="") %>
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item"><a itemprop="item" name="topanker" href="<%$forumobj.forumlink%>" title="<%$forumobj.fn_name|sthsc%>"><span itemprop="name"><%$forumobj.fn_name%></span></a><meta itemprop="position" content="3" /></li>
      <%/if%>  
      <% if ($forumtheme.id>0) %>
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item"><a itemprop="item" href="<%$forumtheme.themelink%>" title="<%$forumtheme.t_name|sthsc%>"><span itemprop="name"><%$forumtheme.t_name%></span></a><meta itemprop="position" content="4" /></li>
      <%/if%>
    </ol>
  <%/if%>  
  
      
  
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#forumtab" onclick="simple_load('forumtab','<%$PHPSELF%>?cmd=load_forum_js&page=<%$page%>');" aria-controls="home" role="tab" data-toggle="tab">Forum</a></li>
    <li role="presentation"><a href="#forumsearch" aria-controls="profile" role="tab" data-toggle="tab">Suche</a></li>
    <li role="presentation"><a href="#foruminfo" aria-controls="profile" role="tab" data-toggle="tab">Forumregeln</a></li>
  </ul>
  <div class="tab-content">
  
    <div role="tabpanel" class="tab-pane active" id="forumtab">
     
      <% include file="forum_list.tpl" %>
      <% include file="forum_themes.tpl" %>
      <% include file="forum_threads.tpl" %>
      <% include file="forum_editor.tpl" %>
    </div>
    
    <div role="tabpanel" class="tab-pane" id="forumsearch">
      <form action="<%$PHPSELF%>" method="POST" id="forumdetailsearch">
        <input type="hidden" name="cmd" value="forumsearchdetail">
        <input type="hidden" name="page" value="<%$page%>">
        <div class="form-group">
          <label for="">Suchbegriff</label>
          <input type="text" class="form-control" name="FORM[sword]">
        </div>
        
        <div class="forum-group">
          <label for="">Forum</label>
          <select name="FORM[fid]" id="" class="form-control">
            <option value="0">- alle -</option>
             <% foreach from=$FORUM.fgroups item=fgroup %>
               <optgroup label="<%$fgroup.fg_name%>">
               <% foreach from=$fgroup.foren item=forum %>
                 <option value="<%$forum.id%>"><%$forum.fn_name%></option>
               <%/foreach%>
               </optgroup>
             <%/foreach%>
          </select>
        </div>
        
        <button class="btn btn-primary mt-sm" type="submit">suchen</button>
      </form>
      <div id="js-searchresultf"></div>
    </div>
    
    <div role="tabpanel" class="tab-pane" id="foruminfo">
      <h3>Forum Information</h3>
      <ul>
        <li><a href="{URL_TPL_100165}" title="Diskussionsrichtlinien">Diskussionsrichtlinien</a></li>
        <li><a href="{URL_TPL_100166}" title="Nutzungsbedingungen">Nutzungsbedingungen</a></li>
      </ul>
    </div>
    
  
  </div>
  
</div>