<img src="<%$PATH_CMS%>file_data/workshop/<%$WORKSHOP.ws.ws_theme%>" alt="" class="img-responsive mb-lg">

<% if ($WORKSHOP.ws.bookings_free>0) %>
  <a href="<%$PHPSELF%>?page=<%$page%>&cmd=book_now&id=<%$WORKSHOP.ws.id%>" title="buchen" class="btn btn-primary pull-right btn-booknow">buchen</a>
<%else%>
 <div class="alert alert-danger">ausgebucht</div>
<%/if%>
  <h2><%$WORKSHOP.ws.ws_title%></h2>
  <div class="row">
    <div class="col-md-6">
      <%$WORKSHOP.ws.ws_shortdesc%>
    </div>
    <div class="col-md-6">
      <div class="cricle pull-right">
        <h2><%$WORKSHOP.ws.ws_price_br%> <i class="fa fa-eur"></i></h2>
      </div>
      Datum: <%$WORKSHOP.ws.date_ger%> <%$WORKSHOP.ws.ws_time%> - <%$WORKSHOP.ws.ws_time_to%>
      
      <address>
        <b>Location:</b><br>
        <%$WORKSHOP.ws.ws_location%><br>
        <%$WORKSHOP.ws.ws_street%><br>
        <%$WORKSHOP.ws.ws_plz%> <%$WORKSHOP.ws.c_city%>
      </address>
      <p>
        <b>Zielgruppe:</b><br>
        <%$WORKSHOP.ws.ws_zielgruppe%>
      </p>
      <p>
        <b>Teilnehmer Anzahl:</b><br>
        <%$WORKSHOP.ws.ws_teilnvon%> - <%$WORKSHOP.ws.ws_teilnbis%> Personen
      </p>      
    </div>
  </div>
<hr>
  <div class="row">
    <div class="col-md-6">
        <p>
          <b>Zielsetzung:</b><br>
         <%$WORKSHOP.ws.ws_zielsetzung%>
        </p>
        <p>
          <b>Das müssen Sie mitbringen:</b><br>
         <%$WORKSHOP.ws.ws_mitbringen%>
        </p>
        <p>
          <b>Sonstiges:</b><br>
         <%$WORKSHOP.ws.ws_sonstiges%>
        </p>
    </div>
    <div class="col-md-6">

        <p>
          <b>Durchführung:</b><br>
         <%$WORKSHOP.ws.ws_durchfuehrung%>
        </p>
        <p>
          <b>Im Preis enthalten sind:</b><br>
         <%$WORKSHOP.ws.ws_enthalten%>
        </p>
        <p>
          <b>Bildrechte:</b><br>
         <%$WORKSHOP.ws.ws_bildrechte%>
        </p>      
    </div>
  </div>
  <hr>

<h3>Bilder</h3>
<div class="row">
    <% foreach from=$WORKSHOP.ws.thumbs name=gloop item=img %>
    <div class="col-md-4">
      <img alt="<%$img|sthsc%>" src="<%$img%>" class="img-responsive" />
    </div>
     <% if ($smarty.foreach.gloop.iteration % 3 == 0 )%></div><div class="row mt-lg"><%/if%>
    <%/foreach%>
</div>