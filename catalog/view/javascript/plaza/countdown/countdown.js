$(document).ready(function () {
    $('.countdown-box').each(function () {
        var element = '.countdown-product-' + $(this).data('id');
        var date = $(this).data('date');
        countdown(date, element);
    })
});

function countdown(date, element) {
    var countDownDate = new Date(date).getTime();

    // Get fixed todays date and time
    var now = new Date().getTime();

    // Find the fixed distance between now an the count down date
    var distance = countDownDate - now;

    if(distance && distance > 0) {
        var x = setInterval(function() {
            // Get todays date and time
            var nowRealTime = new Date().getTime();

            // Find the distance between now an the count down date
            var distanceRealTime = countDownDate - nowRealTime;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distanceRealTime / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distanceRealTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distanceRealTime % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distanceRealTime % (1000 * 60)) / 1000);

            $(element + ' .day').html(days);
            $(element + ' .hour').html(hours);
            $(element + ' .min').html(minutes);
            $(element + ' .sec').html(seconds);

            if (distance < 0) {
                $(element).html();
            }
        }, 1000);
    } else {
        $(element).html('');
    }
}