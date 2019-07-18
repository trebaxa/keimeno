<input type="hidden" name="YTOPTIONS[vi_stock]" value="YT">
 <table   class="table table-striped table-hover">
     <tr>
     <td width="300">{LA_YTSECTION}:</td> 
     <td>
      <select class="form-control custom-select" name="YTOPTIONS[queryType]">
        <option <% if ($VIM.YTOPTIONS.queryType=='all') %>selected<%/if%> value="all" selected="true">All Videos</option>
        <option <% if ($VIM.YTOPTIONS.queryType=='top_rated') %>selected<%/if%> value="top_rated">Top Rated Videos</option>
        <option <% if ($VIM.YTOPTIONS.queryType=='most_viewed') %>selected<%/if%> value="most_viewed">Most Viewed Videos</option>
        <option <% if ($VIM.YTOPTIONS.queryType=='recently_featured') %>selected<%/if%> value="recently_featured">Recently Featured Videos</option>
        <option <% if ($VIM.YTOPTIONS.queryType=='most_recent') %>selected<%/if%> value="most_recent">Most Recent</option>
        <option <% if ($VIM.YTOPTIONS.queryType=='most_responded') %>selected<%/if%> value="most_responded">Most Responded</option>
        <option <% if ($VIM.YTOPTIONS.queryType=='top_favorites') %>selected<%/if%> value="top_favorites">Top Favorites</option>
        <option <% if ($VIM.YTOPTIONS.queryType=='most_discussed') %>selected<%/if%> value="most_discussed">Most Discussed</option>
      </select>
      </td>
     </tr>
     <tr>
     <td>{LA_YTCATS}:</td> 
     <td>
      <select class="form-control custom-select" name="YTOPTIONS[ytcat]">
      <option value="">- {LA_SEARCHINALLCATEGORIES} -</option>
        	<% foreach from=$YT.yt_cats item=ytcat %>	
        <option <% if ($VIM.YTOPTIONS.ytcat==$ytcat.term) %>selected<%/if%> value="<%$ytcat.term%>"><% $ytcat.label %></option>
        <%/foreach%>
      </select>
      </td>
     </tr>     
     
     <tr>
     <td>{LA_ORDER}:</td> 
     <td>
      <select class="form-control custom-select" name="YTOPTIONS[orderby]">
        <option <% if ($VIM.YTOPTIONS.orderby=='relevance' || $VIM.YTOPTIONS.orderby=='') %>selected<%/if%> value="relevance">relevance</option>
        <option <% if ($VIM.YTOPTIONS.orderby=='viewCount') %>selected<%/if%> value="viewCount">viewCount</option>
        <option <% if ($VIM.YTOPTIONS.orderby=='published') %>selected<%/if%> value="published">published</option>
        <option <% if ($VIM.YTOPTIONS.orderby=='rating') %>selected<%/if%> value="rating" >rating</option>
      </select>
      </td>
     </tr>    

     <tr>
     <td>{LA_TIMEBACK}:</td> 
     <td>
      <select class="form-control custom-select" name="YTOPTIONS[time]">
        <option <% if ($VIM.YTOPTIONS.time=='all_time' || $VIM.YTOPTIONS.time=='') %>selected<%/if%> value="all_time" >all_time</option>
        <option <% if ($VIM.YTOPTIONS.time=='today') %>selected<%/if%> value="today">today</option>
        <option <% if ($VIM.YTOPTIONS.time=='this_week') %>selected<%/if%> value="this_week">this_week</option>
        <option <% if ($VIM.YTOPTIONS.time=='this_month') %>selected<%/if%> value="this_month">this_month </option>
      </select>
      </td>
     </tr> 
      
   <tr>
     <td>{LA_YTMAXRESEULTS}:</td> 
     <td>   
      <select class="form-control custom-select" name="YTOPTIONS[maxResults]">   
     <option <% if ($VIM.YTOPTIONS.maxResults==50) %>selected<%/if%> value="50">50</option>
     <option <% if ($VIM.YTOPTIONS.maxResults==40) %>selected<%/if%> value="40">40</option>
     <option <% if ($VIM.YTOPTIONS.maxResults==30) %>selected<%/if%> value="30">30</option>
     <option <% if ($VIM.YTOPTIONS.maxResults==20) %>selected<%/if%> value="20">20</option>
     <option <% if ($VIM.YTOPTIONS.maxResults==10) %>selected<%/if%> value="10">10</option>
      </select>
     </td>
     </tr>
   <tr>
     <td>{LA_YTTOTALLIMIT}:</td> 
     <td>   
      <select class="form-control custom-select" name="YTOPTIONS[maxTotalLimit]">   
     <option <% if ($VIM.YTOPTIONS.maxTotalLimit=='100') %>selected<%/if%> value="100">100</option>
     <option <% if ($VIM.YTOPTIONS.maxTotalLimit=='250') %>selected<%/if%> value="250">250</option>
     <option <% if ($VIM.YTOPTIONS.maxTotalLimit=='500') %>selected<%/if%> value="500">500</option>
     <option <% if ($VIM.YTOPTIONS.maxTotalLimit=='800') %>selected<%/if%> value="800">800</option>
     <option <% if ($VIM.YTOPTIONS.maxTotalLimit=='1000') %>selected<%/if%> value="1000">1000</option>
      </select>
     </td>
     </tr>     
   <tr>
     <td>{LA_YTSEARCHTERMS}:</td> 
     <td>            
      <input name="YTOPTIONS[searchTerm]" type="text" class="form-control" value="<% $VIM.YTOPTIONS.searchTerm|sthsc %>">
      <% if ($POST.YTOPTIONS.searchTerm=="" && $VIM.YTOPTIONS.vp_author=="" && $VIM.fault_form==TRUE) %><span class="redimportant">{LA_MISSED}</span><%/if%>
 		</td>
		</tr>   
		<tr>
     <td>Author:</td> 
     <td>            
      <input name="YTOPTIONS[vp_author]" type="text" class="form-control" value="<% $VIM.YTOPTIONS.vp_author|sthsc %>">
 		</td>
		</tr> 		   
		<tr>
     <td>{LA_YTSEARCHTERMSEXCLUDE}:</td> 
     <td>            
      <input name="YTOPTIONS[excludeTerms]" type="text" class="form-control" value="<% $VIM.YTOPTIONS.excludeTerms|sthsc %>">
      <br><span class="small">{LA_KOMMASEPARATED}</span>
 		</td>
		</tr>  		   		
</table>