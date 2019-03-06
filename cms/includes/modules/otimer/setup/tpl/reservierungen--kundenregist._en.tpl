<div class="infobox">
<h2>Ihr Termin: <%$PROG.pr_title%></h2>
<%$appointment.timefrom.datime_ger%> - <%$appointment.timeto.time.time%>

</div>
<form role="form" action="<%$PHPSELF%>" method="post" id="orderform" name="orderform" onSubmit="return valform()" enctype="multipart/form-data">
    <input type="hidden" name="aktion" value="kreg"> 
    <input type="hidden" name="page" value="<% $otimer.page %>">
<h2>Kontaktdaten</h2>
    <table class="tab_std" width="100%" >
        <tbody>
            <tr>
                <td width="31%">{LBL_ANREDE}:</td>
                <td><select class="form-control" name="FORM[geschlecht]"><%$anredeselect%></select></td>
            </tr>
               
            <tr>
                <td width="31%">{LBL_NACHNAME}*:</td>
                <td><input class="text" size="26" name="FORM_NOTEMPTY[nachname]" value="<% $kregform.nachname %>" class="form-control" type="text">
<% if ($kregform_err.nachname!='') %><span class="important"><% $kregform_err.nachname %></span><% /if %>
               </td>
            </tr>
            <tr>
                <td width="31%">{LBL_VORNAME}*:</td>
                <td><input class="text" name="FORM_NOTEMPTY[vorname]" value="<% $kregform.vorname %>" class="form-control" type="text">
<% if ($kregform_err.vorname!='') %>                
<span class="important"><% $kregform_err.vorname %></span>
<% /if %>
</td>
            </tr>
            
         
            <tr>
                <td width="31%">{LBL_STRASSE}*:</td>
                <td><input class="text" name="FORM_NOTEMPTY[strasse]" value="<% $kregform.strasse %>" class="form-control" type="text">
<% if ($kregform_err.strasse!='') %>                
<span class="important"><% $kregform_err.strasse%></span>
<% /if %>
</td>
            </tr>
            <tr>
                <td width="31%">{LBL_PLZ}*:</td>
                <td><input class="text" id="plz" size="10" name="FORM_NOTEMPTY[plz]" value="<% $kregform.plz %>" class="form-control" type="text">
<% if ($kregform_err.plz!='') %>                
<span class="important"><% $kregform_err.plz %></span>
<% /if %></td>
            </tr>
            <tr>
                <td width="31%">{LBL_ORT}*:</td>
                <td><input class="text" id="ort" size="26" name="FORM_NOTEMPTY[ort]" value="<% $kregform.ort %>" class="form-control" type="text">
<% if ($kregform_err.ort!='') %>                
<span class="important"><% $kregform_err.ort%></span>
<% /if %></td>
            </tr>
            <tr>
                <td width="22%">{LBL_LAND}:</td>
                <td width="78%" ><select class="form-control" name="FORM[land]" size="-1"><%$countrys%></select></td>
            </tr>
            <tr>
                <td width="31%">{LBL_TELEFON}*:</td>
                <td><input class="text" size="18" name="FORM_NOTEMPTY[tel]" value="<% $kregform.tel %>" class="form-control" type="text">
<% if ($kregform_err.tel !='') %>                
<span class="important"><% $kregform_err.tel %></span>
<% /if %></td>
            </tr>
        
            <tr>
                <td width="31%">Email*:</td>
                <td><input class="text" size="26" autocomplete="OFF" name="FORM[email]" value="<% $kregform.email %>" class="form-control" type="text">
<% if ($kregform_err.email!='') %>                
<span class="important"><% $kregform_err.email%></span>
<% /if %>
</td>
            </tr>
           
      
            <tr>
                <td width="31%">{LBL_NEWSLETTER}:</td>
                <td><input <% if ($kregform.mailactive==1) %> checked <% /if %> type="checkbox" name="FORM[mailactive]" value="1">{LBL_NEWSACTIVE}</td>
            </tr>
        </tbody>
    </table>
    

     
    <br>

<script type="text/javascript">
<!--
function valform() {
 if (document.orderform.agbtrue.checked) {
 return true;
} else {
 alert('{LBL_AGBNOTCHECKED}');
 return false;
}
}
// -->
</script>
<div class="infobox"><input type="checkbox" id="agbtrue" name="agbtrue" value="1">
{LBL_AFBREAD}
</div> 
    <% html_subbtn class="btn btn-primary" value="{LBL_RESERV}" %>


</form>
<br>
