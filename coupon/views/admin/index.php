<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Faisal Kamal
 * Date: 2/23/13
 * Time: 5:47 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<section class="title">
    <h4><?php echo lang('coupon:title'); ?></h4>
</section>

<section class="item">
    <?php template_partial('filters'); ?>
    <?php if ($coupons) : ?>

    <div id="filter-stage">

        <?php echo form_open('admin/coupon/action'); ?>

        <?php echo $this->load->view('admin/tables/coupons'); ?>

        <?php echo form_close(); ?>

    </div>

    <?php else : ?>
    <div class="no_data"><?php echo 'Currently there are no coupons in database.' ?></div>
    <?php endif; ?>

</section>