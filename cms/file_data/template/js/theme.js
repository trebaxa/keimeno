function stringToDate(value) {
        var isEuro = value.match(/^\d{1,2}\.\d{1,2}\.\d{4}$/)
        var isIso = value.match(/^\d{4}-\d{1,2}-\d{1,2}$/)
        if (isEuro) {
                value = value.split('.')
                value = [value[2], value[1], value[0]].join('-')
                isIso = true
        }
        if (isEuro || isIso) {
                var date = new Date(value)
        }
        if (isNaN(date.getTime()) || !isIso) {
                return false
        }
        return date.getTime()
}

function euroFormatter(v, axis) {
        return v.toFixed(axis.tickDecimals) + "€";
}

function doPlot(position,oilprices,exchangerates) {
        $.plot("#chart-placeholder", [{
                data: oilprices,
                label: "Oil price ($)"
        },
        {
                data: exchangerates,
                label: "USD/EUR exchange rate",
                yaxis: 2
        }], {
                xaxes: [{
                        mode: "time"
                }],
                yaxes: [{
                        min: 0
                },
                {
                        // align if we are to the right
                        alignTicksWithAxis: position == "right" ? 1 : null,
                        position: position,
                        tickFormatter: euroFormatter
                }],
                legend: {
                        position: "sw"
                }
        });
}
$(document).ready(function() {
        $('#js-lightbox a').simpleLightbox();
        $('a.page-scroll').bind('click', function(event) { 
            var $anchor = $(this); 
            $('html, body').stop().animate({ scrollTop: $($anchor.data('hash')).offset().top }, 2000); 
            event.preventDefault(); 
        });
        $(window).scroll(function () { 
                if ($(this).scrollTop() > 300) { $('#scrollTop').fadeIn(); } else { $('#scrollTop').fadeOut(); } 
        });  
});

 