<div>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Info</h3><!-- /.panel-title -->
                </div><!-- /.panel-heading -->
                <table class="table">
                    <tr>
                        <td>Loading Time:</td>
                        <td><%$SEO.loadtime%> Sek.</td>
                    </tr>
                </table>
            </div><!-- /.panel panel-default -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Meta Tags / Angaben</h3><!-- /.panel-title -->
                </div><!-- /.panel-heading -->
                <table class="table table-striped table-hover">
                    <tr>
                        <th>Title:</th>
                        <td><% if ($SEO.metas.title.value=="") %><%$SEO.metas.searchtitle.value%><%else%><%$SEO.metas.title.value%><%/if%></td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td><%$SEO.metas.description.value%></td>
                    </tr>
                    <tr>
                        <th>Keywords:</th>
                        <td><p class="break-word"><%$SEO.metas.keywords.value%></p></td>
                    </tr>
                </table>
            </div><!-- /.panel panel-default -->
        </div><!-- /.col-md-6 -->
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Important Tags</h3><!-- /.panel-title -->
                </div><!-- /.panel-heading -->
                <table class="table table-striped table-hover">
                 <thead><tr>
                    <th>Tag</th>
                    <th>Anzahl Vorkommen</th>
                    <th>Words</th>    
                 </tr></thead>
                <% foreach from=$SEO.htags key=htag item=row %>
                 <tr>
                    <td>&lt;<%$htag%>&gt;</td>
                    <td><%$row.count%></td>
                    <td width="600"><%', '|implode:$row.words%></td>
                 </tr>
                <%/foreach%>
                </table>
            </div><!-- /.panel panel-default -->
        </div><!-- /.col-md-6 -->
    </div><!-- /.row -->
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Keywords Analyse</h3><!-- /.panel-title -->
        </div><!-- /.panel-heading -->
    
        <table id="keyword-table" class="table table-striped">
         <thead><tr>
            <th>Word</th>
            <th>Score</th>
            <th>Anzahl Vorkommen</th>
         </tr></thead>
        <% foreach from=$SEO.wordscores item=row %>
         <tr>
            <td><%$row.word%></td>
            <td><%$row.score%></td>
            <td><%$row.count%></td>
         </tr>
        <%/foreach%>
        </table>

<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="keyword-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>  
        
      
    </div><!-- /.panel panel-default -->

</div><!-- / -->