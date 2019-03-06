<h1>Videothek</h1>

Kategorie:
     <select class="form-control" onChange="location.href=this.options[this.selectedIndex].value">
     <option <% if (0==$GET.videocid) %>selected<%/if%> value="0">- nicht zugeordnet -</option>
     <% foreach from=$VIM.cat_selectbox_arr key=catid item=catname %>
       <option <% if ($catid==$GET.videocid) %>selected<%/if%> value="<%$PHPSELF%>?page=<%$page%>&cmd=load_videos_fe&videocid=<%$catid%>"><% $catname %></option>
      <%/foreach%>
      </Select>
      

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
