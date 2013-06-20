<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Faisal Kamal
 * Date: 3/25/13
 * Time: 11:44 PM
 * To change this template use File | Settings | File Templates.
 */?>

<!-- Page layout: Default -->
<section id="pageTop" class="page-chunk ">
    <div class="page-chunk-pad">
        <h1>Silky Skin Coupons</h1>
        <p>Some copy related to the title</p>
    </div>
</section>
<section id="left-column" class="page-chunk page-content">
    <div class="page-chunk-pad">
        <h3>Discount Coupons</h3>
        <hr>
        <p>
            <img alt="vivant-home-banner.jpg" class="pyro-image alignment-none" src="http://dev.silkyskinsa.com/files/large/9" height="194" width="571">
        </p>
    </div>
    <ul class="offer_list">
        <?php foreach($coupons as $coupon): ?>
        <li class="curled offer js-offer min sale printable" id="coupon_<?php echo $coupon->id?>">
            <?php $thumbnail = Files::get_file($coupon->thumbnail);
            $data = $thumbnail['data'];
            ?>
            <div class="curled-page store-logo ">
                <div class="placeholder img" <?php if($data->filename): ?>style="background:url('<?php echo site_url().'/uploads/'.SITE_REF.'/files/'.$data->filename?>') no-repeat center center;"<?php endif; ?>></div>
            </div>
            <div class="detail clearfix">
                <div class="description">
                    <div class="title">
                        <h3><?php echo $coupon->name?></h3>
                        <p class="print">
                            <span><a class="print" href="javascript:void(0);" onclick="print(<?php echo $coupon->id?>,'<?php echo site_url().'/coupon/print_coupon/'.$coupon->id?>');" id="print_<?php echo $coupon->id?>"><?php echo lang('coupon:print_label');?></a></span>
                        </p>
                    </div>
                    <p class="discount min">
                         <?php echo $coupon->description?>
                    </p>
                </div>
                <p class="expiry_date">
                    <span>Expiry: <?php echo date('d-M-Y',strtotime($coupon->expiry_date)) ;?></span>
                </p>
                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style" addthis:url="<?php echo site_url().'coupon/view/'.$coupon->id?>">
                    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                    <a class="addthis_button_tweet"></a>
                    <a class="addthis_button_google_plusone"></a>
                    <a class="addthis_counter addthis_pill_style"></a>
                </div>
                <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=sustaintopervail"></script>
                <!-- AddThis Button END -->

            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
<section id="right-column" class="page-chunk page-content"><div class="page-chunk-pad"><h2>
    Recommended Products</h2>
    <hr>
    <p>
        These are products that we use and recommend to our clients.</p>
</div></section>


<div style="clear:both;"></div>

