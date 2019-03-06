<style type="text/css">
/*<![CDATA[*/
  @import url(<%$PATH_CMS%>js/milkbox/milkbox.css);
 #uploadblock{
  position: absolute;
  left: 50%;
  top: 100px;
  margin-left: -215px;
  width:430px;
  padding: 12px; 
  text-align: left;
  display: none;
  visibility:hidden;
    border:3px solid #E0462E; 
      color: black;
  background-color: #EFEFEF;  
  -moz-border-radius: 10px;
 }  
 #loginblock{
  position: absolute;
  left: 50%;
  top: 100px;
  margin-left: -215px;
  width:430px;
  padding: 12px; 
  text-align: left;
  display: none;
  visibility:hidden;
    border:3px solid #E0462E; 
      color: black;
  background-color: #EFEFEF;  
  -moz-border-radius: 10px;
 }  
/*]]>*/
</style>

<% if ($customer.PERMOD.gallery.add==true) %>

<% if ($customer.kid>0 && $customer.PERMOD.gallery.add==true) %>
<div style="width:100%;text-align:right;">
<a href="javascript:showDivTag('uploadblock')">{LBL_CSUPLOADLINK}</a>
</div>
<%else%>
<div style="width:100%;text-align:right;">
<a href="javascript:showDivTag('loginblock')">{LBL_CSUPLOADLINK}</a>
</div>
<%/if%>
<%/if%>

<div id="loginblock">
<div style="width:100%;text-align:right;"><a href="#" title="schliessen" onClick="dc_closeLink(this);"><img src="<%$PATH_CMS%>images/destroy.png" ></a></div>
Sie müssen angemeldet sein, um ein Foto hochzuladen.

</div>


<div id="uploadblock">
<div style="width:100%;text-align:right;"><a href="#" title="schliessen" onClick="dc_closeLink(this);"><img src="<%$PATH_CMS%>images/destroy.png" ></a></div>
<% if ($customer.kid>0) %>
<form role="form" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">

       <% if ($EDITOR.pic_id>0) %>
            <img  src="<%$EDITOR.img_src%>" alt="">
       <%/if%>
    <table width="100%" >
        <tr>
           <td>
            <% if ($gallery_obj.albumcount>1) %>
            <strong>Album:</strong><br>
            <%$gallery_obj.album_select%>
            <%else%>
             <input type="hidden" name="FORM[group_id]" value="<%$gallery_id%>">
            <%/if%>
            </td>
        </tr>
        <tr>
            <td>
            <strong>Datei:</strong><br>
            <input type="file" name="attfile" size="21" value="search">
 
            </td>
        </tr>
        <tr>
            <td>
            <strong>Titel:</strong><br>
            <input class="form-control" type="text" name="FORM[pic_title]" size="30" maxlength="30" value="<%$EDITOR.FORM.pic_title|hsc%>"></td>
        </tr>
      <tr>
            <td>
            <strong>Quelle:</strong><br>
            <input class="form-control" type="text" name="FORM[fotoquelle]" size="30" maxlength="30" value="<%$EDITOR.FORM.fotoquelle|hsc%>"></td>
        </tr>        
        <tr>
            <td>
            <strong>Foto Beschreibung:</strong><br>
            <textarea class="form-control"  rows="3" cols="60" name="FORM[pic_desc]"><%$EDITOR.FORM.pic_desc|hsc%></textarea></td>
        </tr>
        
        <tr>
        <td>
           <% if ($EDITOR.pic_id>0) %>
    <input type="submit" class="btn btn-primary" value="speichern">
    <%else%>
    <input type="submit" class="btn btn-primary" value="{LBL_UPLOAD}">
    <%/if%>
        </td>
        </tr>
    </table>
    <input type="hidden" name="FORM_CON_ID" value="<%$EDITOR.FORM_CON.id%>">
    <input type="hidden" name="aktion" value="uploadpic">
    <input type="hidden" name="start" value="<%$paging.start%>">
    <input type="hidden" name="FORM_CON[lang_id]" value="1">
    <input type="hidden" name="FORM_CON[pic_id]" value="<%$EDITOR.pic_id%>">
    <input type="hidden" value="<%$page%>" name="page">
    <input type="hidden" value="<%$gallery_id%>" name="gid">
 
</form>

<%/if%>
</div>


<script>
//<![CDATA[
<% if ($aktion=='edit') %> 
showDivTag('uploadblock');
<%else%>
hideDivTag('loginblock'); 
hideDivTag('uploadblock'); 
<%/if%>
//]]>
</script>
