<style>
    #ec-timer ul li {
        display: inline-block;
        text-align: center;
        margin: 0 5px;
    }

    #ec-timer ul li .unit {
        font-weight: bold;
        font-size: 175%;
        margin: -15px;
    }

    ul {
        padding: 0;
        margin: 0;
    }

    .panel {
        background-color: #fff;
        border: 1px solid transparent;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .panel-default {
        border-color: #ddd;
    }

    .panel-body {
        padding: 15px;
    }

    .panel-default > .panel-heading {
        background-color: #f5f5f5;
        border-color: #ddd;
        color: #333;
    }

    .panel-heading {
        border-bottom: 1px solid transparent;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        padding: 10px 15px;
        overflow: hidden;
        position: relative;
    }

    .panel-footer {
        background-color: #f5f5f5;
        border-bottom-left-radius: 3px;
        border-bottom-right-radius: 3px;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        overflow: hidden;
        position: relative;
    }

    .panel-heading .title, .panel-footer .title {
        color: #fff;
        font-size: 600%;
        font-weight: bold;
        left: -5px;
        position: absolute;
        top: -60%;
        z-index: 1;
    }

    .date {
        font-size: 200%;
        z-index: 2;
        position: relative;
    }

    .text-center {
        text-align: center;
    }

</style>
<script>
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
</script>
<div id="exclusive-content-timer" class="panel panel-default">
    <div id="ec-starts" class="panel-heading text-center">
        <div class="title">Starting</div>
        <div class="date"><?php echo date($val['ec_date_format'], $check['starting']); ?></div>
    </div>
    <div id="ec-timer" class="panel-body text-center"
         data-seconds="<?php echo($check['until'] < 0 && $check['remaining'] > 0 ? $check['remaining'] : $check['until']); ?>">
        <ul>
            <li id="days">
                <div class="unit">0</div>
                <div class="label">Days</div>
            </li>
            <li id="hours">
                <div class="unit">0</div>
                <div class="label">Hours</div>
            </li>
            <li id="minutes">
                <div class="unit">0</div>
                <div class="label">Mins</div>
            </li>
            <li id="seconds">
                <div class="unit">0</div>
                <div class="label">Secs</div>
            </li>
        </ul>
    </div>
    <div id="ec-ends" class="panel-footer text-center">
        <div class="title">Ending</div>
        <div class="date"><?php echo date($val['ec_date_format'], $check['ending']); ?></div>
    </div>
</div>