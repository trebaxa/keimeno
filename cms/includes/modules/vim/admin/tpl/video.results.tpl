<div class="page-header"><h1>Suchergebnis</h1></div>
<h3><% $VIM.YTOPTIONS.searchTerm %></h3>

<% if (count($VIM.video_list)>0) %>
<br><a href="javascript:void(0);" onClick="markAllRows('daform','idvideo',true);return false;">{LA_ALLEAUSWHLEN}</a> | <a href="javascript:void(0);" onClick="markAllRows('daform','idvideo',false);return false;">{LA_ALLEABWHLEN}</a>	
<form name="daform" action="<%$PHPSELF%>" method="POST">	
<input type="hidden" name="cmd" value="approve_videos">
<input type="hidden" name="start" value="<% $GET.start %>">
<input type="hidden" name="epage" value="<% $epage %>">
<input type="hidden" name="section" value="<% $section %>">
<div style="width:600px;">
<fieldset>
<table  class="table table-striped table-hover">
<tr><td>{LA_FILTERMETHOD}: </td><td>
<select class="form-control custom-select" name="QF[method]">
	<option value="accept_sel">approve selected</option>	
</select>
</td></tr>
<td>{LA_TOTALVIDEOCOUNT}:</td>
<td><% $VIM.video_totalcount %></td>
</tr>
</tr>
</table>
<div class="subright"><%$subbtn%></div>
<% include file="paging.admin.tpl" %>
</fieldset>
</div>

	<table  class="table table-striped table-hover">
	<thead><tr>
				<th></th>
        <th></th>
        <th></th>
        <th></th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&direc=<%$VIM.video_sorting.direc%>&order=VP.author_username&section=<%$section%>&aktion=<%$aktion%>&id=<%$GET.id%>">{LA_AUTHOR}</a></th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&direc=<%$VIM.video_sorting.direc%>&order=VP.videotitle&section=<%$section%>&aktion=<%$aktion%>&id=<%$GET.id%>">{LA_VIDEOTITLE}</a></th>        
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&direc=<%$VIM.video_sorting.direc%>&order=VP.upload_date&section=<%$section%>&aktion=<%$aktion%>&id=<%$GET.id%>">{LA_UPLOADDATE}</a></th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&direc=<%$VIM.video_sorting.direc%>&order=VP.videoduration&section=<%$section%>&aktion=<%$aktion%>&id=<%$GET.id%>">{LA_VIDEODURATION}</a></th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&direc=<%$VIM.video_sorting.direc%>&order=VP.viewcount&section=<%$section%>&aktion=<%$aktion%>&id=<%$GET.id%>">{LBL_VIEWS}</a></th>        
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&direc=<%$VIM.video_sorting.direc%>&order=VP.videotags&section=<%$section%>&aktion=<%$aktion%>&id=<%$GET.id%>">{LA_YPTAGS}</a></th>        
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&direc=<%$VIM.video_sorting.direc%>&order=VP.videodescription&section=<%$section%>&aktion=<%$aktion%>&id=<%$GET.id%>">{LA_DESCRIPTION}</a></th>
        
	</tr></thead>
	<% foreach from=$VIM.video_list item=ytvideo name="vloop" %>	 
  		<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>		<% assign var=sclass value="row1" %> <% /if %>
		<tr class="<% $sclass %>">
        <td><% $smarty.foreach.vloop.iteration %></td>
        <td><input type="checkbox" id="idvideo" name="VIDEOIDS[]" value="<% $ytvideo.yt_videoid %>"></td>
        <td><a target="_yt" href="<% $ytvideo.yt_watchpageurl %>"><img width="100"  src="<% $ytvideo.yt_thumbnailurl %>" /></a></td>
        <td>
       
        <% if ($ytvideo.approved_video_count>0) %>
         <img src="./images/page_visible.png"  alt="<% $ytvideo.approved_video_count %>" title="listed: <% $ytvideo.approved_video_count %>">
         <%else%>
         <img src="./images/page_notvisible.png"  alt="<% $ytvideo.approved_video_count %>" title="listed: <% $ytvideo.approved_video_count %>">
        <%/if%>
        </td>    
        <td><% $ytvideo.author %></td>
        <td width="300"><% $ytvideo.yt_videotitle %></td>        
        <td><% $ytvideo.yt_uploaddate %></td>        
        <td class="text-right"><% $ytvideo.yt_videoduration_min %></td>                
        <td><% $ytvideo.yt_viewcount %></td>        
        <td width="300"><% $ytvideo.vtags %></td>        
        <td width="300"><% $ytvideo.yt_videodescription|truncate:100 %></td>
        </tr>
	<%/foreach%>
  </table>
  <%$subbtn%>
  </form>
  <% include file="paging.admin.tpl" %>
  <br><a href="javascript:void(0);" onClick="markAllRows('daform','idvideo',true);return false;">{LA_ALLEAUSWHLEN}</a> | <a href="javascript:void(0);" onClick="markAllRows('daform','idvideo',false);return false;">{LA_ALLEABWHLEN}</a>
	<%else%>
	{LA_NOSEARCHRESULT}
<%/if%>
