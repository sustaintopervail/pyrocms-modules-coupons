<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Faisal Kamal
 * Date: 3/23/13
 * Time: 2:31 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<section class="title">
    <?php if ($this->method == 'create'): ?>
    <h4><?php echo 'New Coupon' ?></h4>
    <?php else: ?>
    <h4><?php echo sprintf('Edit %', $coupon->name); ?></h4>
    <?php endif; ?>
</section>
<section class="item">
<?php echo form_open_multipart(uri_string(), 'class="crud"'); ?>

        <div class="form_inputs" id="coupon-options-tab">
            <fieldset>

                <ul>
                    <li>
                        <label for="name"><?php echo lang('coupon:form_name_label'); ?></label>
                        <div class="input">
                            <?php echo form_input('name', htmlspecialchars_decode($coupon->name), 'maxlength="100" id="name"'); ?>
                        </div>
                    </li>
                    <li>
                        <label for="description"><?php echo lang('coupon:form_description_label'); ?></label>
                        <div class="input">
                            <?php echo form_textarea('description', $coupon->description, 'rows="5" columns="20" id="description" class="wysiwyg-simple"'); ?>
                        </div>
                    </li>
                    <li>
                        <label for="thumbnail"><?php echo lang('coupon:form_thumbnail_label'); ?></label>
                        <div class="input">
                            <?php echo form_upload('userfile', '', 'id="thumbnail"'); ?>
                        </div>
                    </li>
                    <li>
                        <label for="expiry_date"><?php echo lang('coupon:form_date_label'); ?></label>
                        <div class="input">
                            <?php echo form_input('expiry_date', (!is_null($coupon->expiry_date) && $coupon->expiry_date !='') ? date('m/d/Y',strtotime($coupon->expiry_date)) : '', 'id="expiry_date"'); ?>
                        </div>
                    </li>
                </ul>
            </fieldset>
        </div>
        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'save_exit', 'cancel'))); ?>
        </div>

</form>
</section>