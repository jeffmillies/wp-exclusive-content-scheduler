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
        $('#unit-day').html((days < 10 ? '0' + days : days));
        $('#unit-hour').html((hours < 10 ? '0' + hours : hours));
        $('#unit-minute').html((minutes < 10 ? '0' + minutes : minutes));
        $('#unit-second').html((seconds < 10 ? '0' + seconds : seconds));
    }

    $(document).ready(function () {
        var container = $('#exclusive-content-timer');
        var totalSeconds = container.attr('data-seconds');
        var type = container.attr('data-type');
        updateCountDown(totalSeconds);

        var timer = setInterval(function () {
            if (totalSeconds <= 0) {
                if (type == "running") {
                    $('#ec_content').html($('#ec_after_content').html());
                    clearInterval(timer);
                } else {
                    location.reload();
                }
            }
            updateCountDown(totalSeconds);
            totalSeconds--;
        }, 1000);
    });
} else {
    alert('jQuery required');
}