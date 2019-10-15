   <div class="form-group"> 
    <label>Template</label> 
    <select class="form-control custom-select" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
  </div>
  <div class="row">
      <div class="form-group col-md-6">  
        <label>Empfänger Email</label>
        <input type="email" class="form-control" name="PLUGFORM[email]" value="<%$WEBSITE.node.tm_plugform.email%>" required>
      </div>
      <div class="form-group col-md-6 ">  
        <label>Empfänger Email Auswahl</label><br>
        <div class="form-inline" id="js-email-form">
            <input placeholder="Name, Label" type="text" autocomplete="OFF" class="form-control" name="EFORM[label]" value=""/>
            <div class="input-group">
                <input placeholder="E-Mail" autocomplete="OFF" type="email" class="form-control" name="EFORM[email]" value="" />
                <div class="input-group-btn">
                    <button type="button" onclick="save_contact_emaillist_<% $WEBSITE.node.id %>();" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                </div>
            </div>
        </div>
        <div data-simplebar="">
            <div id="js-elist"></div>
        </div>
      </div>
  </div>  
  <script>
    function save_contact_emaillist_<% $WEBSITE.node.id %>() {      
      var url = 'run.php?epage=contact.inc&cmid=<% $WEBSITE.node.id %>&cmd=add_email_to_list&'+$('#js-email-form :input').serialize();
      jsonexec(url, true);
      $('#js-email-form :input').val('');
    }
    function reload_elist_<% $WEBSITE.node.id %>() {
       simple_load('js-elist', 'run.php?epage=contact.inc&cmid=<% $WEBSITE.node.id %>&cmd=reload_elist'); 
    }
    reload_elist_<% $WEBSITE.node.id %>();
  </script>  
  <div class="row">
      <div class="form-group col-md-6">  
        <label>Titel</label>
        <input type="text" class="form-control" name="PLUGFORM[cf_title]" value="<%$WEBSITE.node.tm_plugform.cf_title%>" required>
      </div> 
      <div class="form-group col-md-6">  
        <label>Untertitel</label>
        <input type="text" class="form-control" name="PLUGFORM[cf_lead]" value="<%$WEBSITE.node.tm_plugform.cf_lead%>">
      </div>
  </div>
  <hr>
  <div class="form-group">  
    <label>Gesendet Nachricht Titel</label>
    <input type="text" class="form-control" name="PLUGFORM[cf_thanks_title]" value="<%$WEBSITE.node.tm_plugform.cf_thanks_title|hsc%>" required>
  </div>   
  <div class="form-group">  
    <label>Gesendet Nachricht Text</label>
    <input type="text" class="form-control" name="PLUGFORM[cf_thanks]" value="<%$WEBSITE.node.tm_plugform.cf_thanks|hsc%>" required>
  </div>   
  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[cf_save]" value="1" <% if ($WEBSITE.node.tm_plugform.cf_save==1) %>checked<%/if%> />
        Kontaktdaten nicht in Datenbank speichern
    </label>
  </div>
  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[cf_notschapura]" value="1" <% if ($WEBSITE.node.tm_plugform.cf_notschapura==1) %>checked<%/if%> />
        Abesender E-Mail ist nicht pflicht
    </label>
  </div>
  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[cf_send_we]" value="1" <% if ($WEBSITE.node.tm_plugform.cf_send_we==1) %>checked<%/if%> />
        Widerruf-Email an Besucher/Absender senden
    </label>
  </div>
  <div class="form-group">  
    <label>Widerruf Email Text</label>
    <textarea placeholder="" name="PLUGFORM[cf_we_text]" class="form-control"><%$WEBSITE.node.tm_plugform.cf_we_text|sthsc%></textarea>
    <label>z.B.:</label>
    <p class="well">    
    Hallo,<br><br>Sie haben in die Verarbeitung Ihrer im Kontaktformular angegebenen Daten zum Zwecke der Bearbeitung Ihrer Anfrage eingewilligt. 
    Diese Einwilligung können Sie jederzeit durch Klick auf den nachfolgenden Link <br><%help%><%literal%><%$gbl_config.we_link%> <%/literal%><%/help%>, unter dem entsprechenden Link auf der Kontaktseite unserer Homepage, durch 
                gesonderte E-Mail (<%$gbl_config.adr_service_email%>), Telefax (<%$gbl_config.adr_fax%>) oder Brief 
                an die <%$gbl_config.adr_firma%>, <%$gbl_config.adr_street%>, <%$gbl_config.adr_plz%> <%$gbl_config.adr_town%> widerrufen.
    </p>
  </div>  
  <div class="form-group">  
    <label>Verbotene Wörter</label>
    <textarea placeholder="http://,https://,я,д,з,bit.ly" name="PLUGFORM[cf_forbiddenwords]" class="form-control"><%$WEBSITE.node.tm_plugform.cf_forbiddenwords|hsc%></textarea>
  </div>  
  
  <div class="alert alert-info">
    <p><b>Beispiel Liste für SPAM Words:</b></p>
    <p>bit.ly,100%,#1,$$$,100% free,100% Satisfied,4U,50% off,Accept credit cards,Acceptance,Access,Accordingly,Act Now,Action,Ad,Additional income,Addresses on CD,
    Affordable,All natural,All new,Amazed,Amazing,Amazing stuff,Apply now,Apply Online,As seen on,Auto email removal,Avoid,Avoid bankruptcy,Bargain,Be amazed,
    Be your own boss,Being a member,Beneficiary,Best price,Beverage,Big bucks,Bill 1618,Billing,Billing address,Billion,Billion dollars,Bonus,Boss,Brand new pager,
    Bulk email,Buy,Buy direct,Buying judgments,Cable converter,Call,Call free,Call now,Calling creditors,Can’t live without,Cancel,Cancel at any time,
    Cannot be combined with any other offer,Cards accepted,Cash,Cash bonus,Cashcashcash,Casino,Celebrity,Cell phone cancer scam,Cents on the dollar,
    Certified,Chance,Cheap,Check,Check or money order,Claims,Claims not to be selling anything,Claims to be in accordance with some spam law,Claims to be legal,
    Clearance,Click,Click below,Click here,Click to remove,Collect,Collect child support,Compare,Compare rates,Compete for your business,Confidentially on all orders,
    Congratulations,Consolidate debt and credit,Consolidate your debt,Copy accurately,Copy DVDs,Costs,Credit,Credit bureaus,Credit card offers,Cures,Cures baldness,Deal,
    Dear [email/friend/somebody],Debt,Diagnostics,Dig up dirt on friends,Direct email,Direct marketing,Discount,Do it today,Don’t delete,Don’t hesitate,Dormant,
    Double your,Double your cash,Double your income,Drastically reduced,Earn,Earn $,Earn extra cash,Earn per week,Easy terms,Eliminate bad credit,Eliminate debt,
    Email harvest,Email marketing,Exclusive deal,Expect to earn,Expire,Explode your business,Extra,Extra cash,Extra income,F r e e,Fantastic,Fantastic deal,Fast cash,
    Fast Viagra delivery,Financial freedom,Financially independent,For free,For instant access,For just $ (some amount),For just $xxx,For Only,For you,Form,Free,
    Free access,Free cell phone,Free consultation,Free DVD,Free gift,Free grant money,Free hosting,Free info,Free installation,Free Instant,Free investment,Free leads,
    Free membership,Free money,Free offer,Free preview,Free priority mail,Free quote,Free sample,Free trial,Free website,Freedom,Friend,Full refund,Get,Get it now,
    Get out of debt,Get paid,Get started now,Gift certificate,Give it away,Giving away,Great,Great offer,Guarantee,Guaranteed,Have you been turned down?,Hello,Here,
    Hidden,Hidden assets,Hidden charges,Home,Home based,Home employment,Home based business,Human growth hormone,If only it were that easy,
    Important information regarding,In accordance with laws,Income,Income from home,Increase sales,Increase traffic,Increase your sales,Incredible deal,
    Info you requested,Information you requested,Instant,Insurance,Insurance,Internet market,Internet marketing,Investment,Investment decision,It’s effective,
    Join millions,Join millions of Americans,Junk,Laser printer,Leave,Legal,Life,Life Insurance,Lifetime,Limited,limited time,Limited time offer,Limited time only,
    Loan,Long distance phone offer,Lose,Lose weight,Lose weight spam,Lower interest rates,Lower monthly payment,Lower your mortgage rate,Lowest insurance rates,
    Lowest Price,Luxury,Luxury car,Mail in order form,Maintained,Make $,Make money,Marketing,Marketing solutions,Mass email,Medicine,Medium,Meet singles,Member,
    Member stuff,Message contains,Message contains disclaimer,Million,Million dollars,Miracle,MLM,Money,Money back,Money making,Month trial offer,More Internet Traffic,
    Mortgage,Mortgage rates,Multi-level marketing,Name brand,Never,New customers only,New domain extensions,Nigerian,No age restrictions,No catch,No claim forms,
    No cost,No credit check,No disappointment,No experience,No fees,No gimmick,No hidden,No hidden Costs,No interests,No inventory,No investment,No medical exams,
    No middleman,No obligation,No purchase necessary,No questions asked,No selling,No strings attached,No-obligation,Not intended,Not junk,Not spam,Now,Now only,
    Obligation,Offshore,Offer,Offer expires,Once in lifetime,One hundred percent free,One hundred percent guaranteed,One time,One time mailing,Online biz opportunity,
    Online degree,Online marketing,Online pharmacy,Only,Only $,Open,Opportunity,Opt in,Order,Order now,Order shipped by,Order status,Order today,Outstanding values,
    Passwords,Pennies a day,Per day,Per week,Performance,Phone,Please read,Potential earnings,Pre-approved,Presently,Price,Print form signature,Print out and fax,
    Priority mail,Prize,Problem,Produced and sent out,Profits,Promise,Promise you,Purchase,Pure Profits,Quote,Rates,Real thing,Refinance,Refinance home,Refund,Removal,
    Removal instructions,Remove,Removes wrinkles,Request,Requires initial investment,Reserves the right,Reverses,Reverses aging,Risk free,Rolex,Round the world,S 1618,
    Safeguard notice,Sale,Sample,Satisfaction,Satisfaction guaranteed,Save $,Save big money,Save up to,Score,Score with babes,Search engine listings,Search engines,
    Section 301,See for yourself,Sent in compliance,Serious,Serious cash,Serious only,Shopper,Shopping spree,Sign up free today,Social security number,Solution,Spam,
    Special promotion,Stainless steel,Stock alert,Stock disclaimer statement,Stock pick,Stop,Stop snoring,Strong buy,Stuff on sale,Subject to cash,Subject to credit,
    Subscribe,Success,Supplies,Supplies are limited,Take action,Take action now,Talks about hidden charges,Talks about prizes,Teen,Tells you it’s an ad,Terms,
    Terms and conditions,The best rates,The following form,They keep your money — no refund!,They’re just giving it away,This isn’t a scam,This isn’t junk,
    This isn’t spam,This won’t last,Thousands,Time limited,Traffic,Trial,Undisclosed recipient,University diplomas,Unlimited,Unsecured credit,Unsecured debt,
    Unsolicited,Unsubscribe,Urgent,US dollars,Vacation,Vacation offers,Valium,Viagra,Vicodin,Visit our website,Wants credit card,Warranty,We hate spam,We honor all,
    Web traffic,Weekend getaway,Weight,Weight loss,What are you waiting for?,What’s keeping you?,While supplies last,While you sleep,Who really wins?,Why pay more?,
    Wife,Will not believe your eyes,Win,Winner,Winning,Won,Work from home,Xanax,You are a winner!,You have been selected,Your income,???????????,\\.../.</p>
  </div>
  
  <div class="checkbox">
    <label>
        <input type="checkbox" name="PLUGFORM[cf_captcha]" value="1" <% if ($WEBSITE.node.tm_plugform.cf_captcha==1) %>checked<%/if%> />
        CAPTCHA aktivieren
    </label>
  </div>
  
  <% if ($gbl_config.smtp_use==0) %>
    <div class="alert alert-danger">Es wird empfohlen den Mailversand über SMTP einzurichten, um SPAM Erkennung zu vermeiden. Zusätzlich sollte Ihre 
    Domain den SPF Eintrag in den DNS Einstellungen haben.</div>
  <%/if%>
 