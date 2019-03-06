<% if ($cmd=='answer') %>

  <% if ($GET.threadid>0) %><h2>Bearbeiten</h2><%else%><h2>Antworten</h2><%/if%>


<form onsubmit="doCheck();" action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
<div class="row">
  <div class="col-md-9">
    
        <label>Beitrag bearbeiten</label>
        <textarea id="tbMsg" name="FORMTHREAD[f_text]" style="width:100%;height:300px"><%$forum_thread.f_text%></textarea>
     

  </div>    
  <div class="col-md-3">
      <div class="form-group">
        <label for="fffile">Beitragsanhang</label>
        <input type="file" id="fffile" name="datei">
      </div>
    <% if (count($forum_thread.filelist) > 0)%><br>
<h3>Anhänge</h3>
   <table class="table table-striped table-hover">
    <% foreach from=$forum_thread.filelist item=afile %>
            <tr>
                <td><%$afile.uploadtime%></td>
                <td><a title="<%$afile.f_file%>" href="<%$PHPSELF%>?page=<%$page%>&cmd=fdownload&id=<%$afile.id%>"><%$afile.f_file%></a></td>
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
                <td class="text-right">
                  <a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?threadid=<%$forum_thread.THREADID%>&fileid=<%$afile.id%>&page=<%$page%>&cmd=delforumfile">
                  <i class="fa fa-trash"><!----></i></a>                
                </td>
                <td class="text-right"><a title="<%$afile.f_file|sthsc%>" href="<%$PHPSELF%>?page=<%$page%>&cmd=fdownload&id=<%$afile.id%>"><i class="fa fa-download"><!----></i></a></td>
            </tr>
            <% /foreach %>
        </table>        
     <% /if %>
    
  </div>
</div>

    <script>
    $(function() {
    	$("#tbMsg").sceditor({
    		plugins: "bbcode",
    		toolbar: "bold,italic,underline|link|pastetext,quote,code|maximize,source",
    		style: "minified/jquery.sceditor.default.min.css",
    		emoticonsRoot : "<%$forum_path%>js/sceditor/"
    	});
    });
    </script>



<div class="text-right">
  <button class="btn btn-primary" onclick="doCheck();" type="submit">Senden</button>  
</div>


  <input type="hidden" name="cmd" value="answerthread">
  <input type="hidden" name="page" value="<%$page%>">
  <input type="hidden" name="fid" value="<%$forumtheme.FID%>">
  <input type="hidden" name="threadid" value="<%$GET.threadid%>">
  <input type="hidden" name="FORMTHREAD[f_fid]" value="<%$forumtheme.FID%>">
  <input type="hidden" name="FORMTHREAD[f_tid]" value="<%$forumtheme.TID%>">
</form>

<%/if%>