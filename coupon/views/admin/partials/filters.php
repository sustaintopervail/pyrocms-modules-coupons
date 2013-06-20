<fieldset id="filters">

    <legend><?php echo lang('global:filters'); ?></legend>

    <?php echo form_open(''); ?>
    <?php echo form_hidden('f_module', $module_details['slug']); ?>
    <ul>
        <li>
            <?php echo lang('coupon:form_is_expired_label', 'f_status'); ?>
            <?php echo form_dropdown('f_status', array(0 => lang('global:select-all'), '0'=>'Valid', '1'=>'Expired')); ?>
        </li>

        <li>
            <?php echo lang('blog:category_label', 'f_expiry_date'); ?>
            <?php echo form_input('f_expiry_date', '','id="expiry_date" placeholder="Enter Expiry Date"'); ?>
        </li>

        <li><?php echo form_input('f_name','','placeholder="Enter Coupon Name"'); ?></li>
        <li><?php echo anchor(current_url() . '#', lang('buttons.cancel'), 'class="cancel"'); ?></li>
    </ul>
    <?php echo form_close(); ?>
</fieldset>