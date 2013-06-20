	<table>
		<thead>
			<tr>
				<th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
				<th><?php echo lang('reservation:form_name_label'); ?></th>
				<th class="collapse"><?php echo lang('reservation:form_email_label'); ?></th>
				<th class="collapse"><?php echo lang('reservation:form_phone_label'); ?></th>
				<th class="collapse"><?php echo lang('reservation:form_date_label'); ?></th>
				<th class="collapse"><?php echo lang('reservation:form_amount_label'); ?></th>
				<th class="collapse"><?php echo lang('reservation:form_is_invoiced_label'); ?></th>
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
			<?php foreach ($reservations as $reservation) : ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $reservation->id); ?></td>
					<td><?php echo ucwords($reservation->name); ?></td>
					<td class="collapse"><?php echo $reservation->email; ?></td>
					<td class="collapse"><?php echo $reservation->phone; ?></td>
					<td class="collapse"><?php echo date("d-m-Y g:i:s a",strtotime($reservation->event_date.' '. $reservation->event_time)); ?></td>
					<td class="collapse"><?php echo $reservation->amount; ?></td>
					<td class="collapse"><?php echo $reservation->is_invoiced ? 'Invoiced' : 'not Invoiced'; ?></td>
					<td>
						<?php echo anchor('admin/reservation/edit/' . $reservation->id, lang('reservation:form_generate_invoice_button_label'), 'class="btn orange edit"'); ?>
						<?php echo anchor('admin/reservation/delete/' . $reservation->id, lang('global:delete'), array('class'=>'confirm btn red delete')); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="table_action_buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
	</div>