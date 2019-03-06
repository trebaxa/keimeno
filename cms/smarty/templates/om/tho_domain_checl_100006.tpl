<form id="domaincheckform" action="<%$PHPSELF%>" method="POST">
<input type="hidden" name="page" value="<%$page%>">
<input type="hidden" name="cmd" value="whois">

Domain:&nbsp;<input onclick="this.value='';" type="text" name="FORM[domain]" value="meine-domain" size="25" />
<strong>.</strong><select name="FORM[tld]">
<option selected="selected" value="*">alle</option>
<option value="at">at</option>
<option label="co" value="co">co</option>
<option label="co.uk" value="co.uk">co.uk</option>
<option value="biz">biz</option>
<option value="cc">cc</option>
<option value="com">com</option>
<option value="de">de</option>
<option value="eu">eu</option>
<option value="info">info</option>
<option value="me">me</option>
<option value="mobi">mobi</option>
<option value="net">net</option>
<option value="name">name</option>
<option value="org">org</option>
<option value="ws">ws</option>
<option label="cz" value="cz">cz</option>
<option label="pl" value="pl">pl</option>
<option label="lv" value="lv">lv</option>
</select>
<input type="submit" value="go">
</form>

<div id="output"></div>

<script>
    $(document).ready(function() {
        $('#domaincheckform').ajaxForm({
            target: '#output'
        });
    });
</script>    
