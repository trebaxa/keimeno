<% if (count($GBLVARS.vars)>0)%>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Globale Variablen</h3><!-- /.panel-title -->
        </div><!-- /.panel-heading -->
        
        
            <form class="jsonform" method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
            <input type="hidden" name="cmd" value="save_page_settings"/>
            <input type="hidden" name="epage" value="gblvars.inc"/>
            <input type="hidden" name="tcid" value="<%$GBLVARS.template.formcontent.id%>"/>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{LBLA_DESCRIPTION}</th>
                        <th>Wert</th>
                    </tr>
                </thead>        
                <% foreach from=$GBLVARS.vars key=varkey item=row %>
                 <% if ($varkey!="") %>
                    <tr>
                        <td><% $row.var_desc %></td>
                        <%include file="gblvars.tablesetting.tpl"%>                  
                    </tr>
                  <%/if%>  
                <%/foreach%>
            </table>
            <%$subbtn%>
        </form>
    </div>    
<%else%>
        
<%/if%>    