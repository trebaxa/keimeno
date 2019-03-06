<div id="mta-fb">
<ul >
<li><a <%if $WIZIQ.calendar.fbid==$fb.id%>class="mt-fbsel"<%/if%> href="<%$meta.uri%>" title="<% $fb.fb_title|hsc%>">Alle</a></li>
<%foreach from=$WIZIQ.fachbereiche item=fb %>
<li><a <%if $WIZIQ.calendar.fbid==$fb.id%>class="mt-fbsel"<%/if%> href="<% $meta.uri %>?fbid=<%$fb.id%>" title="<% $fb.fb_title|hsc%>"><% $fb.fb_title%></a></li>
<%/foreach%>
</ul>
</div>


<div class="mta-cal" >
<ul >
<li><span class="upper ulblock"><%$WIZIQ.calendar.month%></span></li>
<%foreach from=$WIZIQ.calendar.days item=day %>
<li><a <%if $WIZIQ.calendar.sd==$day.day%>class="mt-fbsel"<%/if%> href="<% $meta.uri %>?day=<%$day.day%>" title="<% $day.date_ger|hsc%>"><% $day.day%></a></li>
<%/foreach%>
</ul>
</div>

<div class="mta-cal" >
<ul >
<li><a <%if $WIZIQ.calendar.sa=='v'%>class="mt-fbsel"<%/if%> href="<% $meta.uri %>?da=v&day=<%$WIZIQ.calendar.sd%>" title="vormittags">vormittags</a></li>
<li><a <%if $WIZIQ.calendar.sa=='n'%>class="mt-fbsel"<%/if%> href="<% $meta.uri %>?da=n&day=<%$WIZIQ.calendar.sd%>" title="vormittags">nachmittags</a></li>
<li><a <%if $WIZIQ.calendar.sa=='a'%>class="mt-fbsel"<%/if%> href="<% $meta.uri %>?da=a&day=<%$WIZIQ.calendar.sd%>" title="vormittags">abends</a></li>
</ul>

</div>


<div class="tc-clear"></div>
<div id="bluearrow">
 <div class="top">
     <div class="arrow <% $WIZIQ.calendar.sa %>"></div>
 </div>
 <div class="middle">
 <h1>Frei sind</h1>
<table >
<%foreach from=$WIZIQ.alltimespans item=tts%>
<% if ($WIZIQ.calendar.fbid|in_array:$tts.fb || $GET.fbid==0) %> 
<% assign var=mtcounter value=$mtcounter+1 %>
<tr>
    <td><% $tts.nachname %></td>
    <td>
        <ul>
            <%foreach from=$tts.tts item=td%>
                <%foreach from=$td.zeiten item=zeit %>
                    <% if ($zeit.printable==true) %>
                    <li><a href="#" title="<% $zeit.duration_hours|hsc %>h"><% $zeit.start_ger %></a></li>
                    <%/if%>
                <%/foreach%>
            <%/foreach%>
        </ul>
   </td>
  <td width="200">
  <div class="mt-bookbox">
  <a class="mt-book-link" href="#book-box-<%$tts.TID%><% $WIZIQ.calendar.sdate|md5 %>" id="booking-link-<%$tts.TID%><% $WIZIQ.calendar.sdate|md5 %>">buchen</a>  
   </div>  
 
  </td>
</tr>
<%/if%>
<%/foreach%>
</table>
<% if ($mtcounter==0) %>
Es wurden keine Lehrer zu diesem Fachbereich gefunden.
<%/if%> 
 </div>
 <div class="bottom">
 </div>
</div>
<div class="tc-clear"></div>



<%foreach from=$WIZIQ.alltimespans item=tts%>
  <% if ($WIZIQ.calendar.fbid|in_array:$tts.fb || $GET.fbid==0) %> 
 <div style="display:none">
 <div id="book-box-<%$tts.TID%><% $WIZIQ.calendar.sdate|md5 %>">
 <form method="POST" action="<% $meta.uri %>">
 <input type="hidden" name="cmd" value="add_basket"> 
  <input type="hidden" name="WIZIQ[wq_tid]" value="<%$tts.TID%>"> 
 <table >
     <tr>
          <td>Wunschtermin:</td>
          <td><% $WIZIQ.calendar.sdate_ger %>
          <input type="hidden" name="WIZIQ[wq_date]" value="<% $WIZIQ.calendar.sdate %>">
          </td>          
     </tr>
     <tr>
          <td>Unterricht:</td>
          <td><select name="WIZIQ[wq_pid]">
           <%foreach from=$WIZIQ.productsbyfb item=product %>
            <% if ($product.wiziq_bookable==1) %>
              <option value="<%$product.pid%>"><%$product.pname%></option>
              <%/if%>
          <%/foreach%>
          </select>
          </td>          
     </tr> 
     <tr>
          <td>Wunschzeit:</td>
          <td><select name="WIZIQ[wq_time_start]">
           <%foreach from=$WIZIQ.alltimespans item=tts%>
                <% if ($WIZIQ.calendar.fbid|in_array:$tts.fb || $GET.fbid==0) %> 
              <%foreach from=$tts.tts item=td%>
                <%foreach from=$td.zeiten item=zeit %>
                <% if ($zeit.printable==true) %>
                  <option value="<%$zeit.start%>"><%$zeit.start_ger%></option>
                  <%/if%>
                  <%/foreach%>
              <%/foreach%>
              <%/if%>
          <%/foreach%>
          </select>
          </td>          
     </tr>          
 </table>
  <% html_subbtn class="sub_btn" value="buchen" %>
 </form>
</div>
</div>
<%/if%>
<%/foreach%>

<script type="text/javascript">
//<![CDATA[ 
$(document).ready(function() {

  <%foreach from=$WIZIQ.alltimespans item=tts%>
    <% if ($WIZIQ.calendar.fbid|in_array:$tts.fb || $GET.fbid==0) %> 
   $("a#booking-link-<%$tts.TID%><% $WIZIQ.calendar.sdate|md5 %>").fancybox({
        'hideOnContentClick': false
    });
    <%/if%>
   <%/foreach%>
});   
//]]>
</script>

<div class="tc-clear"></div>
<!--
 <% if ($WIZIQ.setshop==123) %>
<div class="tchide">
 <iframe width="0" height="0"  src="<%$SHOP_LINK%>index.php?page=login&aktion=login&email=<%$customer.email%>&pass=<%$customer.passwort%>" ></iframe>
</div>
<%/if%>
-->
