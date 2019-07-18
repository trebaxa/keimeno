<link rel="stylesheet" href="../includes/modules/ktracker/admin/css/style.css" type="text/css"/>

<div class="page-header"><h1>kTracker</h1></div>

<div class="tab-content">

<div class="btn-group mb-lg">
    <a href="<%$eurl%>" class="btn btn-secondary ajax-link"><i class="fa fa-table"></i> Kampanien</a>
    <a href="<%$eurl%>cmd=add" class="btn btn-secondary"><i class="fa fa-plus"></i> Neu</a>
</div>

<% if ($cmd=="" || $cmd=="add") %>

<% if (count($KTRACKER.compains)>0)%>
         <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="save_table" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          <input type="hidden" value="<%$GET.gid%>" name="gid" />
          
      <table class="table table-striped table-hover" id="feedback-table">
            <thead>
                <tr>
                    <th>Compain</th>                    
                    <th>Page</th>                    
                    <th>Link</th>
                    <th>Clicks</th>
                    <th></th>
                </tr>
            </thead>
            
            <% foreach from=$KTRACKER.compains item=row %>
                <tr>
                    <td><input required="" type="text" class="form-control" value="<% $row.k_title|sthsc%>" name="FORM[<%$row.KID%>][k_title]" /></td>                    
                    <td>
                    <% if (count($KTRACKER.websitetree)>0) %>
                        <select name="FORM[<%$row.KID%>][k_page_id]" class="form-control">
                           <% foreach from=$KTRACKER.websitetree item=page key=pid %>
                            <option <% if ($row.k_page_id==$pid) %>selected<%/if%> value="<%$pid%>"><%$page%></option>
                           <%/foreach%> 
                        </select>
                    <%else%>
                        -
                    <%/if%>
                    </td>           
                    <td><a href="<%$row.k_link%>?ktrack=<%$row.KID%>" target="_blank"><%$row.k_link%>?ktrack=<%$row.KID%></a></td>
                    <td><%$row.total_clicks.KSUM%></td>         
                    <td class="text-right">
                     <div class="btn-group">
                        <% foreach from=$row.icons item=picon %><% $picon %><%/foreach%>
                        <a href="<%$eurl%>cmd=load_chart&id=<%$row.KID%>" class="btn btn-secondary ajax-link"><i class="fa fa-eye"></i></a>
                     </div>   
                    </td> 
                </tr>
            <%/foreach%>
        </table>
        <%$subbtn%>
        </form>
<%else%>
    <div class="alert alert-info">Keine Compains angelegt</div>
<%/if%>
<%/if%>    


<% if ($cmd=="load_chart") %>
 <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="panel-title">Besucher</h3>
                            </div>    
                            <div class="col-md-6 text-right">
                                <div class="dropdown">
                                  <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                    Zeitraum
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_ktracker_chart(31);">31 Tage</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_ktracker_chart(90);">3 Monate</a></li>                        
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_ktracker_chart(180);">6 Monate</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_ktracker_chart(365);">1 Jahr</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_ktracker_chart(730);">2 Jahre</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="load_ktracker_chart(1095);">3 Jahre</a></li>
                                  </ul>
                                </div>
                              </div>
                           </div>
                    </div><!-- /.panel-heading -->
                    <div class="panel-body">                    
                        <div class="flotchart-container" id="visitor-counter-cont">
                            <div id="visitor-counter" class="flot-placeholder"></div>
                        </div><!-- /.flotchart-container #visitor-counter-cont -->
                    </div><!-- /.panel-body -->
                </div><!-- /.panel .panel-default -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
<script type="text/javascript">
function load_ktracker_chart(days) {
    load_flot_chart("<%$eurl%>cmd=load_ktracker_chart&id=<%$GET.id%>&days="+days,"visitor-counter","68%","230px",1,"");
}    
load_ktracker_chart(31);
</script>
<%/if%>
</div>