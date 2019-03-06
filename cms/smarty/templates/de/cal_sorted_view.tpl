<h1>Aktuelle Termine</h1>
<% assign var=sclass value="row1" %>
<table class="tab_std" width="100%">
<% foreach from=$mdates_sorted item=mdate name=mt %>
<% if ($sclass=="row1") %> <% assign var=sclass value="row2" %> <% else %>   <% assign var=sclass value="row1" %>    <% /if %>
<% include file="cal_table.tpl" %>
        <% assign var=counter value=$smarty.foreach.mt.iteration %>
<% /foreach %>
</table> 
<% if $counter==0 %>{LBL_NOITEMS}<% /if %>