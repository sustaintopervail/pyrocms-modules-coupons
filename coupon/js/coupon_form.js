(function($) {
	$(function(){
		
		// generate a slug when the user types a title in
		//pyro.generate_slug('#property-content-tab input[name="title"]', 'input[name="slug"]');
		
		// needed so that Keywords can return empty JSON
		$.ajaxSetup({
			allowEmpty: true
		});

        $('#expiry_date').datepicker({"setDate": $('#expiry_date').val()});

		// editor switcher
		$('select[name^=type]').live('change', function() {
			chunk = $(this).closest('li.editor');
			textarea = $('textarea', chunk);
			
			// Destroy existing WYSIWYG instance
			if (textarea.hasClass('wysiwyg-simple') || textarea.hasClass('wysiwyg-advanced')) 
			{
				textarea.removeClass('wysiwyg-simple');
				textarea.removeClass('wysiwyg-advanced');
					
				var instance = CKEDITOR.instances[textarea.attr('id')];
			    instance && instance.destroy();
			}
			
			
			// Set up the new instance
			textarea.addClass(this.value);
			
			pyro.init_ckeditor();
			
		});


        textarea = $('textarea');

        // Destroy existing WYSIWYG instance
        if (textarea.hasClass('wysiwyg-simple') || textarea.hasClass('wysiwyg-advanced'))
        {
            textarea.removeClass('wysiwyg-simple');
            textarea.removeClass('wysiwyg-advanced');

            var instance = CKEDITOR.instances[textarea.attr('id')];
            instance && instance.destroy();
        }


        // Set up the new instance
        textarea.addClass(this.value);

        pyro.init_ckeditor();
		
	});
})(jQuery);