<link href="<?php echo site_url()?>/addons/<?php echo SITE_REF ?>/themes/SilkySkinSA/css/print.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .img.placeholder {
        background:url('<?php echo site_url()?>/addons/<?php echo SITE_REF ?>/themes/SilkySkinSA/img/coupon_default.png') no-repeat center center;
    }

</style>
<!--http://dev.silkyskinsa.com/addons/silkyskinsa/themes/SilkySkinSA/css/style.css-->
<ul class="offer_list">
        <li class="curled offer js-offer min sale printable" id="coupon_<?php echo $coupon->id?>">
            <?php $thumbnail = Files::get_file($coupon->thumbnail);
            $data = $thumbnail['data'];
            ?>
            <div class="curled-page store-logo ">
                <div class="placeholder img" <?php if($data->filename): ?>style="background:url('<?php echo site_url().'/uploads/'.SITE_REF.'/files/'.$data->filename?>') no-repeat center center;"<?php endif; ?>>
                    <img src="<?php echo site_url().'/uploads/'.SITE_REF.'/files/'.$data->filename?>" />
                </div>
            </div>
            <div class="detail clearfix">
                <div class="description">
                    <div class="title">
                        <h3><?php echo $coupon->name?></h3>
                    </div>
                    <p class="discount min" style="height: 100px;line-height: 1.2em;">
                         <?php echo $coupon->description?>
                    </p>
                </div>
                <p class="expiry_date">
                    <span>Expiry: <?php echo date('d-M-Y',strtotime($coupon->expiry_date)) ;?></span>
                </p>
            </div>
        </li>
    </ul>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(function() {
        window.print();
        window.close();
    });
</script>
