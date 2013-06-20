/**
 * User: Faisal Kamal
 * Date: 3/13/13
 * Time: 12:37 PM
 */

jQuery(function($) {
    $('#expiry_date').datepicker();
    /*$(".print" )
        .attr( "href", "javascript:void( 0 )" )
        .click(
        function(e){
            // Print the DIV.
            $( "#"+ $(this).attr('id').replace('print','coupon') ).print();

            // Cancel click event.
            return( false );
        }
    )
    ;*/
});

function print(id,url){
    //$( "#coupon_"+ id ).print(url);
    window.open( url, '_blank', 'width="600"', false );
}
