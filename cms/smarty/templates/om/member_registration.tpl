<form action="<% $PHPSELF %>" method="post" id="orderform" name="orderform" onSubmit="return valform()" enctype="multipart/form-data">
    <input name="aktion" type="hidden" value="<% $kreg_aktion %>">
    <input type="hidden" name="page" value="<% $page %>">
    <input type="hidden" name="token" value="<% $cms_token%>">
<h2>Kontaktdaten</h2>
    <table class="tab_std" width="100%" >
        <tbody>
            <tr>
                <td width="31%">{LBL_ANREDE}:</td>
                <td><select name="FORM[geschlecht]"><% $kregform.salutselect %></select></td>
            </tr>
      <tr>
                <td width="31%">{LBL_FIRMENNAME}*:</td>
                <td><input class="text" name="FORM_NOTEMPTY[firma]" value="<% $kregform.firma %>" type="text">
                <% if ($kregform_err.firma!='') %><span class="important"><% $kregform_err.firma %></span><% /if %>

                </td>
            </tr>            
            <tr>
                <td width="31%">{LBL_NACHNAME}*:</td>
                <td><input class="text" size="26" name="FORM_NOTEMPTY[nachname]" value="<% $kregform.nachname %>" type="text">
<% if ($kregform_err.nachname!='') %><span class="important"><% $kregform_err.nachname %></span><% /if %>
               </td>
            </tr>
            <tr>
                <td width="31%">{LBL_VORNAME}*:</td>
                <td><input class="text" name="FORM_NOTEMPTY[vorname]" value="<% $kregform.vorname %>" type="text">
<% if ($kregform_err.vorname!='') %>                
<span class="important"><% $kregform_err.vorname %></span>
<% /if %>
</td>
            </tr>
         <tr>
                <td width="31%">{TMPL_CFL_1}:</td>
                <td>{TMPL_CFI_1}
                <% if ($kregform_err.CF_1!='') %>                
<span class="important"><% $kregform_err.CF_1 %></span>
<% /if %>
</td>
            </tr>     
      </tbody>
      </table>
      <h2>{LBL_FIRMENNAME} {LBL_ADDRESS}</h2>
  <table class="tab_std" width="100%" >
        <tbody>      
            <tr>
                <td width="31%">{LBL_STRASSE}*:</td>
                <td><input class="text" name="FORM_NOTEMPTY[strasse]" value="<% $kregform.strasse %>" type="text">
<% if ($kregform_err.strasse!='') %>                
<span class="important"><% $kregform_err.strasse%></span>
<% /if %>
</td>
            </tr>
            <tr>
                <td width="31%">{LBL_PLZ}*:</td>
                <td><input class="text" id="plz" size="10" name="FORM_NOTEMPTY[plz]" value="<% $kregform.plz %>" type="text">
<% if ($kregform_err.plz!='') %>                
<span class="important"><% $kregform_err.plz %></span>
<% /if %></td>
            </tr>
            <tr>
                <td width="31%">{LBL_ORT}*:</td>
                <td><input class="text" id="ort" size="26" name="FORM_NOTEMPTY[ort]" value="<% $kregform.ort %>" type="text">
<% if ($kregform_err.ort!='') %>                
<span class="important"><% $kregform_err.ort%></span>
<% /if %></td>
            </tr>
            <tr>
                <td width="22%">{LBL_LAND}:</td>
                <td width="78%"><select name="FORM[land]" size="1"><% $kregform.countryselect %></select></td>
            </tr>
            <tr>
                <td width="31%">{LBL_TELEFON}*:</td>
                <td><input class="text" size="18" name="FORM_NOTEMPTY[tel]" value="<% $kregform.tel %>" type="text">
<% if ($kregform_err.tel !='') %>                
<span class="important"><% $kregform_err.tel %></span>
<% /if %></td>
            </tr>
            <tr>
                <td width="31%">{LBL_FAX}:</td>
                <td><input class="text" size="18" name="FORM[fax]" value="<% $kregform.fax %>" type="text"></td>
            </tr>
            <tr>
                <td width="31%">Email*:</td>
                <td><input class="text" size="26" autocomplete="OFF" name="FORM[email]" value="<% $kregform.email %>" type="text">
<% if ($kregform_err.email!='') %>                
<span class="important"><% $kregform_err.email%></span>
<% /if %>
</td>
            </tr>      
            <tr>
                <td width="31%">Login Email*:</td>
                <td><input class="text" size="26" name="FORM[email_notpublic]" value="<% $kregform.email_notpublic %>" type="text">
                <% if ($kregform_err.email_notpublic!='') %>                
<span class="important"><% $kregform_err.email_notpublic%></span>
<% /if %>
</td>
            </tr> 
            <tr>
                <td width="31%">Homepage:</td>
                <td>http://<input class="text" size="26" name="FORM[homepage]" value="<% $kregform.homepage %>" type="text"></td>
            </tr>
            <tr>
                <td width="31%">{LBL_PASSWORT}*:</td>
                <td><input class="text" type="password" size="26" name="FORM[passwort]" value="<% $kregform.passwort %>">
<% if ($kregform_err.passwort!='') %><span class="important"><% $kregform_err.passwort%></span><% /if %>
</td>
            </tr>
            <% if ($gbl_config.newsletter_disable_unreg==0) %>
            <tr>
                <td width="31%">{LBL_NEWSLETTER}:</td>
                <td><input <% if ($kregform.mailactive==1) %> checked <% /if %> type="checkbox" name="FORM[mailactive]" value="1">{LBL_NEWSACTIVE}</td>
            </tr>
            <%/if%>
<% if ($gbl_config.captcha_active==1) %>
            <tr>
                <td width="10%">{LBL_SECODE}:</td>
                <td width="90%"><img title="{LBL_SECODE}" alt=""  src="<%$PATH_CMS%>captcha.php"> <br>
                {LBL_CODEENTER}:<input size="6" name="securecode" type="text">
                      <% if ($kregform_err.securecode!='') %>                
<span class="important"><% $kregform_err.securecode%></span>
<% /if %>
                </td>
            </tr>
<% /if %>            
        </tbody>
    </table>
   
    
<h1> Bankverbindung   </h1>


 <table class="tab_std" width="100%" >
        <tbody>
             <tr>
                <td width="31%">Konto*:</td>
                <td><input class="text" size="26" name="FORM_NOTEMPTY[konto]" value="<% $kregform.konto %>" type="text">
<% if ($kregform_err.konto!='') %> <span class="important"><% $kregform_err.konto %></span><% /if %>
               </td>
            </tr> 
             <tr>
                <td width="31%">BLZ*:</td>
                <td><input class="text" size="26" name="FORM_NOTEMPTY[blz]" value="<% $kregform.blz %>" type="text">
<% if ($kregform_err.blz !='') %> <span class="important"><% $kregform_err.blz %></span><% /if %>
               </td>
            </tr> 
            <tr>
                <td width="31%">Bank*:</td>
                <td><input class="text" size="26" name="FORM_NOTEMPTY[bank]" value="<% $kregform.bank %>" type="text">
<% if ($kregform_err.bank!='') %> <span class="important"><% $kregform_err.bank %></span><% /if %>
               </td>
            </tr>   
    </tbody>
    </table> 
<h2>{LBL_YOURFOTO}</h2>
<% include file="fileupload.tpl" %>
<h2>Themen</h2>                
<% foreach from=$member_collections item=colg name=cgloop %>
 <br> <h3><% $colg.col_name %></h3>
 <table class="tab_std"  width="100%">
 <tr><td valign="top">
 <% if ($colg.col_id==1) %>
   <% assign var=line_break value=8 %>
  <% else %>
  <% assign var=line_break value=11 %>
  <% /if %>
<% foreach from=$colg.groups item=group name=gloop %>
<input type="checkbox" <% $group.checked %> name="MEMBERGROUPSCOL[<% $group.gid %>_<% $colg.col_id%>]" value="<% $group.gid %>_<% $colg.col_id%>"> <% $group.groupname %><br>
 <% if $smarty.foreach.gloop.iteration % $line_break == 0 %></td><td valign="top"><% /if %>
<% /foreach %></td></tr>
</table>
<% /foreach %>
 
     
    <br>
<% if ($kreg_aktion=='insert') %>
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
    <br><% html_subbtn class="sub_btn" value="{LBL_REGISTER}" %>
<% /if %>
<% if ($kreg_aktion=='update') %>
    <% html_subbtn class="sub_btn" value="{LBL_BTN_SAVE}" %>
<% /if %>
</form>

<% if ($customer.kid>0) %>
<% include file="tw_account.tpl" %>
<%/if%>

<br>
