INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<img src=\"<%$PATH_CMS%>file_data/workshop/<%$WORKSHOP.ws.ws_theme%>\" alt=\"\" class=\"img-responsive mb-lg\">\r\n\r\n<% if ($WORKSHOP.ws.bookings_free>0) %>\r\n  <a href=\"<%$PHPSELF%>?page=<%$page%>&cmd=book_now&id=<%$WORKSHOP.ws.id%>\" title=\"buchen\" class=\"btn btn-primary pull-right btn-booknow\">buchen</a>\r\n<%else%>\r\n <div class=\"alert alert-danger\">ausgebucht</div>\r\n<%/if%>\r\n  <h2><%$WORKSHOP.ws.ws_title%></h2>\r\n  <div class=\"row\">\r\n    <div class=\"col-md-6\">\r\n      <%$WORKSHOP.ws.ws_shortdesc%>\r\n    </div>\r\n    <div class=\"col-md-6\">\r\n      <div class=\"cricle pull-right\">\r\n        <h2><%$WORKSHOP.ws.ws_price_br%> <i class=\"fa fa-eur\"></i></h2>\r\n      </div>\r\n      Datum: <%$WORKSHOP.ws.date_ger%> <%$WORKSHOP.ws.ws_time%> - <%$WORKSHOP.ws.ws_time_to%>\r\n      \r\n      <address>\r\n        <b>Location:</b><br>\r\n        <%$WORKSHOP.ws.ws_location%><br>\r\n        <%$WORKSHOP.ws.ws_street%><br>\r\n        <%$WORKSHOP.ws.ws_plz%> <%$WORKSHOP.ws.c_city%>\r\n      </address>\r\n      <p>\r\n        <b>Zielgruppe:</b><br>\r\n        <%$WORKSHOP.ws.ws_zielgruppe%>\r\n      </p>\r\n      <p>\r\n        <b>Teilnehmer Anzahl:</b><br>\r\n        <%$WORKSHOP.ws.ws_teilnvon%> - <%$WORKSHOP.ws.ws_teilnbis%> Personen\r\n      </p>      \r\n    </div>\r\n  </div>\r\n<hr>\r\n  <div class=\"row\">\r\n    <div class=\"col-md-6\">\r\n        <p>\r\n          <b>Zielsetzung:</b><br>\r\n         <%$WORKSHOP.ws.ws_zielsetzung%>\r\n        </p>\r\n        <p>\r\n          <b>Das müssen Sie mitbringen:</b><br>\r\n         <%$WORKSHOP.ws.ws_mitbringen%>\r\n        </p>\r\n        <p>\r\n          <b>Sonstiges:</b><br>\r\n         <%$WORKSHOP.ws.ws_sonstiges%>\r\n        </p>\r\n    </div>\r\n    <div class=\"col-md-6\">\r\n\r\n        <p>\r\n          <b>Durchführung:</b><br>\r\n         <%$WORKSHOP.ws.ws_durchfuehrung%>\r\n        </p>\r\n        <p>\r\n          <b>Im Preis enthalten sind:</b><br>\r\n         <%$WORKSHOP.ws.ws_enthalten%>\r\n        </p>\r\n        <p>\r\n          <b>Bildrechte:</b><br>\r\n         <%$WORKSHOP.ws.ws_bildrechte%>\r\n        </p>      \r\n    </div>\r\n  </div>\r\n  <hr>\r\n\r\n<h3>Bilder</h3>\r\n<div class=\"row\">\r\n    <% foreach from=$WORKSHOP.ws.thumbs name=gloop item=img %>\r\n    <div class=\"col-md-4\">\r\n      <img alt=\"<%$img|sthsc%>\" src=\"<%$img%>\" class=\"img-responsive\" />\r\n    </div>\r\n     <% if ($smarty.foreach.gloop.iteration % 3 == 0 )%></div><div class=\"row mt-lg\"><%/if%>\r\n    <%/foreach%>\r\n</div>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:1:\"/\";s:2:\"id\";s:6:\"100151\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_workshop_detail', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<img src=\"<%$PATH_CMS%>file_data/workshop/<%$WORKSHOP.ws.ws_theme%>\" alt=\"\" class=\"img-responsive mb-lg\">\r\n\r\n<% if ($WORKSHOP.ws.bookings_free>0) %>\r\n  <a href=\"<%$PHPSELF%>?page=<%$page%>&cmd=book_now&id=<%$WORKSHOP.ws.id%>\" title=\"buchen\" class=\"btn btn-primary pull-right btn-booknow\">buchen</a>\r\n<%else%>\r\n <div class=\"alert alert-danger\">ausgebucht</div>\r\n<%/if%>\r\n  <h2><%$WORKSHOP.ws.ws_title%></h2>\r\n  <div class=\"row\">\r\n    <div class=\"col-md-6\">\r\n      <%$WORKSHOP.ws.ws_shortdesc%>\r\n    </div>\r\n    <div class=\"col-md-6\">\r\n      <div class=\"cricle pull-right\">\r\n        <h2><%$WORKSHOP.ws.ws_price_br%> <i class=\"fa fa-eur\"></i></h2>\r\n      </div>\r\n      Datum: <%$WORKSHOP.ws.date_ger%> <%$WORKSHOP.ws.ws_time%> - <%$WORKSHOP.ws.ws_time_to%>\r\n      \r\n      <address>\r\n        <b>Location:</b><br>\r\n        <%$WORKSHOP.ws.ws_location%><br>\r\n        <%$WORKSHOP.ws.ws_street%><br>\r\n        <%$WORKSHOP.ws.ws_plz%> <%$WORKSHOP.ws.c_city%>\r\n      </address>\r\n      <p>\r\n        <b>Zielgruppe:</b><br>\r\n        <%$WORKSHOP.ws.ws_zielgruppe%>\r\n      </p>\r\n      <p>\r\n        <b>Teilnehmer Anzahl:</b><br>\r\n        <%$WORKSHOP.ws.ws_teilnvon%> - <%$WORKSHOP.ws.ws_teilnbis%> Personen\r\n      </p>      \r\n    </div>\r\n  </div>\r\n<hr>\r\n  <div class=\"row\">\r\n    <div class=\"col-md-6\">\r\n        <p>\r\n          <b>Zielsetzung:</b><br>\r\n         <%$WORKSHOP.ws.ws_zielsetzung%>\r\n        </p>\r\n        <p>\r\n          <b>Das müssen Sie mitbringen:</b><br>\r\n         <%$WORKSHOP.ws.ws_mitbringen%>\r\n        </p>\r\n        <p>\r\n          <b>Sonstiges:</b><br>\r\n         <%$WORKSHOP.ws.ws_sonstiges%>\r\n        </p>\r\n    </div>\r\n    <div class=\"col-md-6\">\r\n\r\n        <p>\r\n          <b>Durchführung:</b><br>\r\n         <%$WORKSHOP.ws.ws_durchfuehrung%>\r\n        </p>\r\n        <p>\r\n          <b>Im Preis enthalten sind:</b><br>\r\n         <%$WORKSHOP.ws.ws_enthalten%>\r\n        </p>\r\n        <p>\r\n          <b>Bildrechte:</b><br>\r\n         <%$WORKSHOP.ws.ws_bildrechte%>\r\n        </p>      \r\n    </div>\r\n  </div>\r\n  <hr>\r\n\r\n<h3>Bilder</h3>\r\n<div class=\"row\">\r\n    <% foreach from=$WORKSHOP.ws.thumbs name=gloop item=img %>\r\n    <div class=\"col-md-4\">\r\n      <img alt=\"<%$img|sthsc%>\" src=\"<%$img%>\" class=\"img-responsive\" />\r\n    </div>\r\n     <% if ($smarty.foreach.gloop.iteration % 3 == 0 )%></div><div class=\"row mt-lg\"><%/if%>\r\n    <%/foreach%>\r\n</div>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:15:\"/de/100151.html\";s:2:\"id\";s:6:\"100151\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_workshop_detail', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<div class=\"row\">\r\n  <div class=\"col-md-6\">\r\n      <h2>Workshops in <%$WORKSHOP.city.c_city%></h2>\r\n      <p><%$WORKSHOP.city.c_text|nl2br%></p>\r\n  </div>\r\n  <div class=\"col-md-6\">\r\n    <img src=\"<%$PATH_CMS%>file_data/workshop/<%$WORKSHOP.city.c_image%>\" alt=\"\" class=\"img-responsive\">\r\n  </div>\r\n</div>\r\n\r\n<% if (count($WORKSHOP.workshops)>0) %>\r\n<table class=\"table table-striped- table-hover\">\r\n        <thead>\r\n            <tr>                \r\n                <th>Workshop</th>                \r\n                <th>Datum</th>\r\n                <th class=\"text-center\">freie Plätze</th>\r\n                <th>Beschreibung</th>\r\n                <th></th>\r\n            </tr>\r\n        </thead>   \r\n        <tbody>\r\n        <% foreach from=$WORKSHOP.workshops item=row %>\r\n            <tr >                \r\n                <td><%$row.ws_title%></td>\r\n                <td><%$row.date_ger%></td>\r\n                <td class=\"text-center\"><span class=\"badge<% if ($row.bookings_free>0) %> bg-success<%/if%>\"><%$row.bookings_free%></span></td>\r\n                <td width=\"60%\"><%$row.ws_shortdesc|truncate:300%></td>\r\n                <td class=\"text-right\">\r\n                  <% if ($row.bookings_free>0) %>\r\n                    <a class=\"btn btn-default\" href=\"<%$PHPSELF%>?page=<%$page%>&cmd=load_workshop&id=<%$row.id%>\">mehr...</a>\r\n                  <%else%>\r\n                    <div class=\"alert alert-danger\">ausgebucht</div>\r\n                  <%/if%>\r\n                </td>\r\n            </tr>\r\n        <%/foreach%>\r\n        </tbody>\r\n</table>\r\n<%else%>\r\n  <div class=\"alert alert-info\">Zur Zeit gibt es hier keine Workshops.</div>\r\n<%/if%>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:1:\"/\";s:2:\"id\";s:6:\"100152\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_workshop_auflistung', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<div class=\"row\">\r\n  <div class=\"col-md-6\">\r\n      <h2>Workshops in <%$WORKSHOP.city.c_city%></h2>\r\n      <p><%$WORKSHOP.city.c_text|nl2br%></p>\r\n  </div>\r\n  <div class=\"col-md-6\">\r\n    <img src=\"<%$PATH_CMS%>file_data/workshop/<%$WORKSHOP.city.c_image%>\" alt=\"\" class=\"img-responsive\">\r\n  </div>\r\n</div>\r\n\r\n<% if (count($WORKSHOP.workshops)>0) %>\r\n<table class=\"table table-striped- table-hover\">\r\n        <thead>\r\n            <tr>                \r\n                <th>Workshop</th>                \r\n                <th>Datum</th>\r\n                <th class=\"text-center\">freie Plätze</th>\r\n                <th>Beschreibung</th>\r\n                <th></th>\r\n            </tr>\r\n        </thead>   \r\n        <tbody>\r\n        <% foreach from=$WORKSHOP.workshops item=row %>\r\n            <tr >                \r\n                <td><%$row.ws_title%></td>\r\n                <td><%$row.date_ger%></td>\r\n                <td class=\"text-center\"><span class=\"badge<% if ($row.bookings_free>0) %> bg-success<%/if%>\"><%$row.bookings_free%></span></td>\r\n                <td width=\"60%\"><%$row.ws_shortdesc|truncate:300%></td>\r\n                <td class=\"text-right\">\r\n                  <% if ($row.bookings_free>0) %>\r\n                    <a class=\"btn btn-default\" href=\"<%$PHPSELF%>?page=<%$page%>&cmd=load_workshop&id=<%$row.id%>\">mehr...</a>\r\n                  <%else%>\r\n                    <div class=\"alert alert-danger\">ausgebucht</div>\r\n                  <%/if%>\r\n                </td>\r\n            </tr>\r\n        <%/foreach%>\r\n        </tbody>\r\n</table>\r\n<%else%>\r\n  <div class=\"alert alert-info\">Zur Zeit gibt es hier keine Workshops.</div>\r\n<%/if%>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:15:\"/de/100152.html\";s:2:\"id\";s:6:\"100152\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_workshop_auflistung', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<form action=\"<%$PHPSELF%>\" class=\"form-inline\">\r\n  <input type=\"hidden\" name=\"cmd\" value=\"load_workshops\">\r\n  <input type=\"hidden\" name=\"page\" value=\"<%$page%>\">\r\n  <div class=\"form-group\">\r\n    <label for=\"bs-city\">Stadt:</label>\r\n    <select id=\"bs-city\" name=\"city\" class=\"form-control\">\r\n    <% foreach from=$WORKSHOP.cities item=row %>\r\n      <option value=\"<%$row.id%>\"><%$row.c_city%></option>\r\n    <%/foreach%>\r\n    </select>\r\n  </div>\r\n  <button type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-search\"></i></button>\r\n  \r\n</form>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:1:\"/\";s:2:\"id\";s:6:\"100153\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_staedte_auflistung', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<form action=\"<%$PHPSELF%>\" class=\"form-inline\">\r\n  <input type=\"hidden\" name=\"cmd\" value=\"load_workshops\">\r\n  <input type=\"hidden\" name=\"page\" value=\"<%$page%>\">\r\n  <div class=\"form-group\">\r\n    <label for=\"bs-city\">Stadt:</label>\r\n    <select id=\"bs-city\" name=\"city\" class=\"form-control\">\r\n    <% foreach from=$WORKSHOP.cities item=row %>\r\n      <option value=\"<%$row.id%>\"><%$row.c_city%></option>\r\n    <%/foreach%>\r\n    </select>\r\n  </div>\r\n  <button type=\"submit\" class=\"btn btn-primary\"><i class=\"fa fa-search\"></i></button>\r\n  \r\n</form>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:15:\"/de/100153.html\";s:2:\"id\";s:6:\"100153\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_staedte_auflistung', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<section id=\"js-reg-form\">\r\n   <h2>Workshop buchen</h2>\r\n  \r\n  <form role=\"form\" action=\"<% $PHPSELF %>\" method=\"post\" enctype=\"multipart/form-data\" class=\"jsonform\">\r\n      <input name=\"cmd\" type=\"hidden\" value=\"register_customer\">\r\n      <input type=\"hidden\" name=\"page\" value=\"<% $page %>\">\r\n      <input type=\"hidden\" name=\"token\" value=\"<% $cms_token%>\">\r\n  <div class=\"row\">\r\n  <div class=\"col-md-4\">\r\n  <h2>{LBL_ADDRESS}</h2>\r\n      <div class=\"form-group\">\r\n          <label for=\"fmrgeschlecht\" class=\"sr-only\">{LBL_ANREDE}</label>\r\n          <select class=\"form-control\" id=\"fmrgeschlecht\" name=\"FORM[geschlecht]\">\r\n            <option value=\"m\">Herr</option>\r\n            <option value=\"w\">Frau</option>\r\n          </select>\r\n      </div>\r\n  \r\n      <div class=\"form-group\">\r\n          <label for=\"vorname\"  class=\"sr-only\">{LBL_VORNAME}*</label>\r\n          <input type=\"text\" class=\"form-control<% if ($kregform_err.vorname!=\'\') %> has-error<% /if %>\" id=\"vorname\" placeholder=\"Vorname\" required name=\"FORM_NOTEMPTY[vorname]\" value=\"<% $kregform.vorname %>\" >\r\n      </div> \r\n      \r\n      <div class=\"form-group\">\r\n          <label for=\"fmrnachname\"  class=\"sr-only\">{LBL_NACHNAME}*</label>\r\n          <input type=\"text\" class=\"form-control<% if ($kregform_err.nachname!=\'\') %> has-error<% /if %>\" id=\"fmrnachname\" placeholder=\"Nachname\" required name=\"FORM_NOTEMPTY[nachname]\" value=\"<% $kregform.nachname %>\" >\r\n      </div>   \r\n \r\n      <div class=\"form-group\">\r\n          <div class=\"row\">\r\n              <div class=\"col-md-8\">\r\n                  <label for=\"strasse\"  class=\"sr-only\">{LBL_STRASSE}*</label>\r\n                  <input type=\"text\" class=\"form-control<% if ($kregform_err.strasse!=\'\') %> has-error<% /if %>\" id=\"strasse\" placeholder=\"Strasse\" required name=\"FORM_NOTEMPTY[strasse]\" value=\"<% $kregform.strasse %>\" >\r\n              </div>\r\n              <div class=\"col-md-4\">    \r\n                  <label for=\"hausnr\"  class=\"sr-only\">HausNr*</label>\r\n                  <input type=\"text\" class=\"form-control<% if ($kregform_err.hausnr!=\'\') %> has-error<% /if %>\" id=\"hausnr\" placeholder=\"Hausnr.\" required name=\"FORM_NOTEMPTY[hausnr]\" value=\"<% $kregform.hausnr %>\" >\r\n              </div>    \r\n          </div>\r\n      </div> \r\n     \r\n     <div class=\"row\">\r\n              <div class=\"col-md-4\">\r\n                <div class=\"form-group\">\r\n                    <label for=\"plz\"  class=\"sr-only\">{LBL_PLZ}*</label>\r\n                    <input type=\"text\" class=\"form-control<% if ($kregform_err.plz!=\'\') %> has-error<% /if %>\" id=\"plz\" placeholder=\"PLZ\" required name=\"FORM_NOTEMPTY[plz]\" value=\"<% $kregform.plz %>\" >\r\n                </div>  \r\n              </div>\r\n              <div class=\"col-md-8\">\r\n              <div class=\"form-group\">\r\n                  <label for=\"ort\"  class=\"sr-only\">{LBL_ORT}*</label>\r\n                  <input type=\"text\" class=\"form-control<% if ($kregform_err.ort!=\'\') %> has-error<% /if %>\" id=\"ort\" placeholder=\"Ort\" required name=\"FORM_NOTEMPTY[ort]\" value=\"<% $kregform.ort %>\" >\r\n              </div>   \r\n        </div>         \r\n  </div>   \r\n      <div class=\"form-group\">\r\n          <label for=\"land\"  class=\"sr-only\">{LBL_LAND}*</label>\r\n          <select class=\"form-control<% if ($kregform_err.land!=\'\') %> has-error<% /if %>\" id=\"land\" name=\"FORM[land]\"><% $WORKSHOP.land_select %></select>\r\n      </div>   \r\n  \r\n      <div class=\"form-group\">\r\n          <label for=\"tel\"  class=\"sr-only\">{LBL_TELEFON}*</label>\r\n          <input type=\"text\" class=\"form-control<% if ($kregform_err.tel!=\'\') %> has-error<% /if %>\" id=\"tel\" placeholder=\"Telefon\" required name=\"FORM_NOTEMPTY[tel]\" value=\"<% $kregform.tel %>\" >\r\n      </div>    \r\n             \r\n  \r\n  </div>\r\n  \r\n  <div class=\"col-md-4\">\r\n       <h2>Login Daten</h2>\r\n      <div class=\"form-group\">\r\n          <label for=\"email\"  class=\"sr-only\">Email*</label>\r\n          <input type=\"email\" class=\"required form-control<% if ($kregform_err.email!=\'\') %> has-error<% /if %>\" id=\"email\" placeholder=\"Email\" required name=\"FORM[email]\" value=\"<% $kregform.email %>\" >\r\n      </div>     \r\n  \r\n      <div class=\"form-group\">\r\n          <label for=\"passwort\"  class=\"sr-only\">{LBL_PASSWORT}*</label>\r\n          <input type=\"password\" class=\"<% if ($CU_LOGGEDIN==false) %>required<%/if%> form-control<% if ($kregform_err.passwort!=\'\') %> has-error<% /if %>\" id=\"passwort\" placeholder=\"Passwort\" <% if ($CU_LOGGEDIN==false) %>required<%/if%> name=\"FORM[passwort]\" value=\"<% $kregform.passwort %>\" >\r\n      </div> \r\n  \r\n  <% if ($gbl_config.captcha_active==1) %>\r\n      <div class=\"form-group\">\r\n          <label for=\"capcha\"  class=\"sr-only\">{LBL_SECODE}*</label>\r\n          <img title=\"{LBL_SECODE}\" alt=\"\"  src=\"<%$PATH_CMS%>captcha.php\">\r\n          {LBL_CODEENTER}:<br>\r\n          <input type=\"text\" class=\"form-control<% if ($kregform_err.securecode!=\'\') %> has-error<% /if %>\" id=\"securecode\" placeholder=\"Capcha Text\" required name=\"securecode\" value=\"\" >\r\n      </div> \r\n   <% /if %>   \r\n  </div> \r\n  \r\n  <div class=\"col-md-4\">   \r\n  <% if ($CU_LOGGEDIN==false) %>\r\n          <h2>AGB</h2>\r\n          <div class=\"checkbox\">\r\n              <label> \r\n                  <input type=\"checkbox\" required name=\"agbtrue\" value=\"1\">Die <a href=\"{URL_TPL_10045}\">AGB</a> habe ich zur Kenntis genommen und mit ihrer Geltung bin ich einverstanden\r\n              </label>    \r\n          </div>\r\n          <div class=\"checkbox\">\r\n              <label>\r\n                  <input type=\"checkbox\" required name=\"wr\" value=\"1\">Die <a href=\"{URL_TPL_10045}\">Wiederrufsbelehrung</a> habe ich zur Kenntis genommen\r\n              </label>    \r\n          </div>    \r\n      <%/if%>\r\n  <button type=\"submit\" class=\"btn btn-primary\">kostenpflichtig bestellen</button>\r\n    \r\n  </div>\r\n</div>    <!-- ENDE ROW -->\r\n</form>\r\n</section>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:1:\"/\";s:2:\"id\";s:6:\"100155\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_register', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<section id=\"js-reg-form\">\r\n   <h2>Workshop buchen</h2>\r\n  \r\n  <form role=\"form\" action=\"<% $PHPSELF %>\" method=\"post\" enctype=\"multipart/form-data\" class=\"jsonform\">\r\n      <input name=\"cmd\" type=\"hidden\" value=\"register_customer\">\r\n      <input type=\"hidden\" name=\"page\" value=\"<% $page %>\">\r\n      <input type=\"hidden\" name=\"token\" value=\"<% $cms_token%>\">\r\n  <div class=\"row\">\r\n  <div class=\"col-md-4\">\r\n  <h2>{LBL_ADDRESS}</h2>\r\n      <div class=\"form-group\">\r\n          <label for=\"fmrgeschlecht\" class=\"sr-only\">{LBL_ANREDE}</label>\r\n          <select class=\"form-control\" id=\"fmrgeschlecht\" name=\"FORM[geschlecht]\">\r\n            <option value=\"m\">Herr</option>\r\n            <option value=\"w\">Frau</option>\r\n          </select>\r\n      </div>\r\n  \r\n      <div class=\"form-group\">\r\n          <label for=\"vorname\"  class=\"sr-only\">{LBL_VORNAME}*</label>\r\n          <input type=\"text\" class=\"form-control<% if ($kregform_err.vorname!=\'\') %> has-error<% /if %>\" id=\"vorname\" placeholder=\"Vorname\" required name=\"FORM_NOTEMPTY[vorname]\" value=\"<% $kregform.vorname %>\" >\r\n      </div> \r\n      \r\n      <div class=\"form-group\">\r\n          <label for=\"fmrnachname\"  class=\"sr-only\">{LBL_NACHNAME}*</label>\r\n          <input type=\"text\" class=\"form-control<% if ($kregform_err.nachname!=\'\') %> has-error<% /if %>\" id=\"fmrnachname\" placeholder=\"Nachname\" required name=\"FORM_NOTEMPTY[nachname]\" value=\"<% $kregform.nachname %>\" >\r\n      </div>   \r\n \r\n      <div class=\"form-group\">\r\n          <div class=\"row\">\r\n              <div class=\"col-md-8\">\r\n                  <label for=\"strasse\"  class=\"sr-only\">{LBL_STRASSE}*</label>\r\n                  <input type=\"text\" class=\"form-control<% if ($kregform_err.strasse!=\'\') %> has-error<% /if %>\" id=\"strasse\" placeholder=\"Strasse\" required name=\"FORM_NOTEMPTY[strasse]\" value=\"<% $kregform.strasse %>\" >\r\n              </div>\r\n              <div class=\"col-md-4\">    \r\n                  <label for=\"hausnr\"  class=\"sr-only\">HausNr*</label>\r\n                  <input type=\"text\" class=\"form-control<% if ($kregform_err.hausnr!=\'\') %> has-error<% /if %>\" id=\"hausnr\" placeholder=\"Hausnr.\" required name=\"FORM_NOTEMPTY[hausnr]\" value=\"<% $kregform.hausnr %>\" >\r\n              </div>    \r\n          </div>\r\n      </div> \r\n     \r\n     <div class=\"row\">\r\n              <div class=\"col-md-4\">\r\n                <div class=\"form-group\">\r\n                    <label for=\"plz\"  class=\"sr-only\">{LBL_PLZ}*</label>\r\n                    <input type=\"text\" class=\"form-control<% if ($kregform_err.plz!=\'\') %> has-error<% /if %>\" id=\"plz\" placeholder=\"PLZ\" required name=\"FORM_NOTEMPTY[plz]\" value=\"<% $kregform.plz %>\" >\r\n                </div>  \r\n              </div>\r\n              <div class=\"col-md-8\">\r\n              <div class=\"form-group\">\r\n                  <label for=\"ort\"  class=\"sr-only\">{LBL_ORT}*</label>\r\n                  <input type=\"text\" class=\"form-control<% if ($kregform_err.ort!=\'\') %> has-error<% /if %>\" id=\"ort\" placeholder=\"Ort\" required name=\"FORM_NOTEMPTY[ort]\" value=\"<% $kregform.ort %>\" >\r\n              </div>   \r\n        </div>         \r\n  </div>   \r\n      <div class=\"form-group\">\r\n          <label for=\"land\"  class=\"sr-only\">{LBL_LAND}*</label>\r\n          <select class=\"form-control<% if ($kregform_err.land!=\'\') %> has-error<% /if %>\" id=\"land\" name=\"FORM[land]\"><% $WORKSHOP.land_select %></select>\r\n      </div>   \r\n  \r\n      <div class=\"form-group\">\r\n          <label for=\"tel\"  class=\"sr-only\">{LBL_TELEFON}*</label>\r\n          <input type=\"text\" class=\"form-control<% if ($kregform_err.tel!=\'\') %> has-error<% /if %>\" id=\"tel\" placeholder=\"Telefon\" required name=\"FORM_NOTEMPTY[tel]\" value=\"<% $kregform.tel %>\" >\r\n      </div>    \r\n             \r\n  \r\n  </div>\r\n  \r\n  <div class=\"col-md-4\">\r\n       <h2>Login Daten</h2>\r\n      <div class=\"form-group\">\r\n          <label for=\"email\"  class=\"sr-only\">Email*</label>\r\n          <input type=\"email\" class=\"required form-control<% if ($kregform_err.email!=\'\') %> has-error<% /if %>\" id=\"email\" placeholder=\"Email\" required name=\"FORM[email]\" value=\"<% $kregform.email %>\" >\r\n      </div>     \r\n  \r\n      <div class=\"form-group\">\r\n          <label for=\"passwort\"  class=\"sr-only\">{LBL_PASSWORT}*</label>\r\n          <input type=\"password\" class=\"<% if ($CU_LOGGEDIN==false) %>required<%/if%> form-control<% if ($kregform_err.passwort!=\'\') %> has-error<% /if %>\" id=\"passwort\" placeholder=\"Passwort\" <% if ($CU_LOGGEDIN==false) %>required<%/if%> name=\"FORM[passwort]\" value=\"<% $kregform.passwort %>\" >\r\n      </div> \r\n  \r\n  <% if ($gbl_config.captcha_active==1) %>\r\n      <div class=\"form-group\">\r\n          <label for=\"capcha\"  class=\"sr-only\">{LBL_SECODE}*</label>\r\n          <img title=\"{LBL_SECODE}\" alt=\"\"  src=\"<%$PATH_CMS%>captcha.php\">\r\n          {LBL_CODEENTER}:<br>\r\n          <input type=\"text\" class=\"form-control<% if ($kregform_err.securecode!=\'\') %> has-error<% /if %>\" id=\"securecode\" placeholder=\"Capcha Text\" required name=\"securecode\" value=\"\" >\r\n      </div> \r\n   <% /if %>   \r\n  </div> \r\n  \r\n  <div class=\"col-md-4\">   \r\n  <% if ($CU_LOGGEDIN==false) %>\r\n          <h2>AGB</h2>\r\n          <div class=\"checkbox\">\r\n              <label> \r\n                  <input type=\"checkbox\" required name=\"agbtrue\" value=\"1\">Die <a href=\"{URL_TPL_10045}\">AGB</a> habe ich zur Kenntis genommen und mit ihrer Geltung bin ich einverstanden\r\n              </label>    \r\n          </div>\r\n          <div class=\"checkbox\">\r\n              <label>\r\n                  <input type=\"checkbox\" required name=\"wr\" value=\"1\">Die <a href=\"{URL_TPL_10045}\">Wiederrufsbelehrung</a> habe ich zur Kenntis genommen\r\n              </label>    \r\n          </div>    \r\n      <%/if%>\r\n  <button type=\"submit\" class=\"btn btn-primary\">kostenpflichtig bestellen</button>\r\n    \r\n  </div>\r\n</div>    <!-- ENDE ROW -->\r\n</form>\r\n</section>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:15:\"/de/100155.html\";s:2:\"id\";s:6:\"100155\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_register', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<section>\r\n  <h2>Vielen Dank für Ihren Auftrag. Bezahlen Sie jetzt bequem mit PayPal.</h2>\r\n  <div class=\"text-center\">\r\n    <form id=\"paypal_form\" action=\"<%$PAYPAL_POST.PAYPAL_URL%>\" name=\"paypal_form\" method=\"post\">\r\n    \r\n    <button class=\"btn btn-default\">Jetzt bezahlen</button>\r\n    <!-- Paypal Configuration -->\r\n    <input type=\"hidden\" name=\"business\" value=\"<% $PAYPAL_POST.business %>\">\r\n    <input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\r\n    <input type=\"hidden\" name=\"image_url\" value=\"<% $PAYPAL_POST.image_url %>\">\r\n    <input type=\"hidden\" name=\"currency_code\" value=\"<% $PAYPAL_POST.currency_code %>\">\r\n    <input type=\"hidden\" name=\"mc_currency\" value=\"<% $PAYPAL_POST.mc_currency %>\">\r\n    <input type=\"hidden\" name=\"return\" value=\"<% $PAYPAL_POST.return %>\">\r\n    <input type=\"hidden\" name=\"cancel_return\" value=\"<% $PAYPAL_POST.cancel_return %>\">\r\n    <input type=\"hidden\" name=\"rm\" value=\"2\">\r\n    <input type=\"hidden\" name=\"residence_country\" value=\"<% $customer.country_code_2 %>\">\r\n    <input type=\"hidden\" name=\"cbt\" value=\"<% $PAYPAL_POST.cbt %>\">\r\n    <!-- Payment Page Information -->\r\n    <input type=\"hidden\" name=\"no_note\" value=\"1\">\r\n    <!-- Product Information -->\r\n    <input type=\"hidden\" name=\"item_name\" value=\"{LBL_YOURORDER} <% $gbl_config.adr_firma %>\">\r\n    <input type=\"hidden\" name=\"amount\" value=\"<% $PAYPAL_POST.amount %>\">\r\n    <input type=\"hidden\" name=\"shipping\" value=\"0\">\r\n    <input type=\"hidden\" name=\"quantity\" value=\"1\">\r\n    <input type=\"hidden\" name=\"item_number\" value=\"<% $PAYPAL_POST.item_number %>\">\r\n    <input type=\"hidden\" name=\"invoice\" value=\"<% $PAYPAL_POST.invoice %>\">\r\n    <input type=\"hidden\" name=\"custom\" value=\"<% $PAYPAL_POST.custom %>\">\r\n    <input type=\"hidden\" name=\"notify_url\" value=\"<% $PAYPAL_POST.notify_url %>\">\r\n    <input type=\"hidden\" name=\"undefined_quantity\" value=\"\">\r\n    <!-- Customer Information -->\r\n    <input type=\"hidden\" name=\"first_name\" value=\"<% $PAYPAL_POST.first_name %>\">\r\n    <input type=\"hidden\" name=\"last_name\" value=\"<% $PAYPAL_POST.last_name %>\">\r\n    <input type=\"hidden\" name=\"adress1\" value=\"<% $PAYPAL_POST.adress1 %>\">\r\n    <input type=\"hidden\" name=\"zip\" value=\"<% $PAYPAL_POST.zip %>\">\r\n    <input type=\"hidden\" name=\"city\" value=\"<% $PAYPAL_POST.city %>\">\r\n    <input type=\"hidden\" name=\"email\" value=\"<% $PAYPAL_POST.email %>\">\r\n    \r\n    </form>\r\n  </div>\r\n</section>\r\n\r\n<script>\r\n//<![CDATA[\r\n// document.paypal_form.submit();\r\n//]]>\r\n</script>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2018-06-06 12:05:24', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:1:\"/\";s:2:\"id\";s:6:\"100156\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_paypal', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<section>\r\n  <h2>Vielen Dank für Ihren Auftrag. Bezahlen Sie jetzt bequem mit PayPal.</h2>\r\n  <div class=\"text-center\">\r\n    <form id=\"paypal_form\" action=\"<%$PAYPAL_POST.PAYPAL_URL%>\" name=\"paypal_form\" method=\"post\">\r\n    \r\n    <button class=\"btn btn-default\">Jetzt bezahlen</button>\r\n    <!-- Paypal Configuration -->\r\n    <input type=\"hidden\" name=\"business\" value=\"<% $PAYPAL_POST.business %>\">\r\n    <input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\r\n    <input type=\"hidden\" name=\"image_url\" value=\"<% $PAYPAL_POST.image_url %>\">\r\n    <input type=\"hidden\" name=\"currency_code\" value=\"<% $PAYPAL_POST.currency_code %>\">\r\n    <input type=\"hidden\" name=\"mc_currency\" value=\"<% $PAYPAL_POST.mc_currency %>\">\r\n    <input type=\"hidden\" name=\"return\" value=\"<% $PAYPAL_POST.return %>\">\r\n    <input type=\"hidden\" name=\"cancel_return\" value=\"<% $PAYPAL_POST.cancel_return %>\">\r\n    <input type=\"hidden\" name=\"rm\" value=\"2\">\r\n    <input type=\"hidden\" name=\"residence_country\" value=\"<% $customer.country_code_2 %>\">\r\n    <input type=\"hidden\" name=\"cbt\" value=\"<% $PAYPAL_POST.cbt %>\">\r\n    <!-- Payment Page Information -->\r\n    <input type=\"hidden\" name=\"no_note\" value=\"1\">\r\n    <!-- Product Information -->\r\n    <input type=\"hidden\" name=\"item_name\" value=\"{LBL_YOURORDER} <% $gbl_config.adr_firma %>\">\r\n    <input type=\"hidden\" name=\"amount\" value=\"<% $PAYPAL_POST.amount %>\">\r\n    <input type=\"hidden\" name=\"shipping\" value=\"0\">\r\n    <input type=\"hidden\" name=\"quantity\" value=\"1\">\r\n    <input type=\"hidden\" name=\"item_number\" value=\"<% $PAYPAL_POST.item_number %>\">\r\n    <input type=\"hidden\" name=\"invoice\" value=\"<% $PAYPAL_POST.invoice %>\">\r\n    <input type=\"hidden\" name=\"custom\" value=\"<% $PAYPAL_POST.custom %>\">\r\n    <input type=\"hidden\" name=\"notify_url\" value=\"<% $PAYPAL_POST.notify_url %>\">\r\n    <input type=\"hidden\" name=\"undefined_quantity\" value=\"\">\r\n    <!-- Customer Information -->\r\n    <input type=\"hidden\" name=\"first_name\" value=\"<% $PAYPAL_POST.first_name %>\">\r\n    <input type=\"hidden\" name=\"last_name\" value=\"<% $PAYPAL_POST.last_name %>\">\r\n    <input type=\"hidden\" name=\"adress1\" value=\"<% $PAYPAL_POST.adress1 %>\">\r\n    <input type=\"hidden\" name=\"zip\" value=\"<% $PAYPAL_POST.zip %>\">\r\n    <input type=\"hidden\" name=\"city\" value=\"<% $PAYPAL_POST.city %>\">\r\n    <input type=\"hidden\" name=\"email\" value=\"<% $PAYPAL_POST.email %>\">\r\n    \r\n    </form>\r\n  </div>\r\n</section>\r\n\r\n<script>\r\n//<![CDATA[\r\n// document.paypal_form.submit();\r\n//]]>\r\n</script>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2018-06-06 12:05:24', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:15:\"/de/100156.html\";s:2:\"id\";s:6:\"100156\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_paypal', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<% if ($cmd==\"load_workshop\") %>\r\n  <% include file=\"ws_workshop_detail.tpl\" %>\r\n<%/if%>\r\n\r\n<% if ($cmd==\"load_workshops\") %>\r\n  <% include file=\"ws_workshop_auflistung.tpl\" %>\r\n<%/if%>\r\n\r\n<% if ($cmd==\"\") %>\r\n  <% include file=\"ws_staedte_auflistung.tpl\" %>\r\n<%/if%>\r\n\r\n<% if ($cmd==\'book_now\') %>\r\n  <% include file=\"ws_register.tpl\" %>\r\n<%/if%>\r\n\r\n<% if ($cmd==\'load_paypal\') %>\r\n  <% include file=\"ws_paypal.tpl\" %>\r\n<%/if%>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2016-02-24 11:35:13', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:1:\"/\";s:2:\"id\";s:6:\"100157\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_workshop_modul', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<% if ($cmd==\"load_workshop\") %>\r\n  <% include file=\"ws_workshop_detail.tpl\" %>\r\n<%/if%>\r\n\r\n<% if ($cmd==\"load_workshops\") %>\r\n  <% include file=\"ws_workshop_auflistung.tpl\" %>\r\n<%/if%>\r\n\r\n<% if ($cmd==\"\") %>\r\n  <% include file=\"ws_staedte_auflistung.tpl\" %>\r\n<%/if%>\r\n\r\n<% if ($cmd==\'book_now\') %>\r\n  <% include file=\"ws_register.tpl\" %>\r\n<%/if%>\r\n\r\n<% if ($cmd==\'load_paypal\') %>\r\n  <% include file=\"ws_paypal.tpl\" %>\r\n<%/if%>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:15:\"/de/100157.html\";s:2:\"id\";s:6:\"100157\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ws_workshop_modul', t_precontent=''
