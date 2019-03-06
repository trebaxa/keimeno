<section>
  <h2>Vielen Dank für Ihren Auftrag. Bezahlen Sie jetzt bequem mit PayPal.</h2>
  <div class="text-center">
    <form id="paypal_form" action="<%$PAYPAL_POST.PAYPAL_URL%>" name="paypal_form" method="post">
    
    <button class="btn btn-default">Jetzt bezahlen</button>
    <!-- Paypal Configuration -->
    <input type="hidden" name="business" value="<% $PAYPAL_POST.business %>">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="image_url" value="<% $PAYPAL_POST.image_url %>">
    <input type="hidden" name="currency_code" value="<% $PAYPAL_POST.currency_code %>">
    <input type="hidden" name="mc_currency" value="<% $PAYPAL_POST.mc_currency %>">
    <input type="hidden" name="return" value="<% $PAYPAL_POST.return %>">
    <input type="hidden" name="cancel_return" value="<% $PAYPAL_POST.cancel_return %>">
    <input type="hidden" name="rm" value="2">
    <input type="hidden" name="residence_country" value="<% $customer.country_code_2 %>">
    <input type="hidden" name="cbt" value="<% $PAYPAL_POST.cbt %>">
    <!-- Payment Page Information -->
    <input type="hidden" name="no_note" value="1">
    <!-- Product Information -->
    <input type="hidden" name="item_name" value="{LBL_YOURORDER} <% $gbl_config.adr_firma %>">
    <input type="hidden" name="amount" value="<% $PAYPAL_POST.amount %>">
    <input type="hidden" name="shipping" value="0">
    <input type="hidden" name="quantity" value="1">
    <input type="hidden" name="item_number" value="<% $PAYPAL_POST.item_number %>">
    <input type="hidden" name="invoice" value="<% $PAYPAL_POST.invoice %>">
    <input type="hidden" name="custom" value="<% $PAYPAL_POST.custom %>">
    <input type="hidden" name="notify_url" value="<% $PAYPAL_POST.notify_url %>">
    <input type="hidden" name="undefined_quantity" value="">
    <!-- Customer Information -->
    <input type="hidden" name="first_name" value="<% $PAYPAL_POST.first_name %>">
    <input type="hidden" name="last_name" value="<% $PAYPAL_POST.last_name %>">
    <input type="hidden" name="adress1" value="<% $PAYPAL_POST.adress1 %>">
    <input type="hidden" name="zip" value="<% $PAYPAL_POST.zip %>">
    <input type="hidden" name="city" value="<% $PAYPAL_POST.city %>">
    <input type="hidden" name="email" value="<% $PAYPAL_POST.email %>">
    
    </form>
  </div>
</section>

<script>
//<![CDATA[
// document.paypal_form.submit();
//]]>
</script>
