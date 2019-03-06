<% if ($aktion=="show" || $aktion=="shownewsdetail") %> 
    <% include file="newsdetail.tpl" %>
<% /if %>

<% if ($newslist && $aktion=="") %>
<H1>Neuigkeiten</h1>
<% if ($cobj.show_rss_link==1) %>
<div class="row">
    <div class="col-md-12 text-right">
        <a target="_blank" href="<% $PHPSELF %>?aktion=rss&type=news&gid=<% $newsgroup.group_id %>" title="{LBL_RSS_FEED}"><img alt="{LBL_RSS_FEED}" title="{LBL_RSS_FEED}" src="<% $PATH_CMS %>images/opt_rss.gif" ></a>
        <% if ($customer.PERMOD.newslist.add==true) %><a title="Hinzufügen" href="<%$PHPSELF%>?page=<%$page%>&gid=<%$newsgroup.group_id%>&aktion=edit&id=0">Neuigkeiten Hinzufügen</a><%/if%>
    </div>
</div>    
<% /if %>

<table class="table table-hover">
<thead>
    <tr>
    <th></th>
    <th>Datum</th>
    <th>Titel</th>
    <th>Einleitung</th>
    <th></th>
    <% if ($customer.PERMOD.newslist.del==TRUE || $news.n_kid==$customer.kid ) %>
      <th></th>
    <%/if%>
   </tr>
</thead>   
<tbody>
<% foreach from=$newslist item=news name=loop %>
<tr>
 <td> <% if ($news.n_icon!="") %>     <img src="<%$news.n_icon%>" >     <%/if%>  </td>   
 <td><% $news.ndate %></td>
 <td><a href="<% $news.detail_link %>"><% $news.title %></a></td>
 <td><% $news.introduction %></td>
 <td class="text-right">
 <a title="PDF" href="<% $PHPSELF %>?cmd=print_as_pdf&id=<% $news.NID %>"><i class="fa fa-file-pdf-o"><!----></i></a></td>
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
</tbody>
</table>

<% if ($customer.PERMOD.newslist.edit==true) %>
<% if (count($allnewslist.newslist_notapproved) > 0) %>
<br>
<h3>Nicht veröffentlichte News</h3>
<table class="table table-hover">
<thead>
    <tr>
     <th>Datum</th>
     <th>Titel</th>
     <th>Author</th>
    </tr>
<tbody>    
     <% foreach from=$allnewslist.newslist_notapproved item=news%>     
        <tr>
         <td><% $news.ndate %></td>
         <td><a href="<% $news.detail_link %>"><% $news.title %></a></td>
         <td><% $news.introduction %></td>
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
      </tbody>
         </table>
       <%/if%>
                 
<% /if %>
<% if ($newslistcount==0) %>
<h4> Keine gefunden</h4>
<% /if %>
<% /if %>

