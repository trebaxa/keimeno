<% if ($aktion=='answer') %>
<% if ($GET.threadid>0) %>
<h2>Bearbeiten</h2>
<%else%>
<h2>Antworten</h2>
<%/if%>

<script src="<%$forum_path%>bbcode/editor.js" type="text/javascript"></script>
<form onsubmit="doCheck();" action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
<div style="width:600px">
<fieldset>        
<legend>Beitrag bearbeiten</legend>
<table width="100%">
<tr>
        <td>
       <div class="richeditor">
        <div class="editbar">
            <button title="bold" onclick="doClick('bold');" type="button"><b>B</b></button>
            <button title="italic" onclick="doClick('italic');" type="button"><i>I</i></button>
            <button title="underline" onclick="doClick('underline');" type="button"><u>U</u></button>
            <button title="hyperlink" onclick="doLink();" type="button" style="background-image:url('<%$forum_path%>bbcode/images/url.gif');">&nbsp;</button>
            <button title="image" onclick="doImage();" type="button" style="background-image:url('<%$forum_path%>bbcode/images/img.gif');">&nbsp;</button>
            <button title="list" onclick="doClick('InsertUnorderedList');" type="button" style="background-image:url('<%$forum_path%>bbcode/images/icon_list.gif');">&nbsp;</button>
            <button title="color" onclick="showColorGrid2('none')" type="button" style="background-image:url('<%$forum_path%>bbcode/images/colors.gif');">&nbsp;</button><span id="colorpicker201" class="colorpicker201"></span>
            <button title="quote" onclick="doQuote();" type="button" style="background-image:url('<%$forum_path%>bbcode/images/icon_quote.png');">&nbsp;</button>
            <button title="youtube" onclick="InsertYoutube();" type="button" style="background-image:url('<%$forum_path%>bbcode/images/icon_youtube.gif');">&nbsp;</button>
            <button title="switch to source" type="button" onclick="javascript:SwitchEditor()" style="background-image:url('<%$forum_path%>bbcode/images/icon_html.gif');">&nbsp;</button>
        </div>
        <div class="container">
        <textarea id="tbMsg" name="FORMTHREAD[f_text]" style="height:150px;width:100%;"><%$forum_thread.f_text%></textarea>
        </div>
    </div>
    <script type="text/javascript">
        initEditor("tbMsg", true);
    </script>
      </td>
</tr>
<tr>
    <td><br><h3>Beitragsanhang:</h3><br>
    <input type="file" size="25" name="datei">
    </td>
</tr>
</table>

<% if (count($forum_thread.filelist) > 0)%><br>
<h3>Anhänge</h3>
   <table class="tab_std" width="100%">
    <% foreach from=$forum_thread.filelist item=afile %>
        <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
            <tr class="<%$sclass%>">
                <td><%$afile.uploadtime%></td>
                <td><a title="<%$afile.f_file%>" rel="lytebox[l<% $forum_threadTHREADID %>]" target="_afile" href="/<%$FORUM_FILE_PATH%><%$afile.f_file%>"><%$afile.f_file%></a></td>
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
                <td class="tdright">
<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?threadid=<%$forum_thread.THREADID%>&fileid=<%$afile.id%>&page=<%$page%>&aktion=a_delfile">
<img src="<%$PATH_CMS%>images/page_delete.png" title="löschen"  alt="löschen"></a>                
                </td>
            </tr>
            <% /foreach %>
        </table>        
     <% /if %>
<div class="subright"> 
<% html_subbtn class="sub_btn" value="Speichern" onclick="doCheck();" %>

</div>
</fieldset>       
</div>
  <input type="hidden" name="aktion" value="answerthread">
   <input type="hidden" name="page" value="<%$page%>">
   <input type="hidden" name="fid" value="<%$forumobj.FID%>">
      <input type="hidden" name="threadid" value="<%$GET.threadid%>">
   <input type="hidden" name="FORMTHREAD[f_fid]" value="<%$forumobj.FID%>">
<input type="hidden" name="FORMTHREAD[f_tid]" value="<%$forumtheme.id%>">

</form>
<%/if%>
