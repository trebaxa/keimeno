<% if ($aktion=='st') %>
<h1><%$forumtheme.t_name%></h1>
<% if (count($forum_threads)>0) %>
 <table class="tab_std" width="100%">
 <% foreach from=$forum_threads item=fthread %>         
     <tr class="header">
<td>Author</td>             
<td>Beitrag</td>             
</tr>
<tr>
<td valign="top"><b><% $fthread.user.username %></b><br><img src="<% $fthread.user.img %>" >
<div style="float:left;margin-bottom:5px">
<span class="small">
<br>Mitglied seit <% $fthread.user.datum_ger %></span>
<br><a class="small" href="#topanker">nach oben</a>
</td>             
                <td valign="top">
<div style="float:left;width:100%;border-bottom:1px solid #000000;">
<div style="float:left;margin-bottom:5px">
<span class="small">Verfasst am: <% $fthread.thread_datetime %></span>
</div>
<div style="float:right">
<% if ($customer.kid>0 && ($customer.PERMOD.forum.edit==TRUE || $customer.kid==$fthread.f_kid)) %>
  <a title="bearbeiten" href="<%$PHPSELF%>?page=<%$page%>&threadid=<%$fthread.THREADID%>&aktion=answer">
        <img alt="bearbeiten" title="bearbeiten" src="<%$PATH_CMS%>images/opt_edit.png" ></a>
<%/if%>  
<% if ($customer.kid>0 && ($customer.PERMOD.forum.del==TRUE || $customer.kid==$fthread.f_kid)) %> 
    <a onClick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?page=<%$page%>&threadid=<%$fthread.THREADID%>&aktion=delthread">
    <img src="<% $PATH_CMS %>images/opt_del.png" ></a>
<%/if%>  


    
</div>


</div>
<div style="float:left;width:100%;">
<% $fthread.f_text_bbcode|nl2br %>
<% if (count($fthread.filelist) > 0)%><br>
<h3>Anhänge</h3>
   <table class="tab_std" width="100%">
    <% foreach from=$fthread.filelist item=afile %>
        <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
            <tr class="<%$sclass%>">
                <td><%$afile.uploadtime%></td>
                <td><a title="<%$afile.f_file%>" rel="lytebox[l<% $fthread.THREADID %>]" target="_afile" href="/<%$FORUM_FILE_PATH%><%$afile.f_file%>"><%$afile.f_file%></a></td>
                <td><%$afile.humanfilesize%></td>
                <td>
                <% if ($afile.ispicture==true && $afile.thumbnail!="") %>
                <a title="<%$afile.f_file%>" rel="lytebox[<% $fthread.THREADID %>]" target="_afile" href="<%$PATH_CMS%><%$FORUM_FILE_PATH%><%$afile.f_file%>">
                    <img src="<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" >
                 </a>                   
                <%/if%>
                <% if ($afile.ispicture==false && $afile.thumbnail!="") %>                       
                <a title="<%$afile.f_file%>" target="_afile" href="<%$PATH_CMS%><%$FORUM_FILE_PATH%><%$afile.f_file%>">
                    <img src="<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" >
                 </a> 
                 <%/if%>                  
               <% if ($afile.ispicture==false && $afile.thumbnail=="") %> 
                    <%$afile.f_ext%>
                <%/if%> 
                </td>                
            </tr>
            <% /foreach %>
        </table>        
     <% /if %>
</div>
<% if ($customer.kid>0) %> 
<div style="float:left;width:50%;text-align:left">
<% if ($TW.connected==TRUE && $customer.kid==$fthread.f_kid) %>
<form role="form" action="<%$PHPSELF%>?page=9940&aktion=tw_post_status" method="post">
<input type="hidden" name="FORM[twstatus]" value="<% $fthread.f_text_bbcode|sthsc%>">
<input type="hidden" name="comingfrom" value="<% $SERVERVARS.SCRIPT_URI%>">
<input title="post it on Twitter" style="border:0px;background:transparent;margin:0;padding:0;margin-top:6px;" type="image" src="<%$PATH_CMS%>includes/modules/tw/images/twitterico.png">
</form>
<%/if%>
<% if ($TW.connected==FALSE && $customer.kid==$fthread.f_kid && $customer.kid>0 && $customer.tw_consumerkey!="") %>
<a title="Connect to Twitter" href="<%$PHPSELF%>?page=9940&aktion=tw_connect"><img src="<%$PATH_CMS%>includes/modules/tw/images/twitteroffico.png"  alt="Connect to Twitter"></a>
<%/if%>
</div>

<div style="float:left;width:50%;text-align:right">
<form role="form" style="float:right;" action="<%$PHPSELF%>?page=<%$page%>&aktion=answer&tid=<%$forumtheme.id%>" method="post">
<% html_subbtn class="btn btn-primary" value="antworten" %>
</form>
</div>
<%/if%>
</td>
         </tr>
<tr class="fo_footer"> <td colspan="2"><hr></td></tr>
                <%/foreach%>

 </table>

<%else%>
<div class="infobox">Es liegen noch keine Beiträge vor.</div>
<%/if%>
<%/if%>
