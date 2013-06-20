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
    <h4><?php echo lang('reservation:title'); ?></h4>
</section>

<section class="item">

    <?php if ($reservations) : ?>

    <div id="filter-stage">

        <?php echo form_open('admin/reservation/action'); ?>

        <?php echo $this->load->view('admin/tables/reservations'); ?>

        <?php echo form_close(); ?>

    </div>

    <?php else : ?>
    <div class="no_data"><?php echo 'Currently there are no reservations in database.' ?></div>
    <?php endif; ?>

</section>