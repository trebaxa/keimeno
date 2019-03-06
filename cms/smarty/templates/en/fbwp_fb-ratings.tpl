<section>
  <div class="container">
    
  <div class="main-title"><h2>Kunden Meinungen</h2></div>

<%*$FBROBJ.fbratings|echoarr*%>

<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <% foreach from=$FBROBJ.fbratings.data item=row name=fbratingl%>    
      <li data-target="#carousel-example-generic" data-slide-to="$smarty.foreach.fbratingl.index" <%if ($smarty.foreach.fbratingl.first) %>class="active"<%/if%>></li>
    <%/foreach%>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox" itemscope itemtype="http://schema.org/Product"> 
       <img itemprop="image" class="sr-only" src="https://www.trebaxa.com/images/brief_logo.jpg" alt="Trebaxa Logo" />
       <meta itemprop="name" content="Kundenbewertungen von Trebaxa">
       <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
        <meta itemprop="ratingCount" content = "<%$FBROBJ.fbratings.total_rating_count%>">
        <meta itemprop="ratingValue" content = "<%$FBROBJ.fbratings.overall_star_rating%>">
        <meta itemprop="bestRating" content = "5">
        <meta itemprop="worstRating" content = "1">
       </div> 
    <% foreach from=$FBROBJ.fbratings.data item=row name=fbrating%>    
    <div class="item <%if ($smarty.foreach.fbrating.first) %>active<%/if%>">
      <div class="item-content" itemprop="review" itemscope itemtype="https://schema.org/Review">
        <meta itemprop="datePublished" content="<%$row.date_ger%>">
        <meta itemprop="name" content="Kundenbewertung von <%$row.reviewer.name%>">
         <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
             <meta itemprop="ratingValue" content = "<%$row.rating%>">
             <meta itemprop="worstRating" content = "1">
             <meta itemprop="bestRating" content = "5">
            </div>
       <blockquote>
         <q class="fbquote" itemprop="reviewBody"><%$row.review_text%></q>
       <footer class="fbrating">
        <cite>
          <span itemprop="author"><%$row.reviewer.name%></span>&nbsp;&nbsp;&nbsp;<%section name=val start=0 loop=$row.rating step=1%><i class="fa fa-star orange"></i><%/section%>
        </cite>
      </footer>
      </blockquote>
      </div>
    </div>
    <%/foreach%>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
    <%*<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>*%>
    <i class="fa fa-chevron-left" aria-hidden="true"></i>

    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
    <%*<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>*%>
    <i class="fa fa-chevron-right" aria-hidden="true"></i>

    <span class="sr-only">Next</span>
  </a>
</div>

</div>
</section>