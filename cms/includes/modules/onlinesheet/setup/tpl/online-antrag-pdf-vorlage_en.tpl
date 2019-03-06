<html>
<head>
.box {
    width:800px;
    padding:10px;
    border:1px solid #000000;
}
</head>

<body>
<div class="box">
<div align="right">
         <% $gbl_config.adr_firma %><br>          
         <% $gbl_config.adr_street %> <% $gbl_config.adr_hausnr %><br> 
         <% $gbl_config.adr_plz %> <% $gbl_config.adr_town %><br>          
          TEL. <% $gbl_config.adr_telefon %><br>
          FAX <% $gbl_config.adr_fax %><br>
          <span class="small"><% $gbl_config.adr_telkosten %></span>
          <br>EMAIL: <% $gbl_config.adr_service_email %>
</div>

<h1 >Antrag: <%$OSHEET.AID%></h1>
<br>
{TPL_OSHEET_CONTENT}
</div>

</body>
</html>
