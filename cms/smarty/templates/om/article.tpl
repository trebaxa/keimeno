<div id="left_nav">
    <% include file="articles_tree.tpl" %>
</div>

<div style="float:left;height:auto;width:730px;padding-left:10px;">
<% if ($aktion=='showarticle') %>
<div style="text-align:right;float:left;width:100%;">
<% if ($customer.PERMOD.articles.edit==TRUE || $article_obj.a_kid==$customer.kid ) %>
         <a title="bearbeiten" href="<%$PHPSELF%>?gid=<%$article_obj.a_group_id%>&artid=<%$article_obj.AID%>&aktion=edit&page=400">
         <img alt="bearbeiten" src="<%$PATH_CMS%>images/page_white_edit.png" title="bearbeiten" ></a>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?gid=<%$article_obj.a_group_id%>&id=<%$article_obj.AID%>&page=400&aktion=a_delart">
<img src="<%$PATH_CMS%>images/page_delete.png" title="lÃ¶schen"  alt=""></a>
<%/if%> 
<% if ($customer.PERMOD.articles.edit==TRUE ) %>
<a href="<%$PHPSELF%>?orgaktion=showarticle&gid=<%$article_obj.a_group_id%>&aktion=a_approve&value=<% if ($article_obj.a_approved==1) %>0<%else%>1<%/if%>&id=<%$article_obj.AID%>&page=400">
<img title="<% if ($article_obj.a_approved!=1) %>nicht <%/if%>verÃ¶ffentlicht" src="<%$PATH_CMS%>images/page_<% if ($article_obj.a_approved!=1) %>not<%/if%>visible.png"  alt=""></a>
<%/if%> 
</div>
<h1><%$article_obj.ac_title%></h1>
  <style type="text/css">
   @import url(<% $PATH_CMS %>js/images/milkbox/milkbox.css);
</style>
    <script type="text/javascript" src="<% $PATH_CMS %>js/mootools-1.2-more.js"></script> 
    <script type="text/javascript" src="<% $PATH_CMS %>js/milkbox.js"></script> 

<a href="<% $article_obj.icon_fullsize %>" rel="milkbox:fullsize" title="Grossansicht <%$article_obj.ac_title|hsc%>"  >    
<img alt="<%$article_obj.ac_title|hsc%>" src="<% $article_obj.thumbnail %>"  style="float:right">
</a>

<% $article_obj.ac_content %>
<% if (count($article_obj.filelist) > 0)%>
<fieldset>
    <legend>Anh&auml;nge:</legend>
     
    <table class="tab_std">
    <% foreach from=$article_obj.filelist item=afile %>
        <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
            <tr class="<%$sclass%>">
                <td><%$afile.uploadtime%></td>
                <td><a title="<%$afile.f_file%>" target="_afile" href="<%$PATH_CMS%><%$ARTICLES_PATH%><%$afile.f_file%>"><%$afile.f_file%></a></td>
                <td><%$afile.humanfilesize%></td>
                <td>
                <% if ($afile.thumbnail!="") %>
                    <img src=".<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" ></td>
                    <%else%>
                    <%$afile.f_ext%>
                <%/if%>
               
            </tr>
            <% /foreach %>
        </table>        
        
</fieldset>
<% /if %>
<%/if%>

<% if ($aktion=='articles') %>
<h1>Artikel <%$treeleaf.g_title%></h1>
<% if ($customer.kid>0 && $customer.PERMOD.articles.add==true) %>
<br><a href="<%$PHPSELF%>?page=<%$page%>&aktion=edit&artid=0">Neuen Artikel anlegen</a><br><br>
<%/if%>

 <% if ($articles.count>0) %>
 <div style="width:100%;text-align:right;margin-bottom:10px;">Sortierung:
 <select onChange="location.href=this.options[this.selectedIndex].value">>
 <option <% if ($GET.order=="a_time") %>selected<%/if%> value="<%$PHPSELF%>?gid=<%$GET.gid%>&aktion=<%$aktion%>&page=<%$page%>&order=a_time&dc=<%$articles.flipped_dc%>">Artikel-Datum</option>
  <option <% if ($GET.order=="id") %>selected<%/if%> value="<%$PHPSELF%>?gid=<%$GET.gid%>&aktion=<%$aktion%>&page=<%$page%>&order=id&dc=<%$articles.flipped_dc%>">Titel</option>
   <option <% if ($GET.order=="a_kid") %>selected<%/if%> value="<%$PHPSELF%>?gid=<%$GET.gid%>&aktion=<%$aktion%>&page=<%$page%>&order=a_kid&dc=<%$articles.flipped_dc%>">Autor</option>
 </select>
 </div>
<table class="tab_std" >
 
        <% foreach from=$articles.articles_approved item=looparticle %>
        <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
        <% assign var=cuperm value=$customer %>
<% include file="article_tr.tpl"%>       
            <% /foreach %>
     </table>
<% if ($customer.PERMOD.articles.edit==true) %>
<% if (count($articles.articles_notapproved)>0) %>
<br>
<h3>Nicht verÃ¶ffentlichte Aritkel</h3>
<table class="tab_std" >
    <tr class="trheader">
     <td><a href="<%$PHPSELF%>?aktion=<%$aktion%>&page=<%$page%>&order=a_time&dc=<%$articles.flipped_dc%>">Artikel-Datum</a></td>
     <td><a href="<%$PHPSELF%>?aktion=<%$aktion%>&page=<%$page%>&order=id&dc=<%$articles.flipped_dc%>">Artikel</a></td>
     <td><a href="<%$PHPSELF%>?aktion=<%$aktion%>&page=<%$page%>&order=a_kid&dc=<%$articles.flipped_dc%>">Author</a></td>
     <td></td>
    </tr> 
     <% foreach from=$articles.articles_notapproved item=looparticle %>     
     <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
     <% assign var=cuperm value=$customer %>
<% include file="article_tr.tpl"%>       
            <% /foreach %>
              </table>
            <%/if%>
                 
<% /if %>
          
<%else%>        
<div class="infobox">Es liegen keine Artikel vor.</div>
<%/if%> 
<%/if%>



<% if ($aktion=='edit') %>
<h1>Artikel <% if ($artid>0) %> - <% $article_obj.ac_title %><%/if%></h1>
<form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
<input type="hidden" name="artid" value="<%$artid%>">
<input type="hidden" name="aktion" value="save">
<input type="hidden" name="page" value="<%$page%>">
<input type="hidden" name="FORMARTLANG[ac_langid]" value="<%$uselang%>">
<input type="hidden" name="uselang" value="<%$uselang%>">

<% if ($artid==0) %>
<h3>Anlegen</h3>
<table  class="tab_std" width="600">
<tr>
    <td>Beschreibender Titel:</td>
    <td><input type="text" name="FORMART[a_title]" value="<%$article_obj.a_title%>"></td>
</tr>
</table>
<%/if%>

<% if ($artid>0) %>
<h3>Artikel bearbeiten</h3>
<div style="text-align:right;float:left;width:100%;">
<a title="{LBL_SHOW}" href="<%$article_obj.link%>"><img title="{LBL_SHOW}" alt="{LBL_SHOW}" src="<%$PATH_CMS%>images/page_view.png" ></a>
</div>
<table class="tab_std">
<tr><td>Sprache:</td><td>
<select onChange="location.href=this.options[this.selectedIndex].value">
<% foreach from=$language_table item=lang %>
 <option <% if ($uselang==$lang.id) %>selected <%/if%>value="<%$PHPSELF%>?page=<%$page%>&aktion=<%$aktion%>&artid=<%$artid%>&uselang=<%$lang.id%>"><%$lang.post_lang%></option>
<%/foreach%>
</select>
</td></tr>
<tr><td>Eingestellt am:</td><td><input maxlength="10" size="11" type="text" value="<%$article_obj.date%>" name="FORMART[a_date]">dd.mm.YYYY</td></tr>
<tr>
    <td>Thema:</td>
    <td><%$POBJ.treeselectart%></td>
</tr>
<tr>
    <td>Titel:</td>
    <td><input maxlength="255" size="21" type="text" value="<%$article_obj.ac_title%>" name="FORMARTLANG[ac_title]"></td>
</tr>
<tr><td>Autor:</td><td>
<%$article_obj.username %>
</td></tr>
<tr><td>Themen Icon:
<% if ($article_obj.a_icon!="") %><br><img src="<% $article_obj.thumbnail %>" ><%/if%>
</td>
<td><input type="file" name="aicon"><input type="image" src="<%$PATH_CMS%>images/disk.png" class="subimg" title="speichern">
<% if ($article_obj.a_icon!="") %><br>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?gid=<%$article_obj.a_group_id%>&artid=<%$article_obj.AID%>&page=<%$page%>&aktion=a_delicon">
<img src="<%$PATH_CMS%>images/page_delete.png" title="lÃ¶schen"  alt=""></a>
<%/if%>
</td>
</tr>
<tr>
    <td>Datei Anhang:</td>
    <td><input type="file" name="datei"><input type="image" src="<%$PATH_CMS%>images/disk.png" class="subimg" title="speichern">
   </td>
</tr>
<% if (count($article_obj.filelist) > 0)%>
<tr>
    <td>Anh&auml;nge:</td>
    <td>    
    <table class="tab_std">
    <% foreach from=$article_obj.filelist item=afile %>
        <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
            <tr class="<%$sclass%>">
                <td><%$afile.uploadtime%></td>
                <td><a title="<%$afile.f_file%>" target="_afile" href="./<%$ARTICLES_PATH%><%$afile.f_file%>"><%$afile.f_file%></a></td>
                <td><%$afile.humanfilesize%></td>
                <td>
                <% if ($afile.thumbnail!="") %>
                    <img src=".<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" ></td>
                    <%else%>
                    <%$afile.f_ext%>
                <%/if%>
                <td class="tdright">
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?gid=<%$article_obj.a_group_id%>&artid=<%$article_obj.AID%>&id=<%$afile.id%>&page=400&aktion=a_delfile">
<img src="<%$PATH_CMS%>images/page_delete.png" title="lÃ¶schen"  alt="lÃ¶schen"></a>                
                </td>
            </tr>
            <% /foreach %>
        </table>        
        </td>
</tr>
<% /if %>

</table>
Inhalt:<br><%$article_obj.fck%>
<input type="hidden" name="FORMARTLANG[ac_aid]" value="<%$artid%>">
<%/if%>
<% html_subbtn class="sub_btn" value="{LBL_SAVE}" %>
</form>
<%/if%>
</div>
