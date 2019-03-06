<div class="btn-group">
<a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=videolist&section=videomanager">Videos verwalten</a>
  <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=showall&section=cats">{LA_SHOWALL}</a>
  <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=catadd&section=cats">{LA_ADDCATEGORY}</a>
</div>
<% if ($cmd=="videolist") %>
<h3>Videos verwalten</h3>
<form action="<%$PHPSELF%>" method="POST" class="form-inline">	
<input type="hidden" name="cmd" value="<%$cmd%>">
<input type="hidden" name="start" value="<% $GET.start %>">
<input type="hidden" name="section" value="<% $section %>">
<input type="hidden" name="epage" value="<% $epage %>">
<div style="width:1100px;">
<fieldset>
<table class="table table-striped table-hover" >
<tr>
<td width="210">{LA_TOTALVIDEOCOUNTAPP}:</td>
<td><strong><% $VIM.video_totalcount %></strong></td>
</tr>
<tr>
<td>{LA_TOTALVIDEOFILTERCOUNT}:</td>
<td><strong><% $VIM.video_filtered_count %></strong></td>
</tr>
<tr>
<td>{LA_PLEASESELECTCATEGORY}:</td><td>
 <select class="form-control" name="QFILTER[cid]">
<% $VIM.cat_selectbox %>
</select></td>
</tr>
<tr>
<td>{LA_VIDEOSTOCK}:</td>
<td>
      <select class="form-control" name="QFILTER[v_stock]">
        <option <% if ($QFILTER.v_stock==0) %>selected<%/if%> value="">{LA_NOMATTERALL}</option>
        <% foreach from=$VIM.stock_list item=stitem %>	        
        <option <% if ($QFILTER.v_stock==$stitem.v_stock) %>selected<%/if%> value="<%$stitem.v_stock%>"><%$stitem.v_stock%></option>
        <%/foreach%>
      </select>
     </td> </tr>  
<tr>
<td>{LBL_SEARCH}:</td>
<td>
     <input type="text" class="form-control" name="QFILTER[searchword]" value="<%$POST.QFILTER.searchword|sthsc%>" size="30">
     </td> </tr>                 
</table>
<div class="subright"><%$btngo%></div>

</fieldset>
</div>
    </form>
    
    
<% if count($VIM.video_list)>0 %>
<form name="videoform" class="form-inline" action="<%$PHPSELF%>" method="POST">	
<input type="hidden" name="cmd" value="<%$cmd%>">
<input type="hidden" name="section" value="<%$section%>">
<input type="hidden" name="start" value="<% $GET.start %>">
<input type="hidden" name="epage" value="<% $epage %>">
 	<% foreach from=$QFILTER key=qk item=qv %>	
<input type="hidden" name="QFILTER[<%$qk%>]" value="<% $qv %>">
<%/foreach%>
<div class="row">
    <div class="col-md-6">
    {LA_FOLGENDEAUFGABEAUSFHR}:
    <select class="form-control" name="cmd">
        <option <% if ($GET.cmd=="vim_save_videos" || $GET.cmd=="") %>selected<%/if%> value="vim_save_videos">{LA_SAVE}</option>
    	<option <% if ($GET.cmd=="vim_delete_videos") %>selected<%/if%> value="vim_delete_videos">delete marked video links</option>
    </select><%$btngo%>
    </div>
</div>

<div style="width:1100px;"><br><a href="javascript:void(0);" onClick="markAllRows('videoform','idvideo',true);return false;">{LA_ALLEAUSWHLEN}</a> | <a href="javascript:void(0);" onClick="markAllRows('videoform','idvideo',false);return false;">{LA_ALLEABWHLEN}</a>

<% include file="paging.admin.tpl" %><br>
	<table  class="table table-striped table-hover">
	<thead><tr>
    	<th></th>
        <th></th>
        <th></th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=<%$aktion%>&section=<%$section%>&start=<%$REQUEST.start%>&cid=<%$REQUEST.cid%>&sorttype=STRING&col=v_stock&direc=<%$FILTER.direc%>&<%$qfilter_query%>">{LA_VIDEOSTOCK}</a></th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=<%$aktion%>&section=<%$section%>&start=<%$REQUEST.start%>&cid=<%$REQUEST.cid%>&sorttype=STRING&col=v_author_username &direc=<%$FILTER.direc%>&<%$qfilter_query%>">{LA_AUTHOR}</a></th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=<%$aktion%>&section=<%$section%>&start=<%$REQUEST.start%>&cid=<%$REQUEST.cid%>&sorttype=STRING&col=v_videotitle&direc=<%$FILTER.direc%>&<%$qfilter_query%>">{LA_VIDEOTITLE}</a></th>
        <th>{LA_VIDEOPUNCHCATEGORIES}</th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=<%$aktion%>&section=<%$section%>&start=<%$REQUEST.start%>&cid=<%$REQUEST.cid%>&sorttype=STRING&col=v_videotags&direc=<%$FILTER.direc%>&<%$qfilter_query%>">{LA_YPTAGS}</a></th>        
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=<%$aktion%>&section=<%$section%>&start=<%$REQUEST.start%>&cid=<%$REQUEST.cid%>&sorttype=STRING&col=v_videoduration&direc=<%$FILTER.direc%>&<%$qfilter_query%>">{LA_VIDEODURATION}</a></th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=<%$aktion%>&section=<%$section%>&start=<%$REQUEST.start%>&cid=<%$REQUEST.cid%>&sorttype=STRING&col=v_upload_date&direc=<%$FILTER.direc%>&<%$qfilter_query%>">{LA_UPLOADDATE}</a></th>        
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=<%$aktion%>&section=<%$section%>&start=<%$REQUEST.start%>&cid=<%$REQUEST.cid%>&sorttype=STRING&col=v_apptime&direc=<%$FILTER.direc%>&<%$qfilter_query%>">{LA_APPROVEDDATE}</a></th>        
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=<%$aktion%>&section=<%$section%>&start=<%$REQUEST.start%>&cid=<%$REQUEST.cid%>&sorttype=STRING&col=v_frontpage&direc=<%$FILTER.direc%>&<%$qfilter_query%>">Frontpage</a></th>
        <th><a href="<%$PHPSELF%>?epage=<%$epage%>&aktion=<%$aktion%>&section=<%$section%>&start=<%$REQUEST.start%>&cid=<%$REQUEST.cid%>&sorttype=STRING&col=v_order&direc=<%$FILTER.direc%>&<%$qfilter_query%>">FP Sort.</a></th>
                
	</tr></thead>
	<% foreach from=$VIM.video_list item=ytvideo name="vloop" %>	 
		<tr class="wlu_tr_<% $ytvideo.v_stock %>">
			
	<% if ($ytvideo.v_stock=='YT') %>	
  	<% include file="video.vm.yt.tpl" %>               
  <%/if%>
	<% if ($ytvideo.v_stock=='VI') %>	
  	<% include file="video.vm.vim.tpl" %>               
  <%/if%>  


        </tr>
	<%/foreach%>
  </table>

<% include file="paging.admin.tpl" %>
</div>

</form>
<br><a href="javascript:void(0);" onClick="markAllRows('videoform','idvideo',true);return false;">{LA_ALLEAUSWHLEN}</a> | <a href="javascript:void(0);" onClick="markAllRows('videoform','idvideo',false);return false;">{LA_ALLEABWHLEN}</a>

<%else%>
<div class="bg-info text-info">no results</div>
<%/if%>
<%/if%>