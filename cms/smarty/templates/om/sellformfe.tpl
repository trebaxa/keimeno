<style type="text/css">
 @import "<%$PATH_CMS%>includes/modules/sellform/css/sellform.css";
</style>
<script  type="text/javascript" src="<% $PATH_CMS %>includes/modules/sellform/css/sellform.js"></script>

<% if ($section=='') %>
<% if (count($sellforminlay.products)>0) %>
<div> 
 <div class="sf-leftcol"> 
 <p>Einfach und schnell bestellen! Klicken Sie einfach auf eines der Produkte.</p>
 <% foreach from=$sellforminlay.zahlweisen item=row name=zwloop %>
 <div style="height:50px;float:left;"><img src="<%$row.thumb%>" ></div>
 <% if ($smarty.foreach.zwloop.iteration % 2 == 0) %><div class="clearer"></div><%/if%>
 <%/foreach%>
 </div>
 <div style="float:left;"> 
 <% foreach from=$sellforminlay.products item=row %>
  <div style="float:left;width:140px;margin:0px 10px 0px 10px;">
    <a href="<%$PHPSELF%>?cmd=load_product&page=<%$page%>&section=payment&formid=<%$sellforminlay.FORMID%>&pid=<%$row.pid%>">
    <img src="<%$row.thumb%>" ><br>ausw&auml;hlen</a><br>
    <b><%$row.pname%></b><br>
    <% if ($row.p_subtitle!="") %><%$row.p_subtitle%><br><%/if%>
    <p class="small"><% $row.content.plaintext|truncate:30%></p>
    <div style="text-align:right;margin-top:6px;font-weight:bold"><%$row.vkbr_num%><br>
    <span class="small">(EUR)</span>
    </div>
 </div>
 <%/foreach%>
</div> 
</div> 
<%else%>
Keine Produkte gefunden.
<%/if%>

<%/if%>

<% if ($section=='payment') %>

<div >  
 <div class="sf-leftcol">
  <img src="<% $SELLFORM.PRODUCT.thumb_middle%>" ><br>
  <b><% $SELLFORM.PRODUCT.pname%></b>
  <p><% $SELLFORM.PRODUCT.content.plaintext%></p>
 </div>
 <div style="float:left;"> 
 
  <div>
   <% foreach from=$SELLFORM.SF.zahlweisen item=row name=zwloop %>
     <div style="height:50px;float:left;">
      <a href="javascript:void(0)"><img id="zwlogo-<%$row.paymid%>" class="zwtabclick" src="<%$row.thumb%>" ></a>
     </div>
   <%/foreach%>
   <div class="clearer"></div>
  </div>
  <div class="clearer"></div>
 <form action="<%$PHPSELF%>" method="POST">
  <div id="zw-detail">
    <% foreach from=$SELLFORM.SF.zahlweisen item=row name=zwloop %>
     <div <% if ($smarty.foreach.zwloop.first==false) %>style="display:none"<%/if%> id="zw-<%$row.id%>" class="zwcont">
      <% if ($smarty.foreach.zwloop.first==true) %>
       <input id="zw-zahlweise" type="hidden" value="<%$row.paymid%>" name="FORM[zahlweise]">
      <%/if%>
      <p><%$row.zwl_content%></p>
      <% if ($row.id==2 ||$row.id==9) %>
              <table ><tr>
                  <td >Inhaber:</td>
                  <td ><input type="text" name="FORM[kinhaber]" size="15" value="<%$PAY_OBJ.form.kinhaber%>"></td>
                  <td >Kreditkartennummer:</td>
                  <td><input type="text" name="FORM[knummer]" value="<%$PAY_OBJ.form.knummer%>" size="15"></td>
                 </tr>
                 <tr>
                    <td>gÃ¼ltig bis:</td>
                    <td><select name="gueltig_mon"><%$SELLFORM.PAY_OBJ.months_list%></select>&nbsp;
                    <select name="gueltig_jahr"><%$SELLFORM.PAY_OBJ.year_list%></select></td>
                    <td>Card Code</td>
                    <td><input type="text" name="FORM[kcardcode]" value="<%$SELLFORM.PAY_OBJ.form.kcardcode%>" size="5"></td>
                </tr>
              </table>
      <%/if%>
     </div>
    <%/foreach%>
  </div>
  <% if ($SELLFORM.PAY_OBJ.fault_form==true) %>
      <div class="faultbox">Bitte Ã¼berprÃ¼fen Sie Ihre Angaben.</div>
  <%/if%>
   <% include file="sellformkreg.tpl" %>
  <input type="submit" class="sub_btn" value="Ja, ich bestelle">
  <input type="hidden" name="page" value="<%$page%>">
  <input type="hidden" name="section" value="<%$section%>">
  <input type="hidden" name="formid" value="<% $SELLFORM.SF.FORMID %>">  
  <input type="hidden" name="pid" value="<%$REQUEST.pid%>">
  <input type="hidden" name="cmd" value="sforder">
  </form>
 </div> <!-- rechte spalte -->
 
</div> 

<script>
  <% if ($SELLFORM.PAY_OBJ.fault_form==true) %>
   show_zw('<% $SELLFORM.PAY_OBJ.form.zahlweise %>');
  <%/if%>
</script>

<%/if%>

<% if ($section=='orderfine') %>
 <div class="okbox">Vielen Dank fÃ¼r Ihre Bestellung.</div>
 <% if ($SELLFORM.ORDER.zahlweise==6) %>
    <% include file="sellformpaypal.tpl" %>
 <%/if%>
<%/if%>
