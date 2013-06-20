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
    input,textarea{
        width: 325px;
    }
</style>
<?php echo form_open(uri_string(),'class="form-horizontal"'); ?>

        <fieldset>
            <legend><?php echo lang('reservation:page_edit_heading'); ?></legend>
            <div class="control-group">
                <label for="name" class="control-label"><?php echo lang('reservation:form_name_label'); ?></label>
                <div class="controls input">
                    <?php echo form_input('name', htmlspecialchars_decode($reservation->name), 'maxlength="100" id="name" class="input-xlarge"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label for="email" class="control-label"><?php echo lang('reservation:form_email_label'); ?></label>
                <div class="controls input">
                    <?php echo form_input('email', $reservation->email, 'maxlength="100" id="email" class="input-xlarge"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label for="phone" class="control-label"><?php echo lang('reservation:form_phone_label'); ?></label>
                <div class="controls input">
                    <?php echo form_input('phone', $reservation->phone, 'id="phone" class="input-xlarge"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label for="date" class="control-label"><?php echo lang('reservation:form_date_label'); ?></label>
                <div class="controls input">
                    <?php echo form_input('date', $reservation->event_date.' '. $reservation->event_time, 'id="event_date" class="input-append"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label for="location" class="control-label"><?php echo lang('reservation:form_location_label'); ?></label>
                <div class="controls input">
                    <?php echo form_input('location', $reservation->location, 'id="location" class="input-xlarge"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label for="no_of_hours" class="control-label"><?php echo lang('reservation:form_no_of_hours_label'); ?></label>
                <div class="controls input">
                    <?php echo form_input('no_of_hours', $reservation->no_of_hours, ' id="no_of_hours" class="input-small"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label for="additional_notes" class="control-label"><?php echo lang('reservation:form_additional_notes_label'); ?></label>
                <div class="controls input">
                    <?php echo form_textarea(array('id' => 'additional_notes', 'name' => 'additional_notes', 'value' => htmlspecialchars_decode($reservation->additional_notes), 'rows' => 10, 'class' => 'input-xlarge')); ?>
                </div>
            </div>
            <div class="control-group">
                <label for="amount" class="control-label"><?php echo lang('reservation:form_amount_label'); ?></label>
                <div class="controls input">
                    <?php echo form_input('amount', $reservation->amount, ' id="amount" class="input-small"'); ?>
                </div>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit" style="color: black;"><?php echo lang('reservation:form_generate_invoice_button_label'); ?></button>
                <button class="btn" style="color: black;"><?php echo lang('reservation:form_cancel_button_label'); ?></button>
            </div>
            <!--<div class="buttons">
                <?php /*$this->load->view('admin/partials/buttons', array('buttons' => array('save', 'save_exit', 'cancel'))); */?>
            </div>-->
        </fieldset>
    </form>