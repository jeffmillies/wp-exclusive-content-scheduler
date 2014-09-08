if (typeof jQuery === 'function') {
    var $ = jQuery;

    function updateCountDown(delta) {
        var days = Math.floor(delta / 86400);
        delta -= days * 86400;
        var hours = Math.floor(delta / 3600) % 24;
        delta -= hours * 3600;
        var minutes = Math.floor(delta / 60) % 60;
        delta -= minutes * 60;
        var seconds = delta % 60;
        $('#days .unit').html((days < 10 ? '0' + days : days));
        $('#hours .unit').html((hours < 10 ? '0' + hours : hours));
        $('#minutes .unit').html((minutes < 10 ? '0' + minutes : minutes));
        $('#seconds .unit').html((seconds < 10 ? '0' + seconds : seconds));
    }

    $(document).ready(function () {
        var timerContainer = $('#ec-timer');
        var totalSeconds = timerContainer.attr('data-seconds');
        updateCountDown(totalSeconds);

        var timer = setInterval(function () {
            if (totalSeconds <= 0) {
                //location.reload();
            }
            updateCountDown(totalSeconds);
            totalSeconds--;
        }, 1000);
    });
} else {
    alert('jQuery required');
}