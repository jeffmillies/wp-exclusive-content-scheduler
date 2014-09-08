<link rel="stylesheet" href="<?php echo $baseUrl; ?>resources/jquery-ui-datepicker.css">
<script src="<?php echo $baseUrl; ?>resources/jquery-ui-datepicker.js"></script>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>resources/meta-box.css">
<script src="<?php echo $baseUrl; ?>resources/meta-box.js"></script>

<div id="schedule_container">
    <p>
        <input type="checkbox" id="ec_enable" name="ec_enable" value="off" checked hidden style="display: none;"/>
        <input type="checkbox" id="ec_enable"
               name="ec_enable" <?php echo($values['ec_enable'] == 'on' ? 'checked' : ''); ?> />
        <label for="ec_enable">Enable Scheduled Content?</label>
    </p>

    <div id="schedule_box" <?php echo($values['ec_enable'] != 'on' ? 'style="display:none;"' : ''); ?>>
        <p>
            <label for="ec_repeat">Repeats</label>
            <select name="ec_repeat" id="ec_repeat">
                <?php $opts = array('daily', 'weekly', 'monthly', 'yearly');
                foreach ($opts as $opt) {
                    ?>
                    <option
                        value="<?php echo $opt; ?>" <?php echo($opt == $values['ec_repeat'] ? 'selected' : ''); ?> ><?php echo $this->varform($opt); ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="ec_repeat_int">Repeat Every</label>
            <span id="repeat_name" class="floatright" style="margin: 5px;">
                <?php echo($unitNames[$values['ec_repeat']] ? $unitNames[$values['ec_repeat']] : 'days'); ?>
            </span>
            <select name="ec_repeat_int" id="ec_repeat_int">
                <?php
                $opts = array();
                for ($i = 1; $i < 32; $i++) {
                    $opts[] = $i;
                }
                foreach ($opts as $opt) {
                    ?>
                    <option
                        value="<?php echo $opt; ?>" <?php echo($opt == $values['ec_repeat_int'] ? 'selected' : ''); ?>><?php echo $opt; ?></option>
                <?php } ?>
            </select>
        </p>
        <div id="repeat_weekly"
             class="ec_repeat_on" <?php echo($values['ec_repeat'] != 'weekly' ? 'style="display:none;"' : ''); ?>>
            <p>
                <label for="ec_repeat_on">Repeat On</label>
            </p>

            <p>
                <?php
                $opts = array(
                    'ec_repeat_on_chk_su',
                    'ec_repeat_on_chk_mo',
                    'ec_repeat_on_chk_tu',
                    'ec_repeat_on_chk_we',
                    'ec_repeat_on_chk_th',
                    'ec_repeat_on_chk_fr',
                    'ec_repeat_on_chk_sa');
                foreach ($opts as $opt) {
                    $break = explode('_', $opt);
                    $name = $break[count($break) - 1];
                    ?>
                    <input type="checkbox" id="ec_repeat_on" name="<?php echo $opt; ?>"
                           value="on" <?php echo($values[$opt] == 'on' ? 'checked' : ''); ?>> <?php echo ucfirst($name[0]); ?>
                <?php } ?>
            </p>
        </div>
        <div id="repeat_monthly"
             class="ec_repeat_on"  <?php echo($values['ec_repeat'] != 'monthly' ? 'style="display:none;"' : ''); ?>>
            <p>
                <label for="ec_repeat_on">Repeat On</label>
                <select name="ec_repeat_on" id="ec_repeat_on" class="floatright">
                    <?php $opts = array('day_of_month', 'day_of_week');
                    foreach ($opts as $opt) {
                        ?>
                        <option
                            value="<?php echo $opt; ?>" <?php echo($opt == $values['ec_repeat_on'] ? 'selected' : ''); ?>><?php echo $this->varform($opt); ?></option>
                    <?php } ?>
                </select>
            </p>
        </div>
        <p>
            <label for="ec_start_on">Starts On</label>
            <input type="text" name="ec_start_on" id="ec_start_on" class="datepicker"
                   value="<?php echo($values['ec_start_on'] == '' ? date('m/d/Y') : $values['ec_start_on']); ?>">
        </p>

        <p> <?php
            $selected = ($values['ec_time_zone'] == '' ? 'America/Chicago' : $values['ec_time_zone']);
            ?>
            <label for="ec_time_zone">Time Zone</label>
            <select name="ec_time_zone" id="ec_time_zone">
                <?php foreach ($zones as $zone) { ?>
                    <option
                        value="<?php echo $zone; ?>" <?php echo($zone == $selected ? 'selected' : ''); ?>><?php echo $zone; ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="ec_start_time">At</label>
            <select name="ec_start_time_ampm" id="ec_start_time_ampm" class="floatright">
                <?php $opts = array('am', 'pm');
                foreach ($opts as $opt) {
                    ?>
                    <option
                        value="<?php echo $opt; ?>" <?php echo($opt == $values['ec_start_time_ampm'] ? 'selected' : ''); ?>><?php echo $opt; ?></option>
                <?php } ?>
            </select>
            <select name="ec_start_time_min" id="ec_start_time_min">
                <?php
                $opts = array();
                for ($i = 0; $i < 60; $i++) {
                    $opts[] = $i;
                }
                foreach ($opts as $opt) {
                    ?>
                    <option
                        value="<?php echo sprintf('%02d', $opt); ?>" <?php echo($opt == $values['ec_start_time_min'] ? 'selected' : ''); ?>>
                        : <?php echo sprintf('%02d', $opt); ?></option>
                <?php } ?>
            </select>
            <select name="ec_start_time_hr" id="ec_start_time_hr">
                <?php
                $opts = array();
                for ($i = 1; $i < 13; $i++) {
                    $opts[] = $i;
                }
                foreach ($opts as $opt) {
                    ?>
                    <option
                        value="<?php echo sprintf('%02d', $opt); ?>" <?php echo($opt == $values['ec_start_time_hr'] ? 'selected' : ''); ?>><?php echo sprintf('%02d', $opt); ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="ec_duration">Lasting for</label>
            <select name="ec_duration_type" id="ec_duration_type" class="floatright">
                <?php $opts = array('mins', 'hours');
                foreach ($opts as $opt) {
                    ?>
                    <option
                        value="<?php echo $opt; ?>" <?php echo($opt == $values['ec_duration_type'] ? 'selected' : ''); ?>><?php echo $opt; ?></option>
                <?php } ?>
            </select>
            <input type="text" name="ec_duration" id="ec_duration" value="<?php echo $values['ec_duration']; ?>"
                   style="width: 100px; height: 28px;">
        </p>
        <p>
            <label for="ec_end_on">Ends On</label>
            <input type="text" name="ec_end_on" id="ec_end_on" class="datepicker"
                   value="<?php echo($values['ec_end_on'] == '' ? date('m/d/Y', strtotime('+1 day')) : $values['ec_end_on']); ?>">
        </p>

        <div class="note">use a space to clear value</div>
        <p>
            <label for="ec_date_format">Date Format</label>
            <input type="text" name="ec_date_format" id="ec_date_format"
                   value="<?php echo($values['ec_date_format'] == '' ? 'D, M d \a\t g:ia' : $values['ec_date_format']); ?>">
        </p>

        <div class="note">php date manual <a target="_blank" href="http://php.net/manual/en/function.date.php">here</a>
        </div>
    </div>
</div>