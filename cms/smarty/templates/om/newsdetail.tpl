<h1><% $news_obj.ndate %> - <% $news_obj.title %></h1>

<div style="text-align:right;float:left;width:100%;">
<% if ($customer.PERMOD.newslist.edit==TRUE || $news_obj.n_kid==$customer.kid ) %>
         <a title="bearbeiten" href="<%$PHPSELF%>?id=<%$news_obj.NID%>&aktion=edit&page=<%$page%>">
         <img alt="bearbeiten" src="<%$PATH_CMS%>images/page_white_edit.png" title="bearbeiten" ></a>
<%/if%>
<% if ($customer.PERMOD.newslist.del==TRUE || $news_obj.n_kid==$customer.kid ) %>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?id=<%$news_obj.NID%>&page=<%$page%>&aktion=a_delnews">
<img src="<%$PATH_CMS%>images/page_delete.png" title="lÃ¶schen"  alt=""></a>
<%/if%> 
<% if ($customer.PERMOD.newslist.edit==TRUE ) %>
<a href="<%$PHPSELF%>?orgaktion=show&aktion=a_approve&value=<% if ($news_obj.approval==1) %>0<%else%>1<%/if%>&id=<%$news_obj.NID%>&page=<%$page%>">
<img title="<% if ($news_obj.approval!=1) %>nicht <%/if%>verÃ¶ffentlicht" src="<%$PATH_CMS%>images/page_<% if ($news_obj.approval!=1) %>not<%/if%>visible.png"  alt=""></a>
<%/if%> 
</div>

<% $news_obj.content %>
<% if ($news_obj.img !="") %><img src="<% $news_obj.img %>" ><% /if %>

<% if (count($news_obj.filelist) > 0)%>
<br><br>
<h3>AnhÃ¤nge</h3><br>
        <table class="tab_std" border="1">
        <% foreach from=$news_obj.filelist item=afile %>
                <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
                        <tr class="<%$sclass%>">                                
                                <td><a title="<%$afile.f_file%>" target="_afile" href="<%$PATH_CMS%><%$NEWS_PATH%><%$afile.f_file%>"><%$afile.f_file%></a></td>
                                <td><%$afile.humanfilesize%></td>
                                <td>
                                <% if ($afile.thumbnail!="") %>
                                        <img src="<%$PATH_CMS%><%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" >
<%/if%>
</td>
                                <td class="tdright">
<% if ($customer.PERMOD.newslist.del==TRUE || $news_obj.n_kid==$customer.kid ) %>
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?id=<%$news_obj.NID%>&page=<%$page%>&aktion=a_delfile&fileid=<%$afile.id%>">
<img src="<%$PATH_CMS%>images/page_delete.png" title="lÃ¶schen"  alt=""></a>
<%/if%> 
</td>
                        </tr>
                        <% /foreach %>
                </table>          
<% /if %>
