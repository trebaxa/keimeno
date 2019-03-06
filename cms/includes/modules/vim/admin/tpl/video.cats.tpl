<h3>Video Kategorien</h3>
<div class="btn-group">
<a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=videolist&section=videomanager">Videos verwalten</a>
  <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=showall&section=cats">{LA_SHOWALL}</a>
  <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=catadd&section=cats">{LA_ADDCATEGORY}</a>
</div>

<% if ($aktion=="showall") %>
<% include file="video.cattree.tpl" %>
<%/if%>

<% if ($cmd=="catadd" || $aktion=="catedit") %>


<form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
<div style="width:600px">
<fieldset>	
<legend>{LA_ADDEDITCATEGORY}</legend>
<table>
<tr>
	<td class="label">Name<span class="redimportant">*</span>:</td>
	<td><input type="text" class="form-control" name="FORM[ytc_name]" value="<% $VIM.CATOBJ.ytc_name|sthsc %>" size="30">
	      <% if ($POST.FORM.ytc_name=="" && $VIM.fault_form==TRUE) %><span class="redimportant">{LA_MISSED}</span><%/if%>
	      </td>
</tr>
<tr>
	<td class="label">Theme Color:</td>
	<td>
        #<input type="text" class="form-control" name="FORM[ytc_color]" value="<% $VIM.CATOBJ.ytc_color|sthsc %>" maxlength="6" size="6">	     
    </td>
</tr>
<tr>
	<td class="label">Position:</td>
	<td>
	<select class="form-control" name="FORM[ytc_parent]" >

		<option value="0"> - ROOT - </option>

	<%$VIM.cat_selectbox%>
	</select></td>
</tr>
<tr>
	<td class="label">{LA_APPROVED}:</td>
	<td>
		<input <% if ($VIM.CATOBJ.ytc_approval==1) %>checked<%/if%> type="radio" name="FORM[ytc_approval]" value="1">{LBL_YES}
		<input <% if ($VIM.CATOBJ.ytc_approval==0) %>checked<%/if%> type="radio" name="FORM[ytc_approval]" value="0">{LBL_NO}
	</td>
</tr>


<% if ($VIM.CATOBJ.ytc_path!="") %>
<tr>
	<td class="label">Path:</td>
	<td><% $VIM.CATOBJ.ytc_path %></td>
</tr>
<%/if%>
</table>


<div class="subright"><%$subbtn%></div>
</fieldset>	
</div>
  <input type="hidden" name="aktion" value="cat_savecat">
  <input type="hidden" name="id" value="<%$REQUEST.id%>">
	<input type="hidden" name="epage" value="<%$epage%>">
	<input type="hidden" name="orgaktion" value="<%$aktion%>">	
	<input type="hidden" name="section" value="<%$REQUEST.section%>">
</form>


<div class="clear"></div>
<%/if%>
