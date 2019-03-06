<div class="video-nav">
<div style="float:left;padding-top:3px;">
Gefunden: <% $VIM.video_totalcount %>
</div>
<% if ($VIM.video_search==TRUE) %>
<div style="float:right;margin-left:6px;">
<% if ($VIM.video_hasnext==TRUE) %>
<button class="video-nav-link" onClick="javascript:video_show_next(<%$VIM.video_start%>,'<%$GET.sword%>','<%$PHPSELF%>','<%$GET.page%>');">weiter »</button>
<%/if%>
</div>
<div style="float:right;margin-left:6px;padding-top:3px;">
zeige: <%$VIM.video_startnow%> - <%$VIM.video_start%>
</div>
<div style="float:right;margin-left:6px;">
<% if ($VIM.video_hasprevious==TRUE) %>
<button class="video-nav-link" onClick="javascript:video_show_next(<%$VIM.video_startprevious%>,'<%$GET.sword%>','<%$PHPSELF%>','<%$GET.page%>');">« vor</button>
    <%/if%>
</div>
<%/if%>
<div class="clearer"></div>
</div>



<% if (count($VIM.video_list)>0) %>
    <table class="tab_std" width="100%">
    <tr class="trheader">
        <td></td>
        <td>Titel</td>
        <td>L&auml;nge</td>
        <td width="300">Beschreibung</td>
        <td>Upload Datum</td>
      
    </tr>   
    <% foreach from=$VIM.video_list item=file %>
    <tr ">
     <td><a href="/video/<%$file.v_videoid%>/<%$file.v_videotitle|uen%>.html"><img width="100" src="<% $file.v_thumbnailurl  %>" ></a></td>
     <td><a href="/video/<%$file.v_videoid%>/<%$file.v_videotitle|uen%>.html"><% $file.v_videotitle %></a></td>
     <td><% $file.v_duration %></td>
     <td><% $file.v_videodescription|truncate:300 %></td>
     <td><% $file.v_recorded_ger %></td>
    </tr>
    <%/foreach%>
    </table> 
    <%else%>
<div class="infobox">Keine Einträge</div>
    <%/if%>
