<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Faisal Kamal
 * Date: 2/23/13
 * Time: 5:01 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<style type="text/css">
    .infoBlock td{
        padding: 3px;
    }
</style>


<?php echo form_open('reservation/create','class="form-horizontal" id="reservation-form"'); ?>

    <h2>Personal Information</h2>
    <div class="infoBlock">
        <table width="100%">
            <tr>
                <td style="width: 25%;">
                    <label for="name" class="control-label"><?php echo lang('reservation:form_name_label'); ?><small>*</small></label>
                </td>
                <td>
                    <?php echo form_input('name', htmlspecialchars_decode($reservation->name), 'maxlength="100" id="name" class="required"'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="email" class="control-label"><?php echo lang('reservation:form_email_label'); ?><small>*</small></label>
                </td>
                <td>
                    <?php echo form_input('email', $reservation->email, 'maxlength="100" id="email" class="required email"'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="phone" class="control-label"><?php echo lang('reservation:form_phone_label'); ?><small>*</small></label>
                </td>
                <td>
                    <?php echo form_input('phone', $reservation->phone, 'id="phone" class="required phoneUS"'); ?>
                </td>
            </tr>
        </table>
    </div>
    <br />
    <h2>Reservation Detail</h2>
    <div class="infoBlock">
        <table width="100%">
            <tr>
                <td style="width: 25%;">
                    <label for="date" class="control-label"><?php echo lang('reservation:form_date_label'); ?><small>*</small></label>
                </td>
                <td>
                    <?php echo form_input('date', $reservation->date, 'id="date" class="required"'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="location" class="control-label"><?php echo lang('reservation:form_location_label'); ?><small>*</small></label>
                </td>
                <td>
                    <?php echo form_input('location', $reservation->location, 'id="location" class="required"'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="no_of_hours" class="control-label"><?php echo lang('reservation:form_no_of_hours_label'); ?><small>*</small></label>
                </td>
                <td>
                    <?php echo form_dropdown('no_of_hours', array(
                                '' => 'Select No. of Hours',
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6',
                            ), $reservation->no_of_hours, ' id="no_of_hours" class="required number"'); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <label for="additional_notes" class="control-label"><?php echo lang('reservation:form_additional_notes_label'); ?></label>
                </td>
                <td>
                    <?php echo form_textarea(array('id' => 'additional_notes', 'name' => 'additional_notes', 'value' => htmlspecialchars_decode($reservation->additional_notes), 'rows' => 5, 'class' => 'input-xlarge')); ?>
                </td>
            </tr>
            <tr>
               <td colspan="2">
                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit"><?php echo lang('reservation:form_save_button_label'); ?></button>
                    </div>
               </td>
            </tr>
        </table>


    </div>

    </form>