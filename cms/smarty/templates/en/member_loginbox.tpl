<% if ($customer.kid <= 0) %>
<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="login-modalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form role="form" method="POST" action="<% $HTA_CMSSSLLINKS_CMS.EC_URL %>">
        <input type="hidden" name="page" value="950">
        <input type="hidden" name="cmd" value="login">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="login-modalLabel">Login</h4>
      </div>
      <div class="modal-body">
            <div class="form-group">
             <label for="">{LBL_IHREEMAIL}:</label>
             <input class="form-control" type="email" name="email">
            </div> 
            <div class="form-group">
             <label for="">{LBL_PASSWORT}:</label>
             <input type="password" name="pass" placeholder="*****" class="form-control">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button class="btn btn-primary" type="submit">{LBL_LOGIN}</button>
      </div>
      </form>
    </div>
  </div>
</div>
<% /if %>