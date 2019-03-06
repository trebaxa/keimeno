<style type="text/css">
 @import "<%$PATH_CMS%>includes/modules/sellform/css/sellform.css";
</style>
<script  src="<% $PATH_CMS %>includes/modules/sellform/css/sellform.js"></script>

<% if ($section=='sepaval') %>
<h2>Verifizierung</h2>
    <% if ($SELLFORM.sepaok==true) %>
        <h3>SEPA - Verfahren wurde soeben von Ihnen genehmigt.</h3>
    <%else%>
        <h3>SEPA - Verfahren Fehler</h3>
        <div class="faultbox">Es trat ein Fehler auf.</div>
    <%/if%>
<%/if%>

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
<% if ($kregform_err.agb!='') %>                
    <span class="fa-red">Die AGBs wurde nicht anerkannt.</span>
<% /if %>

<% if ($kregform_err.wr!='') %>                
    <span class="fa-red">Das Widerrufsrecht wurde nicht anerkannt.</span>
<% /if %>
  <% if ($SELLFORM.PAY_OBJ.fault_form==true) %>
      <div class="faultbox">Bitte überprüfen Sie Ihre Angaben.</div>
  <%/if%>
 <div class="sf-leftcol">
  <img src="<% $SELLFORM.PRODUCT.thumb_middle%>" ><br>
  <b><% $SELLFORM.PRODUCT.pname%></b>
  <p><% $SELLFORM.PRODUCT.content.plaintext|truncate:300%></p>
 </div>
 <div style="float:left;width:66%"> 
 
  <div>
   <% foreach from=$SELLFORM.SF.zahlweisen item=row name=zwloop %>
     <div style="height:50px;float:left;">
      <a href="javascript:void(0)"><img id="zwlogo-<%$row.paymid%>" class="zwtabclick" src="<%$row.thumb%>" ></a>
     </div>
   <%/foreach%>
   <div class="clearer"></div>
  </div>
  <div class="clearer"></div>
 <form role="form" action="<%$PHPSELF%>" method="POST">
  <div id="zw-detail">
    <% foreach from=$SELLFORM.SF.zahlweisen item=row name=zwloop %>
     <div <% if ($smarty.foreach.zwloop.first==false) %>style="display:none"<%/if%> id="zw-<%$row.id%>" class="zwcont">
      <% if ($smarty.foreach.zwloop.first==true) %>
       <input id="zw-zahlweise" type="hidden" value="<%$row.paymid%>" name="FORM[zahlweise]">
      <%/if%>
      <p><%$row.zwl_content%></p>
       <% if ($row.id==3 ) %>
        <div class="form-group <% if ($kregform_err.iban!='') %>has-error<% /if %>"><label class="control-label" for="iban">IBAN</label> <input class="form-control" id="iban"  class="form-control" type="text" name="FORM[iban]" value="<%$customer.iban|sthsc%>"></div>
        <div class="form-group <% if ($kregform_err.bic!='') %>has-error<% /if %>"><label class="control-label" for="bic">BIC</label> <input class="form-control" id="bic"  class="form-control" type="text" name="FORM[bic]"  value="<%$customer.bic|sthsc%>" <% if ($kregform_err.bic!='') %>class="fa-red"<% /if %>></div>
        <div class="form-group <% if ($kregform_err.bank!='') %>has-error<% /if %>"><label class="control-label" for="bank">Bank</label> <input class="form-control" id="bank"  class="form-control" type="text" name="FORM[bank]"  value="<%$customer.bank|sthsc%>" <% if ($kregform_err.bank!='') %>class="fa-red"<% /if %>></div>
      <%/if%>
    
      <% if ($row.id==2 ||$row.id==9) %>
              <table ><tr>
                  <td >Inhaber:</td>
                  <td ><input class="form-control" type="text" name="FORM[kinhaber]" size="15" value="<%$PAY_OBJ.form.kinhaber%>"></td>
                  <td >Kreditkartennummer:</td>
                  <td><input class="form-control" type="text" name="FORM[knummer]" value="<%$PAY_OBJ.form.knummer%>" size="15"></td>
                 </tr>
                 <tr>
                    <td>gültig bis:</td>
                    <td><select class="form-control" name="gueltig_mon"><%$SELLFORM.PAY_OBJ.months_list%></select>&nbsp;
                    <select class="form-control" name="gueltig_jahr"><%$SELLFORM.PAY_OBJ.year_list%></select></td>
                    <td>Card Code</td>
                    <td><input class="form-control" type="text" name="FORM[kcardcode]" value="<%$SELLFORM.PAY_OBJ.form.kcardcode%>" size="5"></td>
                </tr>
              </table>
      <%/if%>
     </div>
    <%/foreach%>
  </div>
    <h3>AGB</h3>
  <input type="checkbox" required name="agb" value="1">&nbsp;Die <a href="{URL_TPL_10045}">AGB</a> habe ich zur Kenntis genommen und mit ihrer Geltung bin ich einverstanden</span><br>
  <input type="checkbox" required name="wr" value="1">&nbsp;Die <a href="{URL_TPL_10045}">Wiederrufsbelehrung</a> habe ich zur Kenntis genommen</span><br>


   <% include file="sellformkreg.tpl" %>
  <input type="submit" class="btn btn-primary" value="Ja, ich bestelle">
  <input type="hidden" name="page" value="<%$page%>">
  <input type="hidden" name="createabo" value="1">  
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
 <div class="okbox">Vielen Dank für Ihre Bestellung.</div>
 <% if ($SELLFORM.ORDER.zahlweise==6) %>
    <% include file="sellformpaypal.tpl" %>
 <%/if%>
<%/if%>

<% if ($section=='ordercancel') %>
 <div class="infobox">Bezahlung wurde abgebrochen.</div>
<%/if%>