<% if ($cmd=='st') %>
<div id="forum-threads">
<h1><%$forumtheme.t_name%></h1>

<% if (count($forum_threads)>0) %>
 
 <% foreach from=$forum_threads item=fthread %>         
   <%*  <div class="row">
          <div class="col-md-2">Author</div>
          <div class="col-md-10">Beitrag</div>
     </div>
     *%>
     <div class="row js-thread-row">
      <div class="col-md-2">
        <a href="#thread-<%$fthread.THREADID%>" class="hidden"><!----></a>
        <label><% $fthread.user.username %></label>
        <img class="img-thumbnail" src="<% $fthread.user.img %>" alt="<% $fthread.user.username %>" >
        <div>
          <span class="small">Mitglied seit <% $fthread.user.datum_ger %></span>
          <br><a class="small" href="#topanker">nach oben</a>
        </div>  
        
</div>             
<div class="col-md-10">
  <div class="row">
    <div class="col-md-6">
      <span class="small">Verfasst am: <% $fthread.thread_datetime %></span>
    </div>
    <div class="col-md-6 text-right">
        <% if ($customer.kid>0 && ($customer.PERMOD.forum.edit==TRUE || $customer.kid==$fthread.f_kid)) %>
        <a title="bearbeiten" href="<%$PHPSELF%>?page=<%$page%>&threadid=<%$fthread.THREADID%>&cmd=answer">
            <img alt="bearbeiten" title="bearbeiten" src="<%$PATH_CMS%>images/opt_edit.png" ></a>
      <%/if%>  
      <% if ($customer.kid>0 && ($customer.PERMOD.forum.del==TRUE || $customer.kid==$fthread.f_kid)) %> 
        <a class="js-delthread" data-tid="<%$fthread.THREADID%>" href="javascript:void(0)">
        <img src="<% $PATH_CMS %>images/opt_del.png" ></a>
      <%/if%>  
    </div>
  </div><!--row-->
<div class="row">
  <div class="col-md-12">
    <div class="forum-text">
      <% $fthread.f_text_bbcode %>
    </div>
    <% if (count($fthread.filelist) > 0)%>
    <h5>Anhänge</h5>
    <table class="table table-striped table-hover">
        <% foreach from=$fthread.filelist item=afile %>
            <tr >
                <td><%$afile.uploadtime%></td>
                <td><%$afile.humanfilesize%></td>
                <td>
                <% if ($afile.ispicture==true && $afile.thumbnail!="") %>
                <a title="<%$afile.f_file%>" rel="lytebox[<% $fthread.THREADID %>]" target="_afile" href="<%$PHPSELF%>?page=<%$page%>&cmd=send_img_tobrowser&id=<%$afile.id%>">
                    <img src="<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" >
                 </a>                   
                <%/if%>
                <% if ($afile.ispicture==false && $afile.thumbnail!="") %>                       
                <a title="<%$afile.f_file%>" target="_afile" href="<%$PHPSELF%>?page=<%$page%>&cmd=fdownload&id=<%$afile.id%>">
                    <img src="<%$afile.thumbnail%>" alt="<%$afile.f_file%> <%$afile.resu%>" title="<%$afile.f_file%> <%$afile.resu%>" >
                 </a> 
                 <%/if%>                  
               <% if ($afile.ispicture==false && $afile.thumbnail=="") %> 
                    <%$afile.f_ext%>
                <%/if%> 
                </td>                
                <td class="text-right"><a title="<%$afile.f_file|sthsc%>" href="<%$PHPSELF%>?page=<%$page%>&cmd=fdownload&id=<%$afile.id%>"><i class="fa fa-download"><!----></i></a></td>
            </tr>
            <% /foreach %>
        </table>        
     <% /if %>
  </div>   <!--col12-->
</div><!--row-->
<hr>
<div class="row">
  <div class="col-md-6">
     <% if ($customer.kid>0) %> 
      <a class="js-freport" href="<%$PHPSELF%>?cmd=freport&page=<%$page%>&threadid=<%$fthread.THREADID%>&themeid=<%$fthread.f_tid%>"><i class="fa fa-bomb"></i>&nbsp;report</a>
    <%/if%>
  </div>
  <div class="col-md-6 text-right">
    <form action="<%$PHPSELF%>?page=<%$page%>&cmd=answer&tid=<%$forumtheme.TID%>" method="post">
      <button class="btn btn-default btn-sm" type"submit"><i class="fa fa-comment-o"><!----></i> antworten</button>
    </form>
  </div>
</div>

  </div><!--col-->
</div><!--row-->
<div class="row">
  <div class="col-md-12"><hr></div>
</div>
<% /foreach %>

<%else%>
  <div class="alert alert-info">Es liegen noch keine Beiträge vor.</div>
<%/if%>
</div><!--container-->
<%/if%>