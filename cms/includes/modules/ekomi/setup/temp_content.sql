INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<% if (count($EKOMI.items)>0) %>\r\n<h2>eKomi Bewertungen</h2>\r\n<table class=\"tab_std\">\r\n<% foreach from=$EKOMI.items item=row %>\r\n            <tr \">\r\n                    <td><% $row.date%></td>\r\n                    <td><% $row.customer%></td>\r\n                    <td width=\"160\"><%section name=foo start=0 loop=$row.stars step=1%><i class=\"fa fa-star fa-green\">&nbsp;</i><%/section%></td>\r\n                    <td><% $row.review%></td>\r\n            </tr>\r\n<%/foreach%>\r\n</table>\r\n\r\n\r\n<%else%>\r\nKeine Einträge vorhanden.\r\n<%/if%>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:1:\"/\";s:2:\"id\";s:6:\"100033\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ekomi_bewertungen_100033', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<% if (count($EKOMI.items)>0) %>\r\n<h2>eKomi Bewertungen</h2>\r\n<table class=\"tab_std\">\r\n<% foreach from=$EKOMI.items item=row %>\r\n            <tr \">\r\n                    <td><% $row.date%></td>\r\n                    <td><% $row.customer%></td>\r\n                    <td width=\"160\"><%section name=foo start=0 loop=$row.stars step=1%><i class=\"fa fa-star fa-green\">&nbsp;</i><%/section%></td>\r\n                    <td><% $row.review%></td>\r\n            </tr>\r\n<%/foreach%>\r\n</table>\r\n\r\n\r\n<%else%>\r\nKeine Einträge vorhanden.\r\n<%/if%>\r\n', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:15:\"/de/100033.html\";s:2:\"id\";s:6:\"100033\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='ekomi_bewertungen_100033', t_precontent=''
