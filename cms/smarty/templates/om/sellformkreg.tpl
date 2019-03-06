<div class="zwcontstd">
<% if ($CU_LOGGEDIN==false) %>
 <table class="tab_std" width="100%" >
        <tbody>
            <tr>
                <td width="31%">{LBL_ANREDE}:</td>
                <td><select name="FORMCUST[geschlecht]"><%$SELLFORM.PAY_OBJ.anredeform.anrede_select%></select></td>
            </tr>
            <tr>
                <td width="31%">{LBL_NACHNAME}*:</td>
                <td><input class="text" size="26" name="FORMCUST[nachname]" value="<% $SELLFORM.PAY_OBJ.formcust.nachname|hsc %>" type="text">
<% if ($kregform_err.nachname!='') %><span class="important"><% $kregform_err.nachname %></span><% /if %>
               </td>
            </tr>
            <tr>
                <td width="31%">{LBL_VORNAME}*:</td>
                <td><input class="text" name="FORMCUST[vorname]" value="<% $SELLFORM.PAY_OBJ.formcust.vorname|hsc %>" type="text">
<% if ($kregform_err.vorname!='') %>                
<span class="important"><% $kregform_err.vorname %></span>
<% /if %>
</td>
            </tr>
            <tr>
                <td width="31%">{LBL_STRASSE}/Hausnr.*:</td>
                <td><input class="text" name="FORMCUST[strasse]" value="<% $SELLFORM.PAY_OBJ.formcust.strasse|hsc %>" type="text">
                /<input class="text" name="FORMCUST[hausnr]" size="5" maxlength="10" value="<% $SELLFORM.PAY_OBJ.formcust.hausnr|hsc %>" type="text">
<% if ($kregform_err.strasse!='') %>                
<span class="important"><% $kregform_err.strasse%></span>
<% /if %>
</td>
            </tr>
            <tr>
                <td width="31%">{LBL_PLZ}*:</td>
                <td><input class="text" id="plz" size="10" name="FORMCUST[plz]" value="<% $SELLFORM.PAY_OBJ.formcust.plz|hsc %>" type="text">
<% if ($kregform_err.plz!='') %>                
<span class="important"><% $kregform_err.plz %></span>
<% /if %></td>
            </tr>
            <tr>
                <td width="31%">{LBL_ORT}*:</td>
                <td><input class="text" id="ort" size="26" name="FORMCUST[ort]" value="<% $SELLFORM.PAY_OBJ.formcust.ort|hsc %>" type="text">
<% if ($kregform_err.ort!='') %>                
<span class="important"><% $kregform_err.ort%></span>
<% /if %></td>
            </tr>
            <tr>
                <td width="22%">{LBL_LAND}:</td>
                <td width="78%">
                <select name="FORMCUST[land]" size="1">
                <%$SELLFORM.PAY_OBJ.land_select%>
                </select></td>
            </tr>
         
    
            <tr>
                <td width="31%">Email*:</td>
                <td><input class="text" size="26" autocomplete="OFF" name="FORMCUST[email]" value="<% $SELLFORM.PAY_OBJ.formcust.email|hsc %>" type="text">
<% if ($kregform_err.email!='') %>                
<span class="important"><% $kregform_err.email%></span>
<% /if %>
</td>
            </tr>      
            <tr>
                <td width="31%">{LBL_PASSWORT}*:</td>
                <td><input class="text" type="password" size="26" name="FORMCUST[passwort]" value="<% $SELLFORM.PAY_OBJ.formcust.passwort %>">
<% if ($kregform_err.passwort!='') %><span class="important"><% $kregform_err.passwort%></span><% /if %>
</td>
            </tr>

                </td>
            </tr>
           
        </tbody>
    </table>
<%else%>  
<% $SELLFORM.PAY_OBJ.formcust.vorname %> <% $SELLFORM.PAY_OBJ.formcust.nachname%> <br>
<% $SELLFORM.PAY_OBJ.formcust.strasse%> <% $SELLFORM.PAY_OBJ.formcust.hausnr %> <br>
<% $SELLFORM.PAY_OBJ.formcust.plz%> <% $SELLFORM.PAY_OBJ.formcust.ort%> 
<%/if%>
</div>
