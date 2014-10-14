<div class="wrap">
    <form method="post" action="options.php">
        <?php settings_fields('ec-basic-settings'); ?>
        <?php do_settings_sections('ec-basic-settings'); ?>
        <h2>Exclusive Content Settings</h2>

        <div style="width: 65%; float: left;">
            <label for="ec_settings_template"><b>Template</b></label><br>
            <textarea id="ec_settings_template" name="ec_settings_template"
                      style="width: 100%; height: 300px;"><? echo $options['ec_settings_template']; ?></textarea>
        </div>

        <div class="help" style="width: 20%; height: 300px; float: left; margin-left: 10px;"><b>Variables</b>

            <div style="margin-left: 15px">
                ##title##
                <br>##date##
                <br>##content##
                <br>
                <br>
            </div>

            <b>For the javascript to populate the counter, the following ids need to be on the page:</b>

            <div style="margin-left: 15px">
                unit-day
                <br>unit-hour
                <br>unit-minute
                <br>unit-second

            </div>
        </div>

        <div style="clear: both;"></div>

        <label for="ec_settings_css"><b>CSS</b></label><br>
        <textarea id="ec_settings_css" name="ec_settings_css"
                  style="width: 65%; height: 300px;"><? echo $options['ec_settings_css']; ?></textarea><br><br>

        <div class="help"></div>

        <?php submit_button(); ?>
    </form>
</div>