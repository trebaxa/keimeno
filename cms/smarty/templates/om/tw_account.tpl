<h2>Twitter</h2>
<% if ($aktion=='settings') %>
<h3>Einstellungen<h3>
<form action="<% $HTA_CMSFIXLINKS.GB_URL %>" method="post">
<input type="hidden" name="aktion" value="save_account">
<input type="hidden" name="page" value="9940">
<fieldset>
<legend>Twitter Seceret Keys</legend>
<table  class="tab_std">
<tr>
    <td>Consumer key</td>
    <td><input size="31" type="text" name="FORM[tw_consumerkey]" value="<%$customer.tw_consumerkey|hsc%>" autocomplete="off"></td>
</tr>
<tr>
    <td>Consumer secret</td>
    <td><input size="31" type="text" name="FORM[tw_consumersecret]" value="<%$customer.tw_consumersecret|hsc%>" autocomplete="off"></td>
</tr>
<tr>
    <td>Callback Link</td>
    <td>http://www.<%$meta.domain%><% $HTA_CMSFIXLINKS.GC_URL %></td>
</tr>

</table>
<div class="subright">
<% html_subbtn class="sub_btn" value="speichern" %>
</div>
</fieldset>
</form>
<div class="infobox">
Konfigurieren Sie hier Ihren Twitter Account, um BeitrÃ¤ge aus dem Portal in Ihrer Twitter Timeline zu verÃ¶ffentlichen.

</div>
<%/if%>

<% if ($customer.tw_consumerkey=="" || $customer.tw_consumersecret=="") %>
<div class="infobox">
Sie benÃ¶tigen einen "Consumer Key" und einen "Secret Key".
Registieren Sie unser Portal einfach als Application in Ihrem Twitter Account. <br>
Mehr Infos unter: <a href="https://twitter.com/apps">https://twitter.com/apps</a><br>
<br>Mit unserer Twitterschnittstelle kÃ¶nnen Sie direkt Nachrichten aus dem Portal in Ihren Twitterstream senden.
</div>
<%else%>
<% if ($TW.connected==FALSE) %>
 <div class="infobox">Sie sind nicht in Twitter eingeloggt.
 <br><a href="<%$PHPSELF%>?page=9940&aktion=tw_connect">Jetzt verbinden</a>
  | <a href="<%$PHPSELF%>?page=9940&aktion=settings">Zugang konfigurieren</a></div>
<%/if%>


<%/if%>

<% if ($TW.connected==TRUE) %>
<hr>
<h3>Status aktualisieren<h3>
<form action="<%$PHPSELF%>" method="post">
<input type="hidden" name="aktion" value="tw_post_status">
<input type="hidden" name="page" value="9940">
<input type="hidden" name="comingfrom" value="<%$HTTP_REFERER%>">
<fieldset>
<legend>Status Update</legend>
<textarea rows="3" cols="60" name="FORM[twstatus]"><%$POST.twstatus|hsc%></textarea>
<div class="subright">
<% html_subbtn class="sub_btn" value="an Twitter senden" %>
</div>
</fieldset>
</form>

<a href="<%$PHPSELF%>?page=9940&aktion=tw_clearsession">Verbindung trennen</a>
<%/if%>
