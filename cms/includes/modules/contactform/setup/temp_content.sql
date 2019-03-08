INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<section id=\"contact\">\r\n  <div class=\"container\">\r\n     <h2><%$contact.cf_title%></h2>\r\n     <p class=\"lead\"><%$contact.cf_lead%></p>\r\n    <div class=\"fmrcontactthx\" style=\"display:none\">\r\n        <h2><%$contact.cf_thanks_title%></h2>    \r\n        <p><%$contact.cf_thanks%></p>\r\n    </div>\r\n\r\n<div class=\"row\">\r\n  <div class=\"col-md-8\">\r\n    <form action=\"<% $PHPSELF %>\" method=\"post\" enctype=\"multipart/form-data\" class=\"jsonform fmrcontactform\">\r\n    <input name=\"token\" type=\"hidden\" value=\"<% $cms_token %>\">   \r\n        <input type=\"hidden\" name=\"page\" value=\"<% $page %>\">\r\n        <input type=\"hidden\" name=\"cmd\" value=\"sendmsg\">\r\n        <input type=\"hidden\" name=\"ajaxsubmit\" value=\"1\">\r\n        <input type=\"hidden\" name=\"cont_matrix_id\" value=\"<%$cont_matrix_id%>\">\r\n        <input type=\"hidden\" value=\"\" name=\"email\" class=\"hidden\">\r\n        <div class=\"row\">\r\n            <div class=\"col-md-6\">\r\n                <div class=\"form-group\">\r\n                    <label for=\"vorname\"  class=\"sr-only\">{LBL_VORNAME}*</label>\r\n                    <input type=\"text\" <% if ($CU_LOGGEDIN==true) %>disabled<%/if%> class=\"form-control<% if ($kregform_err.vorname!=\'\') %> has-error<% /if %>\" id=\"vorname\" placeholder=\"Vorname\" required name=\"FORM_NOTEMPTY[vorname]\" value=\"<% if ($CU_LOGGEDIN==true) %><%$customer.vorname%><%else%><% $CONTACTF.values.vorname|sthsc %><%/if%>\" >\r\n                </div> \r\n                <div class=\"form-group\">\r\n                    <label for=\"nachname\"  class=\"sr-only\">{LBL_NACHNAME}*</label>\r\n                    <input type=\"text\" <% if ($CU_LOGGEDIN==true) %>disabled<%/if%> class=\"form-control<% if ($kregform_err.nachname!=\'\') %> has-error<% /if %>\" id=\"nachname\" placeholder=\"Nachname\" required name=\"FORM_NOTEMPTY[nachname]\" value=\"<% if ($CU_LOGGEDIN==true) %><%$customer.nachname%><%else%><% $CONTACTF.values.nachname|sthsc %><%/if%>\" >\r\n                </div>\r\n                <div class=\"form-group\">\r\n                    <label for=\"datei\">Datei</label>\r\n                    <div class=\"input-group\">\r\n                        <input class=\"form-control\" type=\"text\" placeholder=\"Keine Datei ausgewählt\" readonly=\"\" value=\"\" name=\"\"></input>\r\n                        <input id=\"datei\" class=\"xform-control\" type=\"file\" onchange=\"this.previousElementSibling.value = this.value\" multiple value=\"\" name=\"datei[]\"></input>\r\n                        <div class=\"input-group-btn\"><button class=\"btn btn-default\" type=\"button\">Durchsuchen...</button></div>\r\n                    </div>\r\n                </div>\r\n            </div>   \r\n        \r\n            <div class=\"col-md-6\">\r\n                <div class=\"form-group\">\r\n                    <label for=\"tschapura\"  class=\"sr-only\">Email*</label>\r\n                    <input autocomplete=\"OFF\" type=\"email\" class=\"required form-control<% if ($kregform_err.nachname!=\'\') %> has-error<% /if %>\" id=\"tschapura\" placeholder=\"Email\" required name=\"FORM[tschapura]\" value=\"<% if ($CU_LOGGEDIN==true) %><%$customer.email%><%else%><% $CONTACTF.values.tschapura|sthsc %><%/if%>\" >\r\n                </div>    \r\n                <div class=\"form-group\">\r\n                    <label for=\"tel\"  class=\"sr-only\">Rückrufnummer*</label>\r\n                    <input autocomplete=\"OFF\" type=\"text\" class=\"form-control<% if ($kregform_err.tel!=\'\') %> has-error<% /if %>\" id=\"tel\" placeholder=\"Ihre Rückrufnummer\" required name=\"FORM[tel]\" value=\"<% $CONTACTF.values.tel|sthsc %>\" >\r\n                </div>    \r\n                \r\n    <% if ($contact.cf_captcha==1) %>\r\n                <div class=\"form-group\">\r\n                    <label for=\"tel\"  class=\"sr-only\">{LBL_SECODE}*</label>\r\n                    <img title=\"{LBL_SECODE}\" alt=\"\"  src=\"<%$PATH_CMS%>includes/modules/contactform/contact.captcha.php\"> <br>\r\n                    {LBL_CODEENTER}:<input size=\"6\" autocomplete=\"OFF\" name=\"securecode\" class=\"form-control\" type=\"text\">\r\n                </div>\r\n    <% /if %>            \r\n            </div>\r\n            \r\n        </div><!-- row -->\r\n        \r\n        <div class=\"form-group\">\r\n            <label for=\"fmr-fed-nachricht\"  class=\"sr-only\">Nachricht*</label>\r\n            <textarea class=\"form-control<% if ($kregform_err.tel!=\'\') %> has-error<% /if %>\" id=\"fmr-fed-nachricht\" placeholder=\"Ihre Nachricht\" required rows=\"6\" name=\"FORM_NOTEMPTY[nachricht]\"><% $CONTACTF.values.nachricht|sthsc %></textarea>\r\n     </div>\r\n              <div class=\"checkbox\">\r\n                <label for=\"bt-disclaimer-1\">\r\n                  <input type=\"checkbox\" name=\"disclaimer_check-1\" class=\"js-disclaimer-check\" value=\"1\" required=\"\" id=\"bt-disclaimer-1\" onclick=\"if ($(\'.js-disclaimer-check:checked\').length==3){$(\'#js-btn-send\').prop(\'disabled\',false)}else{$(\'#js-btn-send\').prop(\'disabled\',true)}\">\r\n                  Ich bin mit der Verarbeitung meiner angegebenen Daten zum Zwecke der Bearbeitung meiner Anfrage einverstanden\r\n                </label>\r\n              </div>\r\n              \r\n              <div class=\"checkbox\">\r\n                <label for=\"bt-disclaimer-2\">\r\n                  <input type=\"checkbox\" name=\"disclaimer_check-2\" value=\"1\" class=\"js-disclaimer-check\" required=\"\" id=\"bt-disclaimer-2\" onclick=\"if ($(\'.js-disclaimer-check:checked\').length==3){$(\'#js-btn-send\').prop(\'disabled\',false)}else{$(\'#js-btn-send\').prop(\'disabled\',true)}\">\r\n                  Ich habe die <a href=\"#\" title=\"Datenschutzerklärung\">Datenschutzerklärung</a> von <% $gbl_config.adr_general_firmname %> zur Kenntnis genommen.\r\n                </label>\r\n              </div>\r\n              \r\n              <div class=\"checkbox\">\r\n                  <label for=\"bt-disclaimer-3\">\r\n                  <input type=\"checkbox\" name=\"disclaimer_check-3\" value=\"1\" class=\"js-disclaimer-check\" required=\"\" id=\"bt-disclaimer-3\" onclick=\"if ($(\'.js-disclaimer-check:checked\').length==3){$(\'#js-btn-send\').prop(\'disabled\',false)}else{$(\'#js-btn-send\').prop(\'disabled\',true)}\">\r\n                  Ich bin darüber belehrt worden, dass ich meine vorstehende Einwilligung in die Verarbeitung meiner Daten jederzeit unter dem unten angegebenen Link auf der \r\n                  Kontaktseite dieser Homepage, durch Klick auf den entsprechenden Link in der Bestätigungsmail zu meiner Anfrage, durch gesonderte E-Mail (<% $gbl_config.adr_service_email %>), Telefax (<% $gbl_config.adr_fax %>) \r\n                  oder Brief an die <% $gbl_config.adr_firma %>, <% $gbl_config.adr_street %>, <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %> widerrufen kann.\r\n                  </label>\r\n                </div>\r\n        <input type=\"submit\" class=\"btn btn-primary mt-lg\" value=\"senden\" id=\"js-btn-send\" disabled=\"\">\r\n    </form>\r\n    <p class=\"mt-lg\">Sie können hier die Einwilligungserklärung widerrufen:<a href=\"javascript:void(0)\" onclick=\"$(\'#dsgvo-wider-form\').slideToggle()\"> jetzt widerrufen</a></p>\r\n    <div id=\"dsgvo-wider-form\">\r\n        <h3>Einwilligungserklärung widerrufen</h3>\r\n       <form action=\"<% $PHPSELF %>\" method=\"post\" enctype=\"multipart/form-data\" class=\"jsonform\">\r\n          <input name=\"token\" type=\"hidden\" value=\"<% $cms_token %>\">   \r\n          <input type=\"hidden\" name=\"page\" value=\"<% $page %>\">\r\n          <input type=\"hidden\" name=\"cmd\" value=\"send_disclaim_reject\">\r\n          <input type=\"hidden\" name=\"ajaxsubmit\" value=\"1\">\r\n          <input type=\"hidden\" name=\"cont_matrix_id\" value=\"<%$cont_matrix_id%>\">\r\n          <input type=\"hidden\" value=\"\" name=\"email\" class=\"hidden\">\r\n          <div class=\"form-group\">\r\n            <label for=\"tschapura-disclaim\">Ihre E-Mail</label>\r\n              <input autocomplete=\"OFF\" type=\"email\" class=\"required form-control\" id=\"tschapura-disclaim\" placeholder=\"Email\" required=\"\" name=\"FORM[tschapura]\" value=\"<% if ($CU_LOGGEDIN==true) %><%$customer.email%><%else%><% $CONTACTF.values.tschapura|sthsc %><%/if%>\" >\r\n          </div>\r\n          <button type=\"submit\" class=\"btn btn-default\">jetzt widerrufen</button>\r\n        \r\n      </form>\r\n    </div>\r\n    <div class=\"fmrdisclaimthx\" style=\"display:none\">\r\n      <div class=\"alert alert-info\">Sie haben eine E-Mail erhalten</div>\r\n    </div>\r\n    \r\n </div><!--col-->\r\n <div class=\"col-md-4 text-right\">\r\n    \r\n            <h3>Bürozeiten</h3>\r\n            Montags bis Freitags 09:00 - 18:00 Uhr<br>\r\n    \r\n            <h3>Anschrift</h3>\r\n            <% $gbl_config.adr_firma %><br>\r\n            <% $gbl_config.adr_street %><br>\r\n            <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %><br>\r\n        \r\n            <h3>Kontaktmöglichkeiten</h3>\r\n            Telefon: <% $gbl_config.adr_telefon  %><br>\r\n            Fax: <% $gbl_config.adr_fax  %><br>\r\n            Email: <% $gbl_config.adr_service_email %><br>\r\n    \r\n </div>\r\n</div><!--row-->\r\n\r\n    \r\n  </div>\r\n</section>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2019-02-20 12:26:18', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='kontakt_form', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:18:\"/kontakt_form.html\";s:2:\"id\";s:4:\"9580\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='contactform', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<section id=\"contact\">\r\n  <div class=\"container\">\r\n     <h2>Kontakt</h2>\r\n<div class=\"fmrcontactthx\" style=\"display:none\">\r\n    <h2>{LBL_DANKENACHRICHT}!</h2>    \r\n    <p>Wir werden umgehend Ihre Anfrage bearbeiten.</p>\r\n</div>\r\n\r\n<div class=\"row\">\r\n  <div class=\"col-md-8\">\r\n    <form action=\"<% $PHPSELF %>\" method=\"post\" enctype=\"multipart/form-data\" class=\"jsonform fmrcontactform\">\r\n    <input name=\"token\" type=\"hidden\" value=\"<% $cms_token %>\">   \r\n        <input type=\"hidden\" name=\"page\" value=\"<% $page %>\">\r\n        <input type=\"hidden\" name=\"cmd\" value=\"sendmsg\">\r\n        <input type=\"hidden\" name=\"ajaxsubmit\" value=\"1\">\r\n        <input type=\"hidden\" name=\"cont_matrix_id\" value=\"<%$cont_matrix_id%>\">\r\n        <input type=\"hidden\" value=\"\" name=\"email\" class=\"hidden\">\r\n        <div class=\"row\">\r\n            <div class=\"col-md-6\">\r\n                <div class=\"form-group\">\r\n                    <label for=\"vorname\"  class=\"sr-only\">{LBL_VORNAME}*</label>\r\n                    <input type=\"text\" <% if ($CU_LOGGEDIN==true) %>disabled<%/if%> class=\"form-control<% if ($kregform_err.vorname!=\'\') %> has-error<% /if %>\" id=\"vorname\" placeholder=\"Vorname\" required name=\"FORM_NOTEMPTY[vorname]\" value=\"<% if ($CU_LOGGEDIN==true) %><%$customer.vorname%><%else%><% $CONTACTF.values.vorname|sthsc %><%/if%>\" >\r\n                </div> \r\n                <div class=\"form-group\">\r\n                    <label for=\"nachname\"  class=\"sr-only\">{LBL_NACHNAME}*</label>\r\n                    <input type=\"text\" <% if ($CU_LOGGEDIN==true) %>disabled<%/if%> class=\"form-control<% if ($kregform_err.nachname!=\'\') %> has-error<% /if %>\" id=\"nachname\" placeholder=\"Nachname\" required name=\"FORM_NOTEMPTY[nachname]\" value=\"<% if ($CU_LOGGEDIN==true) %><%$customer.nachname%><%else%><% $CONTACTF.values.nachname|sthsc %><%/if%>\" >\r\n                </div>\r\n                <div class=\"form-group\">\r\n                    <label for=\"datei\">Datei</label>\r\n                    <div class=\"input-group\">\r\n                        <input class=\"form-control\" type=\"text\" placeholder=\"Keine Datei ausgewählt\" readonly=\"\" value=\"\" name=\"\"></input>\r\n                        <input id=\"datei\" class=\"xform-control\" type=\"file\" onchange=\"this.previousSibling.value = this.value\" value=\"\" name=\"datei\"></input>\r\n                        <div class=\"input-group-btn\"><button class=\"btn btn-default\" type=\"button\">Durchsuchen...</button></div>\r\n                    </div>\r\n                </div>            \r\n            </div>   \r\n        \r\n            <div class=\"col-md-6\">\r\n                <div class=\"form-group\">\r\n                    <label for=\"tschapura\"  class=\"sr-only\">Email*</label>\r\n                    <input autocomplete=\"OFF\" type=\"email\" class=\"required form-control<% if ($kregform_err.nachname!=\'\') %> has-error<% /if %>\" id=\"tschapura\" placeholder=\"Email\" required name=\"FORM[tschapura]\" value=\"<% if ($CU_LOGGEDIN==true) %><%$customer.email%><%else%><% $CONTACTF.values.tschapura|sthsc %><%/if%>\" >\r\n                </div>    \r\n                <div class=\"form-group\">\r\n                    <label for=\"tel\"  class=\"sr-only\">Rückrufnummer*</label>\r\n                    <input autocomplete=\"OFF\" type=\"text\" class=\"form-control<% if ($kregform_err.tel!=\'\') %> has-error<% /if %>\" id=\"tel\" placeholder=\"Ihre Rückrufnummer\" required name=\"FORM[tel]\" value=\"<% $CONTACTF.values.tel|sthsc %>\" >\r\n                </div>    \r\n    <% if ($contact.cf_cpatcha==1) %>\r\n                <div class=\"form-group\">\r\n                    <label for=\"tel\"  class=\"sr-only\">{LBL_SECODE}*</label>\r\n                    <img title=\"{LBL_SECODE}\" alt=\"\"  src=\"<%$PATH_CMS%>includes/modules/contactform/contact.captcha.php\"> <br>\r\n                    {LBL_CODEENTER}:<input size=\"6\" autocomplete=\"OFF\" name=\"securecode\" class=\"form-control\" type=\"text\"></td>\r\n                </div>\r\n    <% /if %>            \r\n            </div>\r\n        </div><!-- row -->\r\n        \r\n        <div class=\"form-group\">\r\n            <label for=\"fmr-fed-nachricht\"  class=\"sr-only\">Nachricht*</label>\r\n            <textarea class=\"form-control<% if ($kregform_err.tel!=\'\') %> has-error<% /if %>\" id=\"fmr-fed-nachricht\" placeholder=\"Ihre Nachricht\" required rows=\"6\" name=\"FORM_NOTEMPTY[nachricht]\"><% $CONTACTF.values.nachricht|sthsc %></textarea>\r\n        </div>\r\n       \r\n    \r\n        <input type=\"submit\" class=\"btn btn-primary mt-lg\" value=\"senden\">\r\n    </form>\r\n </div><!--col-->\r\n <div class=\"col-md-4 text-right\">\r\n    \r\n            <h3>Bürozeiten</h3>\r\n            Montags bis Freitags 09:00 - 18:00 Uhr<br>\r\n    \r\n            <h3>Anschrift</h3>\r\n            <% $gbl_config.adr_firma %><br>\r\n            <% $gbl_config.adr_street %><br>\r\n            <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %><br>\r\n        \r\n            <h3>Kontaktmöglichkeiten</h3>\r\n            Telefon: <% $gbl_config.adr_telefon  %><br>\r\n            Fax: <% $gbl_config.adr_fax  %><br>\r\n            Email: <% $gbl_config.adr_service_email %><br>\r\n    \r\n </div>\r\n</div><!--row-->\r\n\r\n    \r\n  </div>\r\n</section>\r\n\r\n<div id=\"gmap\"></div>\r\n\r\n\r\n\r\n<script src=\"https://maps.google.com/maps/api/js?sensor=false\"></script>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2019-03-01 16:21:55', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='kontakt_form', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:21:\"/de/kontakt_form.html\";s:2:\"id\";s:4:\"9580\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='contactform', t_precontent=''
