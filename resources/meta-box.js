if (typeof jQuery === 'function') {
    var $ = jQuery;
    var names = {
        'daily': 'days',
        'weekly': 'weeks',
        'monthly': 'months',
        'yearly': 'years'
    };
    $(document).ready(function () {
        $(document).on('change', '#ec_repeat', function () {
            var repeat = $(this).val();
            $('.ec_repeat_on').each(function () {
                $(this).hide();
            });
            $('#repeat_' + repeat).show();
            $('#repeat_name').html(names[repeat]);
        });
        $(document).on('click', '#ec_enable', function () {
            $('#schedule_box').toggle();
        });
        $(".datepicker").datepicker();
    });
} else {
    alert('jQuery required');
}