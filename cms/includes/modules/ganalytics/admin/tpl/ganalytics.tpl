<div class="page-header"><h1>Google Analytics</h1></div>


<%$GANALYTICS.CONFIG%>


<div class="well">
 Binde diesen HTML Code an beliebiger Stelle ein, um dem Besucher die Möglichkeit anzubieten, Google Analytics zu deaktivieren.<br><br>
 <p>
     <h5>Opt-Out</h5>
     <code>
        &lt;a onclick="alert('Das Tracking durch Google Analytics wurde in Ihrem Browser für diese Website deaktiviert');" title="Google Analytics deaktivieren" href="javascript:gaOptout()"&gt;Hier klicken, um Google Analytics zu deaktivieren&lt;/a&gt;
     </code>
 </p>
 <p>
     <h5>Opt-In</h5>
     <code>
         &lt;a onclick="alert('Das Tracking durch Google Analytics wurde in Ihrem Browser für diese Website aktiviert');" title="Google Analytics aktivieren" href="javascript:gaOptin()"&gt;Hier klicken, um Google Analytics zu aktivieren&lt;/a&gt;
     </code>
</p>
 
</div>