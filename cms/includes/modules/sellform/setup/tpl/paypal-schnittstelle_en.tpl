<div>
<h1>{LBL_PAYPAL_HEADER}</h1>
{LBL_PAYPAL_INFO}
<form role="form" id="paypal_form" action="https://www.paypal.com/cgi-bin/webscr" name="paypal_form" method="post">
<input type="submit" class="btn btn-primary" name="submit" value="Jetzt mit PayPal bezahlen">
<!-- Paypal Configuration -->
<input type="hidden" name="business" value="<%$SELLFORM.PAYPAL.business%>">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="image_url" value="<%$SELLFORM.PAYPAL.image_url%>">
<input type="hidden" name="currency_code" value="<% $currency_lettercode %>">
<input type="hidden" name="mc_currency" value="<% $currency_lettercode %>">
<input type="hidden" name="return" value="<%$SELLFORM.PAYPAL.return%>">
<input type="hidden" name="cancel_return" value="<%$SELLFORM.PAYPAL.cancel_return%>">
<input type="hidden" name="rm" value="2">
<input type="hidden" name="residence_country" value="<% $SELLFORM.KOBJ.country_code_2 %>">
<input type="hidden" name="cbt" value="weiter">
<!-- Payment Page Information -->
<input type="hidden" name="no_note" value="1">
<!-- Product Information -->
<input type="hidden" name="item_name" value="Ihr Bestellung <%$SELLFORM.ORDER.oid%>">
<input type="hidden" name="amount" value="<%$SELLFORM.PAYPAL.amount%>">
<input type="hidden" name="shipping" value="0">
<input type="hidden" name="quantity" value="1">
<input type="hidden" name="item_number" value="<%$SELLFORM.ORDER.oid%>">
<input type="hidden" name="invoice" value="<%$SELLFORM.ORDER.oid%>">
<input type="hidden" name="custom" value="<%$SELLFORM.ORDER.oid%>">
<input type="hidden" name="notify_url" value="<%$SELLFORM.PAYPAL.notify_url%>">
<input type="hidden" name="undefined_quantity" value="">
<!-- Customer Information -->
<input type="hidden" name="first_name" value="<%$SELLFORM.KOBJ.vorname%>">
<input type="hidden" name="last_name" value="<%$SELLFORM.KOBJ.nachname%>">
<input type="hidden" name="adress1" value="<%$SELLFORM.KOBJ.strasse%> <%$SELLFORM.KOBJ.hausnr%>">
<input type="hidden" name="zip" value="<%$SELLFORM.KOBJ.plz%>">
<input type="hidden" name="city" value="<%$SELLFORM.KOBJ.ort%>">
<input type="hidden" name="email" value="<%$SELLFORM.KOBJ.email%>">
<br>
</form>
</div>
<script type="text/javascript">
//<![CDATA[
// document.paypal_form.submit();
//]]>
</script>
