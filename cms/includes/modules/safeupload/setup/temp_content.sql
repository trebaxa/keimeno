INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='1', content='<% if ($customer.kid>0) %>\r\n<script src=\"/includes/modules/safeupload/js/dropzone/dropzone.js\"></script>\r\n<link rel=\"stylesheet\" href=\"/includes/modules/safeupload/js/dropzone/dropzone.css\">\r\n    <% if ($cmd==\"\") %>\r\n    <section>\r\n      <div class=\"container\">\r\n        <h3>Dateien hochladen</h3>\r\n        \r\n        <div class=\"dropzonecss\" id=\"js-customer-dropzone\" data-cont_matrix_id=\"<%$cont_matrix_id%>\">\r\n            Drag & Drop Dateien hier\r\n        </div>\r\n        <div id=\"dropzonefeedback\"></div>\r\n        <div id=\"js-customer-files\"></div>\r\n\r\n        <small>Upload für <%$customer.vorname%> <%$customer.nachname%>, KNR: <%$customer.kid%> | Maximale Datei Größe: <%$SAFEUPLOAD.upload_max_filesize%> |  Maximale Datei Post Größe: <%$SAFEUPLOAD.post_max_size%></small>\r\n        \r\n       <div id=\"js-su-files\"></div>\r\n      </div>\r\n    </section>\r\n    <%/if%>\r\n    \r\n    <% if ($cmd==\"reload_customer_files\") %>\r\n     \r\n      <% if (count($SAFEUPLOAD.files)>0) %>\r\n      <h3>Ihre Dateien</h3>\r\n      <table class=\"table table-hover table-striped\">\r\n        <thead>\r\n          <tr>\r\n            <th>Datei</th>\r\n            <th>Größe</th>\r\n            <th>Datum</th>\r\n            <th></th>\r\n          </tr>\r\n        </thead>\r\n        <tbody>\r\n      <% foreach from=$SAFEUPLOAD.files item=row %>\r\n          <tr>\r\n              <td><a title=\"Download <%$row.file%>\" href=\"<%$eurl%>cmd=user_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>\"><%$row.file%></a></td>\r\n              <td><%$row.size%></td>\r\n              <td><%$row.date%></td>\r\n              <td class=\"text-right\"><a title=\"Download <%$row.file%>\" href=\"<%$eurl%>cmd=user_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>\" class=\"btn btn-default btn-sm\"><i class=\"fa fa-download\"></i></a></td>\r\n          </tr>\r\n      <%/foreach%>\r\n      </tbody>\r\n      </table>\r\n      <%/if%>\r\n    <%/if%>\r\n\r\n\r\n\r\n<%else%>\r\n  <div class=\"alert alert-info\">Bitte melden Sie sich an.</div>\r\n<%/if%>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2019-03-11 14:23:55', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:1:\"/\";s:2:\"id\";s:6:\"100191\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='safeupload_su-upload-form', t_precontent=''
INSERT INTO `!!TBL_PREFIX!!temp_content` SET lang_id='2', content='<% if ($customer.kid>0) %>\r\n<script src=\"/includes/modules/safeupload/js/dropzone/dropzone.js\"></script>\r\n<link rel=\"stylesheet\" href=\"/includes/modules/safeupload/js/dropzone/dropzone.css\">\r\n    <% if ($cmd==\"\") %>\r\n    <section>\r\n      <div class=\"container\">\r\n        <h3>Dateien hochladen</h3>\r\n        \r\n        <div class=\"dropzonecss\" id=\"js-customer-dropzone\" data-cont_matrix_id=\"<%$cont_matrix_id%>\">\r\n            Drag & Drop Dateien hier\r\n        </div>\r\n        <div id=\"dropzonefeedback\"></div>\r\n        <div id=\"js-customer-files\"></div>\r\n\r\n        <small>Upload für <%$customer.vorname%> <%$customer.nachname%>, KNR: <%$customer.kid%> | Maximale Datei Größe: <%$SAFEUPLOAD.upload_max_filesize%> |  Maximale Datei Post Größe: <%$SAFEUPLOAD.post_max_size%></small>\r\n        \r\n       <div id=\"js-su-files\"></div>\r\n      </div>\r\n    </section>\r\n    <%/if%>\r\n    \r\n    <% if ($cmd==\"reload_customer_files\") %>\r\n     \r\n      <% if (count($SAFEUPLOAD.files)>0) %>\r\n      <h3>Ihre Dateien</h3>\r\n      <table class=\"table table-hover table-striped\">\r\n        <thead>\r\n          <tr>\r\n            <th>Datei</th>\r\n            <th>Größe</th>\r\n            <th>Datum</th>\r\n            <th></th>\r\n          </tr>\r\n        </thead>\r\n        <tbody>\r\n      <% foreach from=$SAFEUPLOAD.files item=row %>\r\n          <tr>\r\n              <td><a title=\"Download <%$row.file%>\" href=\"<%$eurl%>cmd=user_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>\"><%$row.file%></a></td>\r\n              <td><%$row.size%></td>\r\n              <td><%$row.date%></td>\r\n              <td class=\"text-right\"><a title=\"Download <%$row.file%>\" href=\"<%$eurl%>cmd=user_file_download&kid=<%$GET.kid%>&hash=<%$row.hash%>\" class=\"btn btn-default btn-sm\"><i class=\"fa fa-download\"></i></a></td>\r\n          </tr>\r\n      <%/foreach%>\r\n      </tbody>\r\n      </table>\r\n      <%/if%>\r\n    <%/if%>\r\n\r\n\r\n\r\n<%else%>\r\n  <div class=\"alert alert-info\">Bitte melden Sie sich an.</div>\r\n<%/if%>', content_plain='', linkname='', use_all_lang='0', meta_desc='', meta_keywords='', theme_image='', meta_title='', t_icon='', t_lastchange='2019-03-11 14:23:55', t_header_html='', t_imgthemealt='', t_imgthemetitle='', t_themedescription='', t_htalinklabel='', t_breadcrumb='/', t_breadcrumb_arr='a:1:{i:0;a:5:{s:5:\"label\";s:0:\"\";s:6:\"parent\";s:1:\"0\";s:4:\"link\";s:15:\"/de/100191.html\";s:2:\"id\";s:6:\"100191\";s:8:\"approved\";s:1:\"1\";}}', t_themegaltpl='', t_themegalid='9620', t_ticroppos='Center', t_tiwidth='0', t_tiheight='0', t_alt_title='', t_gblvars='', t_tpl_name='safeupload_su-upload-form', t_precontent=''
