<h1>Upload</h1>

<form method="post" action="<%$meta.uri%>" enctype="multipart/form-data">
<table>
 <tr>
     <td>Titel</td>
     <td><input type="text" name="FORM[title]" value=""></td>     
 </tr>
 <tr>
     <td>Video</td>
     <td><input type="file" name="datei"></td>     
 </tr> 
 </table>
<input type="hidden" name="cmd" value="upload_video">
<input type="hidden" name="returnto" value="<%$meta.uri%>">
<%$subbtn%>
</form>
