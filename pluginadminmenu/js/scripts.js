jQuery(document).ready(function(){
		jQuery('.pam_options:not(:first)').slideUp();

		jQuery('.pam_section h3').click(function(){
			if(jQuery(this).parent().next('.pam_options').css('display')==='none')
				{	jQuery(this).removeClass('inactive').addClass('active').children('img').removeClass('inactive').addClass('active');

				}
			else
				{	jQuery(this).removeClass('active').addClass('inactive').children('img').removeClass('active').addClass('inactive');
				}

			jQuery(this).parent().next('.pam_options').slideToggle('slow');
		});
});
