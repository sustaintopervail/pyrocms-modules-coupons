<div id="content">
    <img src="{{ url:site }}addons/reelphotobooth/themes/ReelPhotoBooth/img/bg-booth-1.jpg" alt="Page Background" class="pageBG">
    <div class="wrapper">
        <h1><?php echo lang('reservation:page_heading'); ?></h1>
        <section class="infoWrapper reservation">
            <?php $this->load->view('partials/notices') ?>
            <?php $this->load->view('partials/form', array('reservation' => $reservation)); ?>
        </section>
    </div>
</div>