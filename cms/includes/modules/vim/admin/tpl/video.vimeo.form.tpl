<input type="hidden" name="YTOPTIONS[vi_stock]" value="VI">
 <table  >
     <tr>
     <td>{LA_ORDER}:</td> 
     <td>
      <select class="form-control custom-select" name="YTOPTIONS[orderby]">
        <option <% if ($VIM.query.qobj.orderby=='newest') %>selected<%/if%> value="newest">newest</option>
        <option <% if ($VIM.query.qobj.orderby=='oldest') %>selected<%/if%> value="oldest">oldest</option>
        <option <% if ($VIM.query.qobj.orderby=='most_played') %>selected<%/if%> value="most_played">most_played</option>
        <option <% if ($VIM.query.qobj.orderby=='most_commented') %>selected<%/if%> value="most_commented" >most_commented</option>
        <option <% if ($VIM.query.qobj.orderby=='most_liked') %>selected<%/if%> value="most_liked" >most_liked</option>
        <option <% if ($VIM.query.qobj.orderby=='relevant') %>selected<%/if%> value="relevant" >relevant</option>
      </select>
      </td>
     </tr>    
     <tr>
     <td>{LA_VISEARCHTYPE}:</td> 
     <td>
      <select class="form-control custom-select" name="FORM[vp_vitype]" id="vi_typeselect" onChange="vm_type_change(this.options[this.selectedIndex].value);">
        <option <% if ($VIM.query.vp_vitype=='SER') %>selected<%/if%> value="SER">search by term</option>
        <option <% if ($VIM.query.vp_vitype=='AUT') %>selected<%/if%> value="AUT">search by author</option>
        <option <% if ($VIM.query.vp_vitype=='TAG') %>selected<%/if%> value="TAG">search by tag</option>
      </select>
      </td>
     </tr>  
          
   <tr>
     <td>{LA_YTMAXRESEULTS}:</td> 
     <td>   
      <select class="form-control custom-select" name="YTOPTIONS[maxResults]">   
     <option <% if ($VIM.query.qobj.maxResults==50) %>selected<%/if%> value="50">50</option>
     <option <% if ($VIM.query.qobj.maxResults==40) %>selected<%/if%> value="40">40</option>
     <option <% if ($VIM.query.qobj.maxResults==30) %>selected<%/if%> value="30">30</option>
     <option <% if ($VIM.query.qobj.maxResults==20) %>selected<%/if%> value="20">20</option>
     <option <% if ($VIM.query.qobj.maxResults==10) %>selected<%/if%> value="10">10</option>
      </select>
     </td>
     </tr>
   <tr>
     <td>{LA_YTTOTALLIMIT}:</td> 
     <td>   
      <select class="form-control custom-select" name="YTOPTIONS[maxTotalLimit]">   
     <option <% if ($VIM.query.qobj.maxTotalLimit=='100') %>selected<%/if%> value="100">100</option>
     <option <% if ($VIM.query.qobj.maxTotalLimit=='250') %>selected<%/if%> value="250">250</option>
     <option <% if ($VIM.query.qobj.maxTotalLimit=='500') %>selected<%/if%> value="500">500</option>
     <option <% if ($VIM.query.qobj.maxTotalLimit=='800') %>selected<%/if%> value="800">800</option>
     <option <% if ($VIM.query.qobj.maxTotalLimit=='1000') %>selected<%/if%> value="1000">1000</option>
      </select>
     </td>
     </tr>     
   <tr id="tr_SER">
     <td>{LA_YTSEARCHTERMS}<span class="redimportant">*</span>:</td> 
     <td>            
      <input id="if_SER" name="YTOPTIONS[searchTerm]" type="text" class="form-control" value="<% $VIM.query.qobj.searchTerm|sthsc %>">
      <% if ($POST.YTOPTIONS.searchTerm=="" && $VIM.fault_form==TRUE) %><span class="redimportant">{LA_MISSED}</span><%/if%>
 		</td>
		</tr>      
		<tr id="tr_AUT">
     <td width="260">Author:</td> 
     <td>            
      <input id="if_AUT" name="FORM[vp_author]" type="text" class="form-control" value="<% $VIM.query.vp_author|sthsc %>">
 		</td>
		</tr>   
		
		   		
</table>

<script>
var vi_type=$("#vi_typeselect").val();
vm_type_change(vi_type);
function vm_type_change(vi_type) {
 if (vi_type=='AUT') {
		 $("#tr_SER").fadeOut();
		 $("#tr_AUT").fadeIn();
		 $("#if_SER").val('AUTHOR');
 }
 if (vi_type=='SER' || vi_type=='TAG') {
		// $("#if_SER").val('');
		 $("#tr_SER").fadeIn();
		 $("#tr_AUT").fadeOut();
 } 

}
</script>