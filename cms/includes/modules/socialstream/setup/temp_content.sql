INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<%*$socialmediastream|echoarr*%>\r\n<section>\r\n  <div class=\"container\">\r\n    <div class=\"main-title text-center centered\">\r\n      <h2 class=\"grey\">Redimero Aktuell</h2>\r\n    </div>\r\n      <div class=\"row\">\r\n          <% foreach from=$socialmediastream item=row name=redifbloop %>\r\n          <div class=\"col-md-4\">\r\n            <div class=\"panel panel-default\">\r\n              <div class=\"panel-heading\">\r\n                <div class=\"row\">\r\n                <div class=\"col-md-6 <% if ($row.socialtype==\'flickr\') %>flickr<%/if%><% if ($row.socialtype==\'fb\') %>facebook<%/if%><% if ($row.socialtype==\'tw\') %>twitter<%/if%>-bg\">\r\n                  <h4><i class=\"fa fa-<% if ($row.socialtype==\'flickr\') %>flickr<%/if%><% if ($row.socialtype==\'fb\') %>facebook<%/if%><% if ($row.socialtype==\'tw\') %>twitter<%/if%>\">&nbsp;</i>\r\n                  <% if ($row.socialtype==\'flickr\') %>flickr<%/if%><% if ($row.socialtype==\'fb\') %>facebook<%/if%><% if ($row.socialtype==\'tw\') %>twitter<%/if%></h4>\r\n                </div>\r\n                <div class=\"col-md-6 text-right\">\r\n                  <% if ($row.beforexmin<60) %>\r\n                      vor <%$row.beforexmin%> Minuten\r\n                  <%/if%>\r\n                  <% if ($row.beforexhours<24) %>\r\n                      vor <%$row.beforexhours%> Stunden\r\n                  <%/if%>\r\n                 \r\n                  <% if ($row.beforexdays<=31) %>\r\n                      vor <%$row.beforexdays%> Tagen\r\n                  <%/if%>  \r\n                  <% if ($row.beforexmonths>=1) %>\r\n                      vor <%$row.beforexmonths%> Monate(n)\r\n                  <%/if%>          \r\n                  </div>\r\n                </div>  \r\n              </div>\r\n              \r\n              <div class=\"panel-body\" style=\"min-height: 550px;\">\r\n               <% if ($row.isvideo==1) %>\r\n                <% if ($row.video_type==\'FB\') %>\r\n                 <iframe src=\"https://www.facebook.com/plugins/video.php?href=<%$row.video_url%>&width=640&show_text=false&appId=<%$FBWP.WP.fb_appid%>&height=480\" width=\"100%\" height=\"240\" style=\"border:none;overflow:hidden\" scrolling=\"no\" frameborder=\"0\" allowTransparency=\"true\"></iframe>\r\n                <%/if%>\r\n                <% if ($row.video_type==\'YT\') %>\r\n                 <iframe width=\"100%\" height=\"240\" src=\"<%$row.source%>\" frameborder=\"0\" allowfullscreen></iframe>\r\n                <%/if%> \r\n               <%else%>\r\n                <figure><a href=\"<%$row.post_link%>\" title=\"<%$row.text|sthsc|truncate:30%>\">\r\n                  <% if ($row.thumb!=\"\") %><img src=\"<%$row.thumb%>\" alt=\"<%$row.text|sthsc|truncate:30%>\" class=\"img-responsive img-hover mb-sm\"><%/if%>\r\n                <%/if%>\r\n                \r\n                  <%*<h3><%$row.story%></h3>*%>\r\n                <p><%$row.text|nl2br|truncate:600%></p>\r\n              </div>\r\n              \r\n              <div class=\"panel-footer\">\r\n                <div class=\"text-right\"><a target=\"_blank\" href=\"<%$row.post_link%>\" class=\"btn btn-default btn-xs\" title=\"<%$row.text|sthsc|truncate:30%>\">...mehr</a></div>\r\n              </div>\r\n              \r\n            </div>\r\n            </div>   \r\n          <% if $smarty.foreach.redifbloop.iteration is div by 3 %></div><div class=\"row\"><%/if%>  \r\n        <%/foreach%>\r\n      </div>\r\n    </div>\r\n</section>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2016-04-25 18:30:17', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_453', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:10:\"/_453.html\";s:2:\"id\";s:4:\"9590\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='social_media_stream', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<%*$socialmediastream|echoarr*%>\r\n<section>\r\n  <div class=\"container\">\r\n    <div class=\"main-title text-center centered\">\r\n      <h2 class=\"grey\">Redimero Aktuell</h2>\r\n    </div>\r\n      <div class=\"row\">\r\n          <% foreach from=$socialmediastream item=row name=redifbloop %>\r\n          <div class=\"col-md-4\">\r\n            <div class=\"panel panel-default\">\r\n              <div class=\"panel-heading\">\r\n                <div class=\"row\">\r\n                <div class=\"col-md-6 <% if ($row.socialtype==\'flickr\') %>flickr<%/if%><% if ($row.socialtype==\'fb\') %>facebook<%/if%><% if ($row.socialtype==\'tw\') %>twitter<%/if%>-bg\">\r\n                  <h4><i class=\"fa fa-<% if ($row.socialtype==\'flickr\') %>flickr<%/if%><% if ($row.socialtype==\'fb\') %>facebook<%/if%><% if ($row.socialtype==\'tw\') %>twitter<%/if%>\">&nbsp;</i>\r\n                  <% if ($row.socialtype==\'flickr\') %>flickr<%/if%><% if ($row.socialtype==\'fb\') %>facebook<%/if%><% if ($row.socialtype==\'tw\') %>twitter<%/if%></h4>\r\n                </div>\r\n                <div class=\"col-md-6 text-right\">\r\n                  <% if ($row.beforexmin<60) %>\r\n                      vor <%$row.beforexmin%> Minuten\r\n                  <%/if%>\r\n                  <% if ($row.beforexhours<24) %>\r\n                      vor <%$row.beforexhours%> Stunden\r\n                  <%/if%>\r\n                 \r\n                  <% if ($row.beforexdays<=31) %>\r\n                      vor <%$row.beforexdays%> Tagen\r\n                  <%/if%>  \r\n                  <% if ($row.beforexmonths>=1) %>\r\n                      vor <%$row.beforexmonths%> Monate(n)\r\n                  <%/if%>          \r\n                  </div>\r\n                </div>  \r\n              </div>\r\n              \r\n              <div class=\"panel-body\" style=\"min-height: 550px;\">\r\n               <% if ($row.isvideo==1) %>\r\n                <% if ($row.video_type==\'FB\') %>\r\n                 <iframe src=\"https://www.facebook.com/plugins/video.php?href=<%$row.video_url%>&width=640&show_text=false&appId=<%$FBWP.WP.fb_appid%>&height=480\" width=\"100%\" height=\"240\" style=\"border:none;overflow:hidden\" scrolling=\"no\" frameborder=\"0\" allowTransparency=\"true\"></iframe>\r\n                <%/if%>\r\n                <% if ($row.video_type==\'YT\') %>\r\n                 <iframe width=\"100%\" height=\"240\" src=\"<%$row.source%>\" frameborder=\"0\" allowfullscreen></iframe>\r\n                <%/if%> \r\n               <%else%>\r\n                <figure><a href=\"<%$row.post_link%>\" title=\"<%$row.text|sthsc|truncate:30%>\">\r\n                  <% if ($row.thumb!=\"\") %><img src=\"<%$row.thumb%>\" alt=\"<%$row.text|sthsc|truncate:30%>\" class=\"img-responsive img-hover mb-sm\"><%/if%>\r\n                <%/if%>\r\n                \r\n                  <%*<h3><%$row.story%></h3>*%>\r\n                <p><%$row.text|nl2br|truncate:600%></p>\r\n              </div>\r\n              \r\n              <div class=\"panel-footer\">\r\n                <div class=\"text-right\"><a target=\"_blank\" href=\"<%$row.post_link%>\" class=\"btn btn-default btn-xs\" title=\"<%$row.text|sthsc|truncate:30%>\">...mehr</a></div>\r\n              </div>\r\n              \r\n            </div>\r\n            </div>   \r\n          <% if $smarty.foreach.redifbloop.iteration is div by 3 %></div><div class=\"row\"><%/if%>  \r\n        <%/foreach%>\r\n      </div>\r\n    </div>\r\n</section>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2017-03-16 12:00:45', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='_453', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:13:\"/de/_453.html\";s:2:\"id\";s:4:\"9590\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='social_media_stream', t_precontent=''