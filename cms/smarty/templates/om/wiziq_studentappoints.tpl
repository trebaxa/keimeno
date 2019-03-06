<h1>Die Lehrer</h1>

<div id="mta-fb" class="mb">
<ul >
<li><a <%if $GET.fbid==$fb.id%>class="mt-fbsel"<%/if%> href="<%$meta.uri%>" title="<% $fb.fb_title|hsc%>">Alle</a></li>
<%foreach from=$WIZIQ.fachbereiche item=fb %>
<li><a <%if $GET.fbid==$fb.id%>class="mt-fbsel"<%/if%> href="<%$meta.uri%>?fbid=<%$fb.id%>" title="<% $fb.fb_title|hsc%>"><% $fb.fb_title%></a></li>
<%/foreach%>
</ul>
</div>

<%foreach from=$WIZIQ.teacherindex item=teacher%>
<div class="bluebox">
<div style="width:150px;float:left;">
    <img src="<% $teacher.img %>" >
</div>
<div style="float:left;width:800px;">
    <h2><%$teacher.vorname%> <%$teacher.nachname%></h2>
<%$teacher.cust_profil|nl2br%>
<div class="clear mb"></div>
<h4>FÃ¤cher</h4>
<%foreach from=$teacher.fachbereiche item=fb name=fbloop %>
<a href="<% $HTA_CMSFIXLINKS.GF_URL %>?fbid=<% $fb.FBID %>"><% $fb.fb_title %></a><% if (!$smarty.foreach.fbloop.last) %>, <%/if%>
<%/foreach%>
<div class="clear mb"></div>

<% if count($WIZIQ.alltimespans)>0 %>
<h4>VerfÃ¼gbarkeit <%$smarty.now|date_format:"%b %Y"%></h4>
<%foreach from=$WIZIQ.alltimespans item=tts%>
<% if ($teacher.TID==$tts.TID) %>
<div class="cal-box"> 
        <ul>
            <%foreach from=$tts.tts item=td%>
                <li><% $td.date_day %></li>
            <%/foreach%>
        </ul>
        </div>
<%/if%>
<%/foreach%>
 <%/if%>
</div>
<div class="clear mb"></div>
</div>

<%/foreach%>
