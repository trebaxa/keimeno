<% if ($gbl_config.ga_ua_ident!="" && $gbl_config.ga_active==1) %>
    <script>
        var gaProperty = '<%$gbl_config.ga_ua_ident%>';
        var disableStr = 'ga-disable-' + gaProperty;
        window[disableStr] = true;
        if (document.cookie.indexOf(disableStr + '=false') > -1) {
          window[disableStr] = false;
        }
        function gaOptout() {
          document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
          window[disableStr] = true;
          console.log('Google Analytics wurde abgeschaltet.');
        }
        function gaOptin() {
          document.cookie = disableStr + '=false; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
          window[disableStr] = false;
          console.log('Google Analytics wurde aktiviert.');
        }
        
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<%$gbl_config.ga_ua_ident%>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      <% if ($gbl_config.ga_aw_ident!="") %>gtag('config', '<%$gbl_config.ga_aw_ident%>', { 'anonymize_ip': <%$GANALYTICS.config.anonymize_ip%>});<%/if%>
      gtag('config', '<%$gbl_config.ga_ua_ident%>', { 
            'anonymize_ip': <%$GANALYTICS.config.anonymize_ip%>,
            'forceSSL': <%$GANALYTICS.config.forcessl%>,
            'send_page_view': <%$GANALYTICS.config.send_page_view%>,
            'link_attribution': <%$GANALYTICS.config.link_attribution%> 
            });
    </script>
<%/if%>