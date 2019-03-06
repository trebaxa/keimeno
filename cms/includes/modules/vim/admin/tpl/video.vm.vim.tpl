        <td><% assign var=vc value=$smarty.foreach.vloop.iteration+$REQUEST.start %><%$vc%></td>
        <td><input type="checkbox" id="idvideo" name="VIDEOIDS[]" value="<% $ytvideo.v_videoid %>">
        <input type="hidden" name="VIDS[]" value="<% $ytvideo.v_videoid %>"></td>
        <td><a target="_yt" href="<% $ytvideo.v_watchpageurl %>"><img width="90"  src="<% $ytvideo.v_thumbnailurl %>" /></a></td>
        <td class="text-center"><% $ytvideo.v_stock %></td>
        <td><% $ytvideo.author %></td>
        <td width="300"><% $ytvideo.v_videotitle %></td>
        <td><% '<br>'|implode:$ytvideo.pathes %></td>               
        <td width="300"><% $ytvideo.vtags %></td>    
        <td class="text-center"><% $ytvideo.v_videoduration_min %></td>     
        <td><% $ytvideo.v_uploaddate %></td>        
        <td><% $ytvideo.approved_date %></td>
        <td class="text-center"><input type="checkbox" value="1" name="VID[<%$ytvideo.v_videoid%>][v_frontpage]"></td>    
        <td class="text-center"><input type="text" class="form-control" size="3" maxlength="3" value="<%$ytvideo.v_order%>" name="VID[<%$ytvideo.v_videoid%>][v_order]"></td>
