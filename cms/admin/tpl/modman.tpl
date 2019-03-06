<div class="page-header"><h1><i class="fa fa-cubes"><!----></i> App Manager</h1></div>


<% if ($section=="pool") %>
    <% include file="modman.pool.tpl" %>
<%/if%>

<% if ($section=="appupstart") %>
    <% include file="modman.keidev.tpl" %>
<%/if%>



<% if ($section=="") %>

    <div class="btn-group">
        <a class="btn btn-default" href="javascript:void(0)" onclick="$('#newmod').slideDown()">Neue App</a>  
        <a class="btn btn-default" href="javascript:void(0)" onclick="compile_all_apps();">Apps neu kompilieren</a>
        <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&section=appupstart">App bei Keimeno veröffentlichen</a>
    </div>
    
    <div id="newmod" style="display:none;margin:10px 0px 30px 0px;">
        <form class="modcreate" action="<%$PHPSELF%>" method="post">
            <table class="table table-striped table-hover">
                <tr>
                    <td>Modul Name:</td>
                    <td><input type="text" class="form-control" name="FORM[mod_name]" value="<%$POST.FORM.mod_name|hsc%>"></td>
                </tr>
                <tr>
                    <td>Modul Ident:</td>
                    <td><input type="text" class="form-control" name="FORM[mod_ident]" value="<%$POST.FORM.mod_ident|hsc%>"></td>
                </tr>   
                <tr>
                    <td>Modul Version:</td>
                    <td><input type="text" class="form-control" name="FORM[mod_version]" value="1.0"></td>
                </tr>
            </table>
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="cmd" value="create_mod">
            <input type="submit" class="btn btn-primary" value="create">
        </form>
        <div id="modresult"></div>
    </div><!-- /#newmod -->

    <script>
        function compile_all_apps() {
          execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=compile_all_apps');  
          msg('{LBL_DONE}');
          reload_menu();
        }
        
        function show_create(formData, jqForm, options) {
            show_black_bg();
            return true;
        }
        
        function clear_mod_form() {
            hide_black_bg();
            simple_load('modtable', '<%$PHPSELF%>?epage=<%$epage%>&cmd=reloadmodtable');
        }
    
        var fuoptions = {
            target:  '#modresult',  
            type: 'POST',
            forceSync: true,
            beforeSubmit: show_create,
            success: clear_mod_form
        };
    
        $('.modcreate').submit(function() {
            $(this).ajaxSubmit(fuoptions);
            return false;
        });
    </script> 

    <div id="modtable"><%include file="modman.modtable.tpl"%></div>
<%/if%>