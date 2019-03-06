<form role="form" name="searchform" action="<% $INDEXSEARCH.searchformurl %>" method="POST">
<input type="hidden" name="cmd" value="indexsearch">
<img style="padding-left:3px;float:right;margin-top:3px;" onClick="document.searchform.submit()" src="/images/opt_sr_btn.gif"  >
<% if ($POST.setvalue=="") %><% assign var=sv value="Suchbegriff" %><% else %><% assign var=sv value=$POST.setvalue %><%/if%>
<input autocomplete="off" id="fe-searcher" name="setvalue" value="<% $sv %>" <%if ($sv=="Suchbegriff") %> onFocus="javascript:this.value=''"<%/if%> class="form-control" type="text" class="searcher" size="16" >
</form>