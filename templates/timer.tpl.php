<style>
    <?php
        echo $options['ec_settings_css'];
    ?>
</style>

<?php
if ($check['until'] < 0 && $check['remaining'] > 0) {
    $useTitle = 'Ending';
    $useDate = date($val['ec_date_format'], $check['ending']);
    $useSeconds = $check['remaining'];
} else {
    $useTitle = 'Starting';
    $useDate = date($val['ec_date_format'], $check['starting']);
    $useSeconds = $check['until'];
}
?>
<div id="exclusive-content-timer" class="panel panel-default" data-seconds="<? echo $useSeconds; ?>">
    <?php
    $template = str_replace(
        array(
            '##title##',
            '##date##'
        ),
        array(
            $useTitle,
            $useDate
        ),
        $options['ec_settings_template']
    );
    echo $template;
    ?>
</div>

<script src="<?php echo $baseUrl; ?>resources/timer.js"></script>