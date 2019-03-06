<% if ($section=="stylelive") %>

    
        <div class="page-header"><h1><i class="fa fa-pencil-square-o"><!----></i> Layout Manager</h1></div>
    
        <div class="btn-group">
            <a class="btn btn-default" href="javascript:void(0);" onclick="show_csscol();">CSS Collection</a>
            <!--  <a class="btn btn-default" href="javascript:void(0);" onclick="show_css();">CSS Editor</a> -->
            <a class="btn btn-default" href="javascript:void(0);" onclick="show_backups();">Backups</a>
            <a class="btn btn-default" href="javascript:void(0);" onclick="show_template_backup();">Template Backup</a>
        </div><!-- /.btn-group -->
    
        <div class="csscoll" id="csscol"><%include file="layout.csscol.tpl"%></div>
        <div class="csscoll" id="backupsdiv"></div>
        <div class="csscoll" id="tplbackup"></div>
        
 

    
    <script>
        function show_csscol() {
             $('.csscol').hide();
             $('#csscol').show();
             load_css_tree();
        }
        
        function show_backups() {
            $('.csscoll').hide();
            $('#backupsdiv').show();    
            simple_load('backupsdiv','<%$PHPSELF%>?epage=<%$epage%>&cmd=loadbackups');  
        }

        function show_template_backup() {
            $('.csscoll').hide();
            $('#tplbackup').show();    
            simple_load('tplbackup','<%$PHPSELF%>?epage=<%$epage%>&cmd=show_template_backup');  
        }        
        show_csscol();
    </script>

<%/if%>

<% if ($section=="showpics") %>
    <script>
        function show_upload_req(formData, jqForm, options) {
        $('body').prepend('<div class="global_black"></div>');
        $('.global_black').css('height', $(document).height() + 'px');
            $('.global_black').css("z-index", 999);
            $('.global_black').show();
            return true;
        }
        
        function clear_file_upload() {
            $('.global_black').remove();
        }
        
        function delete_fup(ident) {
            simple_load('imglist','<%$PHPSELF%>?epage=<%$epage%>&cmd=dellaypic&ident='+ident);
            return false;
        }
            var fuoptions = {
                target:        '#imglist',  
                type: 'POST',
                forceSync: true,
                beforeSubmit: show_upload_req,
                success: clear_file_upload
            };
    </script>
    <div id="imglist"><%include file="layout.img.tpl"%></div>
<%/if%>