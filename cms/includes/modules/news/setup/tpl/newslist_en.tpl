<% if ($newslist) %>
<H1>Neuigkeiten</h1>
<% if ($cobj.show_rss_link==1) %>
<div class="std_con_right"><a target="_blank" href="<% $PHPSELF %>?aktion=rss&type=news&gid=<% $newsgroup.group_id %>" title="{LBL_RSS_FEED}"><img alt="{LBL_RSS_FEED}" title="{LBL_RSS_FEED}" src="<% $PATH_CMS %>images/opt_rss.gif" ></a><br><br></div>
<% /if %>

<% if ($customer.PERMOD.newslist.add==true) %>
<a title="Hinzufügen" href="<%$PHPSELF%>?page=<%$page%>&gid=<%$newsgroup.group_id%>&aktion=edit&id=0">Neuigkeiten Hinzufügen</a><br>
<br>
<%/if%>

<table class="tab_std" border="1" width="100%">
<tr class="header">
<td></td>
    <td>Datum</td>
    <td>Titel</td>
    <td>Einleitung</td>
    <td></td>
    <% if ($customer.PERMOD.newslist.del==TRUE || $news.n_kid==$customer.kid ) %>
      <td></td>
    <%/if%>
   </tr>
<% foreach from=$newslist item=news name=loop %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<tr class="<% $sclass %>">
 <td valign="middle" align="left"> <% if ($news.n_icon!="") %>     <img src="<%$news.n_icon%>" >     <%/if%>  </td>   
 <td valign="middle" align="left"><% $news.ndate %></td>
 <td valign="middle" align="left"><a href="<% $news.detail_link %>"><% $news.title %></a></td>
 <td valign="middle" align="left"><% $news.introduction %></td>
 <td valign="middle" align="right">
 <a target="_new" rev="width: 800px; height: 300px; scrolling: no;" title="<% $news.title %>" rel="lyteframe[<% $news.group_ident %>]" href="<% $news.detail_link_popup %>">
 <img src="<% $PATH_CMS %>js/images/lytebox/lupe.gif" >
 </a> 
 <a target="_blank" title="PDF" href="<% $PHPSELF %>?cmd=print_as_pdf&id=<% $news.NID %>">
 <img src="<% $PATH_CMS %>images/opt_pdf_icon_big.gif" ></a></td>
     <% if ($customer.PERMOD.newslist.del==TRUE || $news.n_kid==$customer.kid ) %>
      <td>         <a title="bearbeiten" href="<%$PHPSELF%>?id=<%$news.NID%>&aktion=edit&page=<%$page%>">
         <img alt="bearbeiten" src="<%$PATH_CMS%>images/page_white_edit.png" title="bearbeiten" ></a>
<% if ($customer.PERMOD.newslist.del==TRUE || $news.n_kid==$customer.kid ) %>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?id=<%$news.NID%>&page=<%$page%>&aktion=a_delnews">
<img src="<%$PATH_CMS%>images/page_delete.png" title="löschen"  alt=""></a>
<%/if%> 
<a href="<%$PHPSELF%>?orgaktion=show&aktion=a_approve&value=<% if ($news.approval==1) %>0<%else%>1<%/if%>&id=<%$news.NID%>&page=<%$page%>">
<img title="<% if ($news_obj.approval!=1) %>nicht <%/if%>veröffentlicht" src="<%$PATH_CMS%>images/page_<% if ($news.approval!=1) %>not<%/if%>visible.png"  alt="">
</a></td>
    <%/if%>
 </tr>
 <% assign var=newslistcount value=$smarty.foreach.loop.iteration %>
<% /foreach %>
</tr>
</table>

<% if ($customer.PERMOD.newslist.edit==true) %>
<% if (count($allnewslist.newslist_notapproved) > 0) %>
<br>
<h3>Nicht veröffentlichte News</h3>
<table class="tab_std" border="1">
    <tr class="trheader">
     <td>Datum</td>
     <td>Titel</td>
     <td>Author</td>
    </tr> 
     <% foreach from=$allnewslist.newslist_notapproved item=news%>     
     <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
        <tr class="<% $sclass %>">
         <td valign="middle" align="left"><% $news.ndate %></td>
         <td valign="middle" align="left"><a href="<% $news.detail_link %>"><% $news.title %></a></td>
         <td valign="middle" align="left"><% $news.introduction %></td>
         <td>

         <a title="bearbeiten" href="<%$PHPSELF%>?id=<%$news.NID%>&aktion=edit&page=<%$page%>">
         <img alt="bearbeiten" src="<%$PATH_CMS%>images/page_white_edit.png" title="bearbeiten" ></a>
<% if ($customer.PERMOD.newslist.del==TRUE || $news.n_kid==$customer.kid ) %>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?id=<%$news.NID%>&page=<%$page%>&aktion=a_delnews">
<img src="<%$PATH_CMS%>images/page_delete.png" title="löschen"  alt=""></a>
<%/if%> 
<a href="<%$PHPSELF%>?orgaktion=show&aktion=a_approve&value=<% if ($news.approval==1) %>0<%else%>1<%/if%>&id=<%$news.NID%>&page=<%$page%>">
<img title="<% if ($news_obj.approval!=1) %>nicht <%/if%>veröffentlicht" src="<%$PATH_CMS%>images/page_<% if ($news.approval!=1) %>not<%/if%>visible.png"  alt="">
</a>
</td>
          </tr>
      <% /foreach %>
         </table>
       <%/if%>
                 
<% /if %>

<% /if %>

<% if ($newslistcount==0) %>
<h4> Keine gefunden</h4>
<% /if %>
