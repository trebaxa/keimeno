<% if ($aktion=='sf') %>
<h1><%$forumobj.fn_name%></h1>
<% if ($customer.kid>0) %> 
<div style="float:left;width:100%;margin:10px;">
<a href="<%$PATH_CMS%>index.php?fid=<%$GET.fid%>&page=<%$page%>&aktion=newtheme"><% html_subbtn class="sub_btn" value="Neues Thema" %></a>    
 </div>
<%/if%>
<% if (count($forum_themes)>0) %>
<table class="tab_std" width="100%">
    <tr class="header">
        <td></td>
        <td>Thema</td>
        <td>Antworten</td>
        <td>Aufrufe</td>
        <td>Letzter Beitrag</td>
        <td></td>
    </tr>
    <% foreach from=$forum_themes item=ftheme %> <% if ($sclass=="row1") %> <% assign var=sclass value="row2" %>
    <% else %> <% assign var=sclass value="row1" %> <% /if %>
    <tr class="<%$sclass%>">
        <td width="22">
       <% if ($ftheme.hastodaythread==true) %>
            <img src="<%$forum_path%>images/themenew.png" >
         <%else%>   
            <img src="<%$forum_path%>images/theme.png" >
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
</table>
<%else%>
<div class="infobox">
    Es liegen noch keine Themen vor.</div>
<%/if%> <%/if%>


<% if ($aktion=='newtheme') %>
<h1>Neues Thema</h1>
<form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
<div style="width:600px">
<fieldset>        
<legend>Thema erstellen</legend>
<table width="100%" >
<tr>
        <td class="label">Themen Titel:</td>
        <td><input type="text" name="FORM[t_name]" value="" size="60"></td>
</tr>
<tr>
        <td colspan="2">
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
</table>
<div class="subright"><%$subbtn%></div>
</fieldset>       
</div>
  <input type="hidden" name="aktion" value="savetheme">
   <input type="hidden" name="page" value="<%$page%>">
   <input type="hidden" name="fid" value="<%$forumobj.FID%>">
   <input type="hidden" name="FORM[t_fid]" value="<%$forumobj.FID%>">
<input type="hidden" name="FORMTHREAD[f_fid]" value="<%$forumobj.FID%>">

</form>
<%/if%>
