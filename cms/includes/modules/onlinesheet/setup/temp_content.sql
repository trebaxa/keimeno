INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<% if ($cmd==\'osdone\') %>\r\n   <h3>Ihr Auftragsnummer lautet: <% $order_obj.AID %></h3>\r\n   <div class=\"row\">\r\n     <div class=\"col-md-12 text-center\">\r\n       <%$sheet_obj.t_donemsg%>\r\n     </div> \r\n   </div> \r\n<%else%>\r\n  <form role=\"form\" action=\"<%$PHPSELF%>\" method=\"POST\">\r\n    <input type=\"hidden\" name=\"cmd\" value=\"send_os_sheet\">\r\n    <input type=\"hidden\" name=\"page\" value=\"<%$page%>\">\r\n      {TPL_FORM_TABLE}\r\n      <div class=\"row\">\r\n        <div class=\"col-md-12 text-center\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Auftrag senden</button>\r\n      </div>\r\n    </div>\r\n  </form>\r\n<%/if%>', content_plain='Ihr Auftragsnummer lautet: Antrag JETZT ausdrucken // ; //]]> ', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_58', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:9:\"/_58.html\";s:2:\"id\";s:3:\"440\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='os_form', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<% if ($cmd==\'osdone\') %>\r\n   <h3>Ihr Auftragsnummer lautet: <% $order_obj.AID %></h3>\r\n   <div class=\"row\">\r\n     <div class=\"col-md-12 text-center\">\r\n       <%$sheet_obj.t_donemsg%>\r\n     </div> \r\n   </div> \r\n<%else%>\r\n  <form role=\"form\" action=\"<%$PHPSELF%>\" method=\"POST\">\r\n    <input type=\"hidden\" name=\"cmd\" value=\"send_os_sheet\">\r\n    <input type=\"hidden\" name=\"page\" value=\"<%$page%>\">\r\n      {TPL_FORM_TABLE}\r\n      <div class=\"row\">\r\n        <div class=\"col-md-12 text-center\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Auftrag senden</button>\r\n      </div>\r\n    </div>\r\n  </form>\r\n<%/if%>', content_plain='Ihr Auftragsnummer lautet: Antrag JETZT ausdrucken // ; //]]> ', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:45', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_58', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:12:\"/de/_58.html\";s:2:\"id\";s:3:\"440\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='os_form', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<html>\r\n<head>\r\n.box {\r\n    width:800px;\r\n    padding:10px;\r\n    border:1px solid #000000;\r\n}\r\n</head>\r\n\r\n<body>\r\n<div class=\"box\">\r\n<div align=\"right\">\r\n         <% $gbl_config.adr_firma %><br>          \r\n         <% $gbl_config.adr_street %> <% $gbl_config.adr_hausnr %><br> \r\n         <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %><br>          \r\n          TEL. <% $gbl_config.adr_telefon %><br>\r\n          FAX <% $gbl_config.adr_fax %><br>\r\n          <span class=\"small\"><% $gbl_config.adr_telkosten %></span>\r\n          <br>EMAIL: <% $gbl_config.adr_service_email %>\r\n</div>\r\n\r\n<h1 >Antrag: <%$OSHEET.AID%></h1>\r\n<br>\r\n{TPL_OSHEET_CONTENT}\r\n</div>\r\n\r\n</body>\r\n</html>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_59', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:9:\"/_59.html\";s:2:\"id\";s:4:\"9700\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='os_pdfsheet', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<html>\r\n<head>\r\n.box {\r\n    width:800px;\r\n    padding:10px;\r\n    border:1px solid #000000;\r\n}\r\n</head>\r\n\r\n<body>\r\n<div class=\"box\">\r\n<div align=\"right\">\r\n         <% $gbl_config.adr_firma %><br>          \r\n         <% $gbl_config.adr_street %> <% $gbl_config.adr_hausnr %><br> \r\n         <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %><br>          \r\n          TEL. <% $gbl_config.adr_telefon %><br>\r\n          FAX <% $gbl_config.adr_fax %><br>\r\n          <span class=\"small\"><% $gbl_config.adr_telkosten %></span>\r\n          <br>EMAIL: <% $gbl_config.adr_service_email %>\r\n</div>\r\n\r\n<h1 >Antrag: <%$OSHEET.AID%></h1>\r\n<br>\r\n{TPL_OSHEET_CONTENT}\r\n</div>\r\n\r\n</body>\r\n</html>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:45', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_59', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:12:\"/de/_59.html\";s:2:\"id\";s:4:\"9700\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='os_pdfsheet', t_precontent=''
