<div class="page-header">
    <h1><i class="fa fa-cloud"><!----></i>Web Explorer</h1>
</div><!-- /.page-header -->

<% if ($aktion=='') %>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{LA_IHREDATEIEN}</h3><!-- /.panel-title -->
        </div><!-- /.panel-heading -->
        <iframe src="../cjs/ResponsiveFilemanager/filemanager/dialog.php?type=2&editor=mce_0&lang=eng&fldr=" style="width:100%;height:800px;border:0px"></iframe>
    </div><!-- /.panel panel-default -->
<%/if%>

<% if ($aktion=='analyze_dirs') %>

<div class="row">
    <div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">TITEL</h3><!-- /.panel-title -->
        </div><!-- /.panel-heading -->

        <label>Verzeichnis: </label>
        <select class="form-control"  onChange="location.href=this.options[this.selectedIndex].value">
            <option <% if ($GET.dir=='fs') %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&aktion=analyze_dirs&dir=fs">File Server</option>
            <option <% if ($GET.dir=='img') %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&aktion=analyze_dirs&dir=img">CMS System Bilder</option>
            <option <% if ($GET.dir=='js') %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&aktion=analyze_dirs&dir=js">Java Plugins</option>
            <option <% if ($GET.dir=='fd') %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&aktion=analyze_dirs&dir=fd">File Data</option>
            <option <% if ($GET.dir=='db') %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&aktion=analyze_dirs&dir=db">DB Backups</option>
        </select>
    
        <table class="table table-striped table-hover" >
        <tr>
            <td>Gesamter Speicherplatz:</td>
            <td class="text-right"> <% $analyzer.total_fs|hfs %></td>
        </tr>
        <tr>
            <td>Anzahl Dateien:</td>
            <td class="text-right"> <% $analyzer.count_files %></td>
        </tr>
        <tr>
            <td>Anzahl Verzeichnisse:</td>
            <td class="text-right"> <% $analyzer.count_dirs %></td>
        </tr>
        </table>
        
        </div><!-- /.panel panel-default -->
    </div><!-- /.col-md-6 -->
</div><!-- /.row -->

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Webspace Verteilung</h3><!-- /.panel-title -->
        </div><!-- /.panel-heading -->
    
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Verzeichnis</th>
                    <th>Speicherplatz</th>
                </tr>
            </thead>
            <% foreach from=$analyzer.total_fs_dir item=dir %>
                <tr>
                    <td><%$dir.dir%></td>
                    <td class="text-right"><%$dir.total_fs|hfs%></td>
                </tr>
            <%/foreach%>
        </table>
    </div><!-- /.panel panel-default -->

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Medien Verteilung</h3><!-- /.panel-title -->
        </div><!-- /.panel-heading -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Endung</th>
                    <th class="text-right">Speicherplatz</th>
                </tr>
            </thead>
            <% foreach from=$analyzer.file_extentions key=ext item=fe %>
                <tr>
                    <td><%$ext%></td>
                    <td class="text-right"><%$fe|hfs%></td>
                </tr>
            <%/foreach%>
        </table>
    </div><!-- /.panel panel-default -->

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Dateien Verteilung</h3><!-- /.panel-title -->
        </div><!-- /.panel-heading -->
        <table class="table table-striped">
            <% $analyzer.file_tree %>
        </table>
    </div><!-- /.panel panel-default -->

<%/if%>