<% if ($cmd=='') %>
<div class="page-header"><h1>Auswertungen</h1></div>
<div class="btn-group">
    <a class="btn btn-secondary" href="javascript:void(0)" onclick="load_ref();">Referer</a>  
    <a class="btn btn-secondary" href="javascript:void(0)" onclick="load_wio();">Wer ist online?</a>  
    <a class="btn btn-secondary" href="javascript:void(0)" onclick="load_se();">Suchmaschinen</a>  
    <a class="btn btn-secondary" href="javascript:void(0)" onclick="load_visitors();">Besucher</a> 
    <a class="btn btn-secondary" href="javascript:void(0)" onclick="load_browsers();">Browser</a>
</div>

<%include file="cb.panel.header.tpl" title="Auswertung"%>
<div id="statcont"></div>

<script>
    function load_se() {
        simple_load('statcont','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_se');
    }
    function load_wio() {
        simple_load('statcont','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_wio');
    }
    function load_ref() {
        simple_load('statcont','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_ref');
    }
    function load_visitors() {
        simple_load('statcont','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_visitors');
    }
    function load_browsers() {
        simple_load('statcont','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_browsers');
    }
    
    <% if ($cmd=="") %>    
        load_ref();
    <%/if%>

</script>
<%include file="cb.panel.footer.tpl" %>
<%/if%>

<% if ($cmd=='load_visitors') %>
<script>
    load_flot_chart("<%$PHPSELF%>?epage=welcome.inc&cmd=load_visitor_chart","visitor-counter","100%","230px",1,"<%$curr_lettercode%>");            
</script>
<div style="width:70%">
    <div class="flotchart-container" id="visitor-counter-cont"><div id="visitor-counter" class="flot-placeholder"></div></div>
</div>
<%/if%>


<% if ($cmd=='load_ref') %>
<h3>Referer by Host:</h3>
<div class="row">
    <div class="col-md-6">
            <table  class="table table-striped table-hover">
		<thead><tr>
			<th><a href="javascript:void(0);" onclick="simple_load('statcont','<%$PHPSELF%>?epage=<%$epage%>&cmd=<%$cmd%>&dsortby=user_domain&direct=<%$GET.direct%>');">Domain</a></th>
			<th><a href="javascript:void(0);" onclick="simple_load('statcont','<%$PHPSELF%>?epage=<%$epage%>&cmd=<%$cmd%>&dsortby=COLCOUNT&direct=<%$GET.direct%>');">Hits Domain</a></th>
			<th><a href="javascript:void(0);" onclick="simple_load('statcont','<%$PHPSELF%>?epage=<%$epage%>&cmd=<%$cmd%>&dsortby=lasthit&direct=<%$GET.direct%>');">Last Hit</a></th>
	  </tr></thead>
      <% foreach from=$STATOBJ.refbyhost item=row %>
    <tr>
    	<td><%$row.user_domain%></td>
  	<td class="text-center"><%$row.COLCOUNT%></td>
  	<td class="text-right"><%$row.lasthit%></td>
    </tr>
    <%/foreach%>
    </table>
    </div>
    <div class="col-md-6">
        <div class="flotchart-container" id="ref-chart-cont"><div id="ref-chart" class="flot-placeholder"></div></div>
    </div>
</div>    
    

<script>
    load_flot_pie('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_ref_chart', 'ref-chart', '100%', '400px', false);         
</script>


    
    <h3>Referer:</h3>
    <table  class="table table-striped table-hover">
		<thead><tr>
			<th><a href="<%$PHPSELF%>?cmd=<%$cmd%>&epage=<%$epage%>&sortby=referer&direct=<%$GET.direct%>">Referer</a></th>
			<th><a href="<%$PHPSELF%>?cmd=<%$cmd%>&epage=<%$epage%>&sortby=user_domain&direct=<%$GET.direct%>">Domain</a></th>
			<th><a href="<%$PHPSELF%>?cmd=<%$cmd%>&epage=<%$epage%>&sortby=user_count&direct=<%$GET.direct%>">Hits</a></th>
			<th><a href="<%$PHPSELF%>?cmd=<%$cmd%>&epage=<%$epage%>&sortby=lasthit&direct=<%$GET.direct%>">Last Hit</a></th>
	  </tr></thead>
      <% foreach from=$STATOBJ.refs item=row %>
    <tr>
      <td><a href="<%$row.referer%>" target="_blank"><%$row.referer%></a></td>
      <td><%$row.user_domain%></td>
  			<td class="text-center"><%$row.user_count%></td>
  		<td class="text-right"><%$row.lasthit%></td>
              </tr>
    <%/foreach%>
    </table>
<%/if%>

<% if ($cmd=='load_se') %>
<div class="row">
    <div class="col-md-6">
	<table class="table table-striped table-hover" id="seo-table" width="300">
        <thead><tr>
            <th>Suchmaschine</th>
            <th>Zugriffe</th>
            <th>letzter Zugriff</th></tr></thead>
            <% foreach from=$STATOBJ.spiders item=row %>
    <tr>
         <td><%$row.searchengine%></td>
         <td><%$row.anzahl%></td>
         <td>
         <% if ($row.datetoday==true) %><b><%$row.lasthit_today%></b><%else%><%$row.lasthit%><%/if%>
         </td>     
              </tr>
<%/foreach%>
<% foreach from=$STATOBJ.bots item=bot %>
    <tr>
         <td><%$bot%></td>
         <td>0</td>
         <td>noch nie</td>
    </tr>      
    <%/foreach%>
    </table>
    <%* Tabellen Sortierungs Script *%>
    <%assign var=tablesortid value="seo-table" scope="global"%>
    <%include file="table.sorting.script.tpl"%>   
    </div>
    <div class="col-md-6 text-center">
			<div id="spiderpie"></div>			
    </div>
   
    
<script type="text/javascript">
    load_flot_pie('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_se_chart', 'spiderpie', '100%', '400px', false, true);
</script>
    

<%/if%>

<% if ($cmd=='load_wio') %>
    <h3>{LBL_NOWONLINE} (Stand: <%$STATOBJ.now%>)</h3>
    <% if (count($STATOBJ.whoisonline)>0) %>
    <table class="table table-striped table-hover">
    <% foreach from=$STATOBJ.whoisonline item=row %>
    <tr>
       <td ><%$row.date%></td>
       <td ><%$row.time%></td>
       <td ><% if ($row.itsme==true) %><b><%$row.ip%></b><%else%><%$row.ip%><%/if%></td>
       <td ><a href="<%$row.akt_page%>" target="_blank"><%$row.akt_page%></a></td>
       </tr>
       <%/foreach%>
       </table>
    <%else%>{LBL_NOWONLINE} 0
    <%/if%>   
<%/if%>

<% if ($cmd=='load_browsers') %>
<h3>Browser / System Analyse</h3>
<div class="row">
        <div class="col-md-6">
           <% if (count($STATOBJ.browser.B)>0) %>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Browser</th>                        
                        <th class="text-right">#</th>
                    </tr>
                </thead>
                <tbody>
                 <% foreach from=$STATOBJ.browser.B item=row %>
                    <tr>                       
                        <td><%$row.b_browser%></td>
                        <td class="text-right"><%$row.b_count%></td>                       
                    </tr>
                <%/foreach%>   
                </tbody>
                </table>
                <%else%>
                  
                <%/if%>
           </div>
           <div class="col-md-6">              
               <div class="chart chart-md" id="stat-browser-B-pie"></div>
                <script>            
                    load_flot_pie('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_browser_chart&type=B', 'stat-browser-B-pie', '100%', '260px', true, true);
                </script>  
               </div>
    </div><!--row-->  
<hr>
<div class="row">
        <div class="col-md-6">
           <% if (count($STATOBJ.browser.S)>0) %>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Platform</th>                        
                        <th class="text-right">#</th>
                    </tr>
                </thead>
                <tbody>
                 <% foreach from=$STATOBJ.browser.S item=row %>
                    <tr>                       
                        <td><%$row.b_system%></td>
                        <td class="text-right"><%$row.b_count%></td>                       
                    </tr>
                <%/foreach%>   
                </tbody>
                </table>
                <%else%>
                  
                <%/if%>
           </div>
           <div class="col-md-6">              
            <div class="chart chart-md" id="stat-browser-S-pie"></div>
                <script>            
                    load_flot_pie('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_browser_chart&type=S', 'stat-browser-S-pie', '100%', '260px', true, true);
                </script>  
               
           </div>
    </div><!--row--> 
<hr>
<div class="row">
        <div class="col-md-6">
           <% if (count($STATOBJ.browser.BV)>0) %>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Browser Versionen</th>                        
                        <th class="text-right">#</th>
                    </tr>
                </thead>
                <tbody>
                 <% foreach from=$STATOBJ.browser.BV item=row %>
                    <tr>                       
                        <td><%$row.b_browserv%></td>
                        <td class="text-right"><%$row.b_count%></td>                       
                    </tr>
                <%/foreach%>   
                </tbody>
                </table>
                <%else%>
                  
                <%/if%>
           </div>
           <div class="col-md-6">              
            <div class="chart chart-md" id="stat-browser-BV-pie"></div>
                <script>            
                    load_flot_pie('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_browser_chart&type=BV', 'stat-browser-BV-pie', '100%', '260px', true, true);
                </script>  
              
           </div>
    </div><!--row-->  
<hr>
<div class="row">
        <div class="col-md-6">
           <% if (count($STATOBJ.browser.MS)>0) %>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Mobile Endgeräte Verteilung</th>                        
                        <th class="text-right">#</th>
                    </tr>
                </thead>
                <tbody>
                 <% foreach from=$STATOBJ.browser.MS item=row %>
                    <tr>                       
                        <td><%$row.b_mobilesystem%></td>
                        <td class="text-right"><%$row.b_count%></td>                       
                    </tr>
                <%/foreach%>   
                </tbody>
                </table>
                <%else%>
                  
                <%/if%>
           </div>
           <div class="col-md-6">              
            <div class="chart chart-md" id="stat-browser-MS-pie"></div>
                <script>            
                    load_flot_pie('<%$PHPSELF%>?epage=<%$epage%>&cmd=load_browser_chart&type=MS', 'stat-browser-MS-pie', '100%', '260px', true, true);
                </script>  
               
           </div>
    </div><!--row-->  
<%/if%>