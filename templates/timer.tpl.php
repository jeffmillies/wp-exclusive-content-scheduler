<link rel="stylesheet" href="<?php echo $baseUrl; ?>resources/timer.css">
<script src="<?php echo $baseUrl; ?>resources/timer.js"></script>
<div id="exclusive-content-timer" class="panel panel-default">
    <?php if ($check['until'] < 0 && $check['remaining'] > 0) { ?>
        <div id="ec-ends" class="panel-footer text-center">
            <div class="title">Ending</div>
            <div class="date"><?php echo date($val['ec_date_format'], $check['ending']); ?></div>
        </div>
    <?php } else { ?>
        <div id="ec-starts" class="panel-heading text-center">
            <div class="title">Starting</div>
            <div class="date"><?php echo date($val['ec_date_format'], $check['starting']); ?></div>
        </div>
    <?php } ?>
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
</div>