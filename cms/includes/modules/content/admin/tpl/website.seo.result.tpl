<div>

    <div class="row">
        <div class="col-md-6">
            <%include file="cb.panel.header.tpl" title="Info"%>            
                <table class="table">
                <tbody>
                    <tr>
                        <td>Loading Time:</td>
                        <td><%$SEO.loadtime%> Sek.</td>
                    </tr>
                    </tbody>
                </table>
            <%include file="cb.panel.footer.tpl"%>
            <%include file="cb.panel.header.tpl" title="Meta Tags / Angaben"%>            
                <table class="table table-striped table-hover">
                    <tbody>
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
                    </tbody>
                </table>
            <%include file="cb.panel.footer.tpl"%>
        </div><!-- /.col-md-6 -->
        <div class="col-md-6">
            <%include file="cb.panel.header.tpl" title="Important Tags"%>           
                <table class="table table-striped table-hover">
                 <thead><tr>
                    <th>Tag</th>
                    <th>Anzahl Vorkommen</th>
                    <th>Words</th>    
                 </tr></thead>
                 <tbody>
                <% foreach from=$SEO.htags key=htag item=row %>
                 <tr>
                    <td>&lt;<%$htag%>&gt;</td>
                    <td><%$row.count%></td>
                    <td width="600"><%', '|implode:$row.words%></td>
                 </tr>
                <%/foreach%>
                </tbody>
                </table>
            <%include file="cb.panel.footer.tpl"%>
        </div><!-- /.col-md-6 -->
    </div><!-- /.row -->
    
    <%include file="cb.panel.header.tpl" title="Keywords Analyse"%>  
        <table id="keyword-table" class="table table-striped">
         <thead><tr>
            <th>Word</th>
            <th>Score</th>
            <th>Anzahl Vorkommen</th>
         </tr></thead>
         <tbody>
        <% foreach from=$SEO.wordscores item=row %>
         <tr>
            <td><%$row.word%></td>
            <td><%$row.score%></td>
            <td><%$row.count%></td>
         </tr>
        <%/foreach%>
        </tbody>
        </table>

        
      
    <%include file="cb.panel.footer.tpl"%>

</div>


<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="keyword-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>  