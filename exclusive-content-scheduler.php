<?php
/*
Plugin Name: Exclusive Content Scheduler
Plugin URI: https://github.com/jeffmillies/wp-exclusive-content-scheduler
Description: Wordpress Plugin to hide content based on a schedule. A timer is placed in the body showing time until post viewed and also time remaining on post.
Author: Jeff Millies
Version: 1.0
Author URI: https://github.com/jeffmillies
License: GPL2
*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class ExclusiveContent
{
    /** Quick Reference array
     * @var array
     */
    private $unitNames = array(
        'daily' => 'days',
        'weekly' => 'weeks',
        'monthly' => 'months',
        'yearly' => 'years'
    );

    /** Valid field names to be used for this plugin
     * @var array
     */
    private $fields = array(
        'ec_enable',
        'ec_repeat',
        'ec_repeat_int',
        'ec_repeat_on',
        'ec_start_on',
        'ec_end_on',
        'ec_duration',
        'ec_duration_type',
        'ec_start_time',
        'ec_start_time_ampm',
        'ec_start_time_hr',
        'ec_start_time_min',
        'ec_repeat_on_chk_su',
        'ec_repeat_on_chk_mo',
        'ec_repeat_on_chk_tu',
        'ec_repeat_on_chk_we',
        'ec_repeat_on_chk_th',
        'ec_repeat_on_chk_fr',
        'ec_repeat_on_chk_sa',
        'ec_time_zone',
        'ec_date_format',
        'ec_meta_box_nonce'
    );

    private $basic = array(
        'ec_settings_css',
        'ec_settings_template',
        'ec_settings_content_waiting',
        'ec_settings_content_after',
        'ec_settings_title_running',
        'ec_settings_title_waiting'
    );

    /** Holder for assigned variables for templates
     * @var
     */
    private $assigned;

    /** Get everything ready
     *
     */
    function __construct()
    {
        $this->add_filters();
        $this->add_actions();
        $this->assign('unitNames', $this->unitNames);
        $this->assign('baseDir', plugin_dir_path(__FILE__));
        $this->assign('baseUrl', plugin_dir_url(__FILE__));
    }

    /** Initiate filter hooks that are going to be used
     *
     */
    private function add_filters()
    {
        add_filter('the_content', array($this, 'filter_content'));
    }

    /** Initial action hooks that are going to be used
     *
     */
    private function add_actions()
    {
        add_action('add_meta_boxes', array($this, 'meta_box'));
        add_action('save_post', array($this, 'save_post'));
        add_action('admin_menu', array($this, 'add_admin_menus'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings()
    {
        $options = array();
        foreach ($this->basic as $setting) {
            register_setting('ec-basic-settings', $setting);
            $value = esc_attr(get_option($setting));
            if (!$value || $value == '') {
                $value = $this->render($setting);
            }
            $options[$setting] = $value;
        }
        $this->options = $options;
        $this->assign('options', $options);
    }

    public function get_options_for_display()
    {
        foreach ($this->basic as $setting) {
            $value = get_option($setting);
            if (!$value || $value == '') {
                $value = $this->render($setting);
            }
            $options[$setting] = $value;
        }
        $this->options = $options;
        $this->assign('options', $options);
    }

    /** Making the values easier to use
     * @param $postId
     * @return array
     */
    private function get_values($postId)
    {
        $clean = array();
        $values = get_post_custom($postId);
        foreach ($values as $field => $value) {
            if (in_array($field, $this->fields))
                $clean[$field] = $value[0];
        }
        return $clean;
    }

    /** See if we need to add in a timer and block the content of a page or not
     * @param $content
     * @return string
     */
    public function filter_content($content)
    {
        global $post;
        $this->get_options_for_display();
        $values = $this->get_values($post->ID);
        $this->assign('baseUrl', plugin_dir_url(__FILE__));
        if ($values['ec_enable'] == 'on' && (trim($values['ec_end_on']) == '' || (trim($values['ec_end_on']) != '' && time() < strtotime($values['ec_end_on'])))) {
            $check = $this->checkSchedule($values);
            if (trim($values['ec_end_on']) != '' && strtotime($check['starting']) > strtotime($values['ec_end_on'])) {
                $start = strtotime($values['ec_end_on']);
                $check = array(
                    'current' => $check['current'],
                    'starting' => $start,
                    'ending' => $start + 1,
                    'until' => ($start - $check['current']),
                    'remaining' => ($start + 1 - $check['current'])
                );
            }
            $this->assign('check', $check);
            $this->assign('val', $values);
            $template = $this->render('timer');

            if ($check['until'] < 0 && $check['remaining'] > 0 || $check['show_post']) {
                if (strstr($template, '##content##')) {
                    $content = str_replace('##content##', $content, $template);
                } else {
                    $content = $template . $content;
                }
            } else {
                $content = str_replace('##content##', $this->options['ec_settings_content_waiting'], $template);
            }
        }
        return $content;
    }

    /** Setup call back to generate the meta box
     *
     */
    public function meta_box()
    {
        add_meta_box(
            'exclusive-content-meta-box',
            __('Schedule Content'),
            array($this, 'meta_box_template'),
            'page',
            'side',
            'low'
        );
    }

    public function add_admin_menus()
    {
        add_options_page('Exclusive Content Settings', 'Exclusive Content', 'manage_options', 'exclusive_content_options', array($this, 'plugin_options_page'));

    }

    /** Save the content from the meta box when posting the save form.
     * @param $post_id
     */
    public function save_post($post_id)
    {
        // Do nothing if auto saving
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        // Check to see if nonce is there for the form
        if (!isset($_POST['ec_meta_box_nonce']) || !wp_verify_nonce($_POST['ec_meta_box_nonce'], 'my_meta_box_nonce')) return;

        // Make sure permissions are correct
        if (!current_user_can('edit_post')) return;

        // Loop through our valid fields and save them
        foreach ($this->fields as $field) {
            update_post_meta($post_id, $field, esc_attr($_POST[$field]));
        }
    }

    /** Build the meta box displayed on the page editor
     * @param $post
     */
    public function meta_box_template($post)
    {
        global $post;
        $this->assign('baseUrl', plugin_dir_url(__FILE__));
        wp_nonce_field('my_meta_box_nonce', 'ec_meta_box_nonce');
        $zones = array();
        foreach (timezone_identifiers_list() as $zone) {
            $zones[] = $zone;
        }
        $values = $this->get_values($post->ID);
        $this->assign('values', $values);
        $this->assign('zones', $zones);
        $this->display('meta-box');
    }

    public function plugin_options_page()
    {
        $this->assign('baseUrl', plugin_dir_url(__FILE__));
        $this->display('options');
    }

    private function pre($info)
    {
        echo '<pre>';
        print_r($info);
        echo '</pre>';
    }

    /** Logic used to figure out the next date based on options set
     * @param $val
     * @return array
     */
    private function checkSchedule($val)
    {
        date_default_timezone_set(($val['sc_time_zone'] == '' ? 'America/Chicago' : $val['sc_time_zone']));
        $startTime = strtotime($val['ec_start_on']);
        $dateFound = false;
        $startCountWeeks = 0;
        $currentTime = strtotime(date('m/d/Y'));
        while ($dateFound === false) {
            for ($day = date('j', $currentTime); $day <= date('t', $currentTime); $day++) {
                $startingTime = strtotime(date("m/d/Y", $currentTime) . " {$val['ec_start_time_hr']}:{$val['ec_start_time_min']} {$val['ec_start_time_ampm']}");
                $duration = $val['ec_duration'] * 60;
                if ($val['ec_duration_type'] == 'hours')
                    $duration = $duration * 60;

                $endingTime = $startingTime + $duration;
                if ($endingTime < time() && date('j') == date('j', $currentTime))
                    $day++;

                $currentTime = strtotime(date('m/' . $day . '/Y', $currentTime));
                switch ($val['ec_repeat']) {
                    case 'daily':
                        $dayDiff = ((($currentTime - $startTime) / 60) / 60) / 24;
                        if (is_int($dayDiff / $val['ec_repeat_int'])) {
                            $dateFound = true;
                            break 3;
                        }
                        break;
                    case 'weekly':
                        $weekDiff = (int)floor((((($currentTime - $startTime) / 60) / 60) / 24) / 7);
                        if (is_int($weekDiff / (int)$val['ec_repeat_int'])) {
                            $activeDays = array();
                            $weekMap = array( // maps values from date('l') to values in sc_repeat_on_chk_*
                                'su' => 0, 'mo' => 1, 'tu' => 2, 'we' => 3, 'th' => 4, 'fr' => 5, 'sa' => 6
                            );
                            foreach ($val as $field => $value) {
                                if (strstr($field, 'ec_repeat_on_chk_') && $value == 'on') {
                                    $activeDays[] = $weekMap[str_replace('ec_repeat_on_chk_', '', $field)];
                                }
                            }
                            if (in_array(date('w', $currentTime), $activeDays)) {
                                $dateFound = true;
                                break 3;
                            }
                        }
                        break;
                    case 'monthly':
                        $d1 = new DateTime(date('Y-m-01', $startTime));
                        $d2 = new DateTime(date('Y-m-01', $currentTime));
                        $monthDiff = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);
                        if (date('m', $startTime) == date('m', $currentTime) && $currentTime > $startTime)
                            continue;

                        if (is_int($monthDiff / $val['ec_repeat_int'])) {
                            // check if it is the day of the month or the day of the week ie: 2nd tuesday of month
                            if ($val['ec_repeat_on'] == 'day_of_month') {
                                if (date('j', $startTime) == date('j', $currentTime)) {
                                    $dateFound = true;
                                    break 3;
                                }
                            }
                            if ($val['ec_repeat_on'] == 'day_of_week') {
                                if ($startCountWeeks == 0) {
                                    for ($i = 1; $i <= date('t', $startTime); $i++) {
                                        if (date('l', $startTime) == date('l', strtotime(date('Y-m', $startTime) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT)))) {
                                            $startCountWeeks++;
                                        }
                                        if (date('Y-m-d', $startTime) == date('Y-m', $startTime) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT)) {
                                            break;
                                        }
                                    }
                                }
                                $currentCountWeeks = 0;
                                for ($i = 1; $i <= date('t', $currentTime); $i++) {
                                    if (date('l', $currentTime) == date('l', strtotime(date('Y-m', $currentTime) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT)))) {
                                        $currentCountWeeks++;
                                    }
                                    if (date('Y-m-d', $currentTime) == date('Y-m', $currentTime) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT)) {
                                        break;
                                    }
                                }
                                if (date('l', $startTime) == date('l', $currentTime) && $startCountWeeks == $currentCountWeeks) {
                                    $dateFound = true;
                                    break 3;
                                }
                            }
                        }
                        break;
                    case 'yearly':
                        $yearDiff = date('Y', $currentTime) - date('Y', $startTime);
                        if (is_int($yearDiff / $val['ec_repeat_int'])) {
                            if (date('md', $startTime) == date('md', $currentTime)) {
                                $dateFound = true;
                                break 3;
                            }
                        }

                        break;
                }
            }
            $currentTime = strtotime(date('m/01/Y', $currentTime) . '+1 Month');
            $day = 1;

            if ($currentTime > strtotime('+1 year'))
                die('Unable to find date, left off at: ' . date('Y-m-d H:i:s', $currentTime));

        }

        $startingTime = strtotime(date("m/d/Y", $currentTime) . " {$val['ec_start_time_hr']}:{$val['ec_start_time_min']} {$val['ec_start_time_ampm']}");
        $duration = $val['ec_duration'] * 60;
        if ($val['ec_duration_type'] == 'hours')
            $duration = $duration * 60;

        $endingTime = $startingTime + $duration;
        $current = time();
        $result = array(
            'current' => $current,
            'starting' => $startingTime,
            'ending' => $endingTime,
            'until' => ($startingTime - $current),
            'remaining' => ($endingTime - $current)
        );
        return $result;
    }

    /** Capture the contents of a template in a variable to use when filtering page contents
     * @param $template
     * @return string
     */
    private function render($template)
    {
        foreach ($this->assigned as $__name => $__values) {
            $$__name = $__values;
        }
        ob_start();
        include_once(plugin_dir_path(__FILE__) . "templates/{$template}.tpl.php");
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assigned = array();
        return $contents;
    }

    /** Echo out a template from render
     * @param $template
     */
    private function display($template)
    {
        echo $this->render($template);
    }

    /** Assign variables to be used in the template
     * @param $name
     * @param $values
     */
    private function assign($name, $values)
    {
        $this->assigned[$name] = $values;
    }

    /** Formats a variable to a normal string
     * @param $variable
     * @return string
     */
    private function varform($variable)
    {
        return ucwords(str_replace(array('_', '-'), ' ', $variable));
    }
}

/**
 *  Initiate class to activate plugin
 */
$exc = new ExclusiveContent();