<% if ($cmd=='sf') %>

<h2><%$forumobj.fn_name%></h2>
<% if ($customer.kid>0) %> 
  <a class="btn btn-primary" href="<%$PATH_CMS%>index.php?fid=<%$GET.fid%>&page=<%$page%>&cmd=newtheme"><i class="fa fa-plus"><!----></i> Neues Thema</a>    
<%/if%>
<% if (count($forum_themes)>0) %>
<table class="table table-hover table-striped">
    <thead>
    <tr>
        <th></hd>
        <th>Thema</th>
        <th>Antworten</th>
        <th>Aufrufe</th>
        <th>Letzter Beitrag</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <% foreach from=$forum_themes item=ftheme %> 
    <tr>
        <td width="22">
       <% if ($ftheme.hastodaythread==true) %>
           <i class="fa fa-file text-danger"><!----></i>
         <%else%>   
            <i class="fa fa-file"><!----></i>
         <%/if%>        
        </td>
        <td><a href="<%$ftheme.themelink%>"><% $ftheme.t_name %></a></td>
        <td><%$ftheme.THCOUNT%></td>
        <td><%$ftheme.t_hits%></td>
        <td><% if ($ftheme.lastthread.id>0) %> <%$ftheme.lastthread.thread_datetime%> <%else%> 
        - <%/if%> </td><td>
<% if ($customer.kid>0 && ($customer.PERMOD.forum.del==TRUE)) %> 
    <a onClick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?fid=<%$GET.fid%>&page=<%$page%>&tid=<%$ftheme.TID%>&aktion=deltheme">
    <img src="<% $PATH_CMS %>images/opt_del.png" ></a>
<%/if%>         
</td>
    </tr>
    <%/foreach%>
    </tbody>
</table>
<%else%>
  <div class="alert alert-info">Es liegen noch keine Themen vor.</div>
<%/if%>
<%/if%>


<% if ($cmd=='newtheme') %>
  <h2>Neuer Beitrag</h2>
  <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
  <div class="row">
    <div class="col-md-6">
    <div class="form-group">
            <label>Themen Titel:</label>
            <input type="text" name="FORM[t_name]" value="" placeholder="Theme Titel" class="form-control">
    </div>

     
            <textarea id="tbMsg" name="FORMTHREAD[f_text]" style="width:100%;height:300px"><%$forum_thread.f_text%></textarea>

  <button class="btn btn-primary" type="submit">Senden</button>
    </div><!-- col-md-6 -->
  </div><!-- row -->
  
  <input type="hidden" name="cmd" value="savetheme">
  <input type="hidden" name="page" value="<%$page%>">
  <input type="hidden" name="fid" value="<%$forumobj.FID%>">
  <input type="hidden" name="FORM[t_fid]" value="<%$forumobj.FID%>">
  <input type="hidden" name="FORMTHREAD[f_fid]" value="<%$forumobj.FID%>">
  
  </form>
  
     <script>
    $(function() {
    	$("#tbMsg").sceditor({
    		plugins: "bbcode",
    		toolbar: "bold,italic,underline|pastetext,quote,code|maximize,source",
    		style: "minified/jquery.sceditor.default.min.css",
    		emoticonsRoot : "<%$forum_path%>js/sceditor/"
    	});
    });
    </script>

<%/if%>