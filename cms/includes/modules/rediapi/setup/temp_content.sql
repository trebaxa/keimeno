INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<% if (count($rediapitable)>0) %>\r\n<section>\r\n  <div class=\"container\">\r\n      <h2>Produkte</h2>\r\n      <%*<%$rediapitable|echoarr%>*%>\r\n      <div class=\"row\">\r\n        <% foreach from=$rediapitable item=row name=prolist %>\r\n      	<div class=\"col-md-3 col-sm-6\">\r\n      					<div class=\"thumbnail\">\r\n      						<img src=\"<%$row.thumb%>\" alt=\"<%$row.pname|sthsc%>\" class=\"img-responsive\">\r\n      						<div class=\"caption text-center\">\r\n      							<h3><%$row.pname%></h3>\r\n      							<p><strong><%$row.vk_eur%> €</strong> pro Stück</p>\r\n      							<p class=\"text-center\">\r\n      								<a href=\"#\" class=\"btn btn-default\"><i class=\"fa fa-shopping-cart\"><!-- Placeholder --></i>&nbsp;Bestellen</a>\r\n      							</p>\r\n      					</div>\r\n      			</div>\r\n      	</div>   \r\n      	 <% if ($smarty.foreach.prolist.iteration % 4 == 0)  %></div><div class=\"row\"><%/if%>\r\n        <%/foreach%>\r\n      </div>\r\n    </div>\r\n</section>\r\n<%/if%>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2015-12-16 14:52:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:1:\"/\";s:2:\"id\";s:6:\"100176\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='fe_rediapi-produkte', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<% if (count($rediapitable)>0) %>\r\n<section>\r\n  <div class=\"container\">\r\n      <h2>Produkte</h2>\r\n      <%*<%$rediapitable|echoarr%>*%>\r\n      <div class=\"row\">\r\n        <% foreach from=$rediapitable item=row name=prolist %>\r\n      	<div class=\"col-md-3 col-sm-6\">\r\n      					<div class=\"thumbnail\">\r\n      						<img src=\"<%$row.thumb%>\" alt=\"<%$row.pname|sthsc%>\" class=\"img-responsive\">\r\n      						<div class=\"caption text-center\">\r\n      							<h3><%$row.pname%></h3>\r\n      							<p><strong><%$row.vk_eur%> €</strong> pro Stück</p>\r\n      							<p class=\"text-center\">\r\n      								<a href=\"#\" class=\"btn btn-default\"><i class=\"fa fa-shopping-cart\"><!-- Placeholder --></i>&nbsp;Bestellen</a>\r\n      							</p>\r\n      					</div>\r\n      			</div>\r\n      	</div>   \r\n      	 <% if ($smarty.foreach.prolist.iteration % 4 == 0)  %></div><div class=\"row\"><%/if%>\r\n        <%/foreach%>\r\n      </div>\r\n    </div>\r\n</section>\r\n<%/if%>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:46', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:15:\"/de/100176.html\";s:2:\"id\";s:6:\"100176\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='fe_rediapi-produkte', t_precontent=''