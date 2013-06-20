<fieldset id="filters">
	
	<legend><?php echo lang('global:filters'); ?></legend>
	
	<?php echo form_open('admin/reservation/ajax_filter'); ?>

	<?php echo form_hidden('f_module', $module_details['slug']); ?>
		<ul>  
			<?php /*?><li>
        		<?php echo lang('property_status_label', 'f_status'); ?>
        		<?php echo form_dropdown('f_status', array('' => lang('global:select-all'), '0'=>'Hidden', '1'=>'Visible')); ?>
    		</li>		<?php */?>			
			<li><?php echo form_input('f_building_name','','placeholder="Enter '.$partial_text.' name to search"'); ?></li>
            <li><?php echo form_input('f_zip_code','','placeholder="Enter zipcode to search"'); ?></li>
			<li><?php echo anchor(current_url() . '#', lang('buttons.cancel'), 'class="cancel"'); ?></li>
		</ul>
	<?php echo form_close(); ?>
</fieldset>