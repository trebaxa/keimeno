INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<form role=\"form\" name=\"searchform\" action=\"<% $INDEXSEARCH.searchformurl %>\" method=\"POST\">\r\n<input type=\"hidden\" name=\"cmd\" value=\"indexsearch\">\r\n<img style=\"padding-left:3px;float:right;margin-top:3px;\" onClick=\"document.searchform.submit()\" src=\"/images/opt_sr_btn.gif\"  >\r\n<% if ($POST.setvalue==\"\") %><% assign var=sv value=\"Suchbegriff\" %><% else %><% assign var=sv value=$POST.setvalue %><%/if%>\r\n<input autocomplete=\"off\" id=\"fe-searcher\" name=\"setvalue\" value=\"<% $sv %>\" <%if ($sv==\"Suchbegriff\") %> onFocus=\"javascript:this.value=\'\'\"<%/if%> class=\"form-control\" type=\"text\" class=\"searcher\" size=\"16\" >\r\n</form>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_143', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:10:\"/_143.html\";s:2:\"id\";s:2:\"60\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='website_searchform_index', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<form role=\"form\" name=\"searchform\" action=\"<% $INDEXSEARCH.searchformurl %>\" method=\"POST\">\r\n<input type=\"hidden\" name=\"cmd\" value=\"indexsearch\">\r\n<img style=\"padding-left:3px;float:right;margin-top:3px;\" onClick=\"document.searchform.submit()\" src=\"/images/opt_sr_btn.gif\"  >\r\n<% if ($POST.setvalue==\"\") %><% assign var=sv value=\"Suchbegriff\" %><% else %><% assign var=sv value=$POST.setvalue %><%/if%>\r\n<input autocomplete=\"off\" id=\"fe-searcher\" name=\"setvalue\" value=\"<% $sv %>\" <%if ($sv==\"Suchbegriff\") %> onFocus=\"javascript:this.value=\'\'\"<%/if%> class=\"form-control\" type=\"text\" class=\"searcher\" size=\"16\" >\r\n</form>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:45', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_143', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:13:\"/de/_143.html\";s:2:\"id\";s:2:\"60\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='website_searchform_index', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<h1><%$POST.setvalue%></h1>\r\n<div id=\"indexsearch\">\r\n<span class=\"small\"><%$SE.search_count %> Ergebnisse (<%$SE.search_time%> Sekunden)</span>\r\n<% foreach from=$SE.search_result item=item %>\r\n<div class=\"searchrow\">\r\n<h1><a title=\"<% $item.s_title %>\" href=\"<% $item.s_url %>\"><%$item.s_title%></a></h1>\r\n<%$item.s_short%>\r\n<br><a title=\"<% $item.s_title %>\" href=\"<% $item.s_url %>\" class=\"url\"><% $item.s_url %></a>\r\n</div>\r\n<%/foreach%>\r\n</div>', content_plain='', linkname='', use_all_lang='0', meta_desc='Suchergebnisse', meta_keywords='suche,trebaxa,seitenindex', theme_image='', meta_title='Suchergebnis', t_icon='', t_lastchange='2017-03-16 12:00:45', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_205', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:13:\"/de/_205.html\";s:2:\"id\";s:2:\"50\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='indexsearch', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<h1><%$POST.setvalue%></h1>\r\n<div id=\"indexsearch\">\r\n<span class=\"small\"><%$SE.search_count %> Ergebnisse (<%$SE.search_time%> Sekunden)</span>\r\n<% foreach from=$SE.search_result item=item %>\r\n<div class=\"searchrow\">\r\n<h1><a title=\"<% $item.s_title %>\" href=\"<% $item.s_url %>\"><%$item.s_title%></a></h1>\r\n<%$item.s_short%>\r\n<br><a title=\"<% $item.s_title %>\" href=\"<% $item.s_url %>\" class=\"url\"><% $item.s_url %></a>\r\n</div>\r\n<%/foreach%>\r\n</div>', content_plain='', linkname='', use_all_lang='0', meta_desc='Suchergebnisse', meta_keywords='suche,trebaxa,seitenindex', theme_image='', meta_title='Suchergebnis', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_205', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:10:\"/_205.html\";s:2:\"id\";s:2:\"50\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='indexsearch', t_precontent=''
