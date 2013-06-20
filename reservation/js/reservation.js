/**
 * User: Faisal Kamal
 * Date: 3/13/13
 * Time: 12:37 PM
 */

jQuery(function($) {
    $('#date').datetimepicker({
        controlType: 'select',
        timeFormat: 'hh:mm tt',
        minDate:new Date(),
        stepMinute: 15
    });

    $("#reservation-form").validate();

});
