<style> <?php echo $options['ec_settings_css']; ?> </style>

<?php
if ($check['until'] < 0 && $check['remaining'] > 0) {
    $type = 'running';
    $useTitle = ($options['ec_settings_title_running'] != '' ? $options['ec_settings_title_running'] : 'ending');
    $useDate = date($val['ec_date_format'], $check['ending']);
    $useSeconds = $check['remaining'];
} else {
    $type = 'waiting';
    $useTitle = ($options['ec_settings_title_waiting'] != '' ? $options['ec_settings_title_waiting'] : 'ending');
    $useDate = date($val['ec_date_format'], $check['starting']);
    $useSeconds = $check['until'];
}
?>
<div id="exclusive-content-timer" class="panel panel-default" data-seconds="<? echo $useSeconds; ?>"
     data-type="<? echo $type; ?>">
    <?php
    $template = str_replace(
        array(
            '##title##',
            '##date##'
        ),
        array(
            ucfirst($useTitle),
            $useDate
        ),
        $options['ec_settings_template']
    );
    echo $template;
    ?>
</div>
<div id="ec_after_content" style="display: none;">
    <?php echo $options['ec_settings_content_after']; ?>
</div>

<script src="<?php echo $baseUrl; ?>resources/timer.js"></script>