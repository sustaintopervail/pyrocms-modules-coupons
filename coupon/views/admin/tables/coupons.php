	<table>
		<thead>
			<tr>
				<th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
				<th><?php echo lang('coupon:form_name_label'); ?></th>
				<th class="collapse"><?php echo lang('coupon:form_thumbnail_label'); ?></th>
				<th class="collapse"><?php echo lang('coupon:form_date_label'); ?></th>
				<th class="collapse"><?php echo lang('coupon:form_is_expired_label'); ?></th>
				<th width="320"></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($coupons as $coupon) : ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $coupon->id); ?></td>
					<td><?php echo ucwords($coupon->name); ?></td>
                    <?php
                          $thumbnail = Files::get_file($coupon->thumbnail);
                          $data = $thumbnail['data'];
                    ?>
					<td class="collapse">
                        <!--<pre><?php /*print_r($thumbnail) */?></pre>-->
                        <img src='<?php echo UPLOAD_PATH.'/files/'.$data->filename; ?>' /></td>
					<td class="collapse"><?php echo date("m/d/Y",strtotime($coupon->expiry_date)); ?></td>
					<td class="collapse"><?php echo strtotime($coupon->expiry_date) < time() ? 'Expired' : 'Valid'; ?></td>
					<td>
						<?php echo anchor('admin/coupon/edit/' . $coupon->id, lang('global:edit'), 'class="btn orange edit"'); ?>
						<?php echo anchor('admin/coupon/delete/' . $coupon->id, lang('global:delete'), array('class'=>'confirm btn red delete')); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="table_action_buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
	</div>