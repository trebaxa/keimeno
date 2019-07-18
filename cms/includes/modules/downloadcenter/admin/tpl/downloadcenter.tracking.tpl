<h3>{LBL_FILE} <% $doc_center.track_file.title %></h3>
<% if (count($doc_center.tracking)>0)%>
        
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">                                                   
                    <h2>Besucher</h2>                            
                    <div class="filter">
                      <div id="reportrange" class="pull-right" >
                        <div class="dropdown">
                                  <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                    Zeitraum
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_dcchart(31);">31 Tage</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_dcchart(90);">3 Monate</a></li>                        
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_dcchart(180);">6 Monate</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_dcchart(365);">1 Jahr</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_dcchart(730);">2 Jahre</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_dcchart(1095);">3 Jahre</a></li>
                                  </ul>
                                </div><!-- dropdown -->
                              </div>
                           </div><!-- filter -->
                           <div class="clearfix"></div>
                    </div><!-- /.panel-heading -->
                    <div class="x_content">                    
                        <div class="flotchart-container" id="dc-counter-cont">
                            <div id="dc-counter" class="flot-placeholder" style="width:100%"> </div>    
                        </div><!-- /.flotchart-container #visitor-counter-cont -->
                    </div><!-- /.panel-body -->
                </div><!-- /.panel .panel-default -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->

<script type="text/javascript">
function load_dcchart(days) {
    load_flot_chart("<%$PHPSELF%>?epage=<%$epage%>&cmd=load_dc_chart&id=<% $smarty.get.id %>&days="+days,"dc-counter","100%","230px",1,"<%$curr_lettercode%>");
}    
$( document ).ready(function() {
    load_dcchart(31);
});
</script>





          
            <table class="table table-striped table-hover" id="doc-track-table">
               <thead> 
                <tr>
                    <th>{LBL_DATE}</th>
                    <th class="text-right">{LBL_DOWNLOADS}</th>
                </tr>
               </thead>
               <tbody> 
                <% foreach from=$doc_center.tracking item=track  %>
                    <tr>
                        <td><% $track.dcdate %></td>
                        <td class="text-right"><% $track.hits %></td>
                    </tr>
                <%/foreach %>
                </tbody>
            </table>
        <%* Tabellen Sortierungs Script *%>
        <%assign var=tablesortid value="doc-track-table" scope="global"%>
        <%include file="table.sorting.script.tpl"%>  
<%else%>
    <div class="alert alert-info">Es liegen noch keine Werte vor.</div>
<%/if%>