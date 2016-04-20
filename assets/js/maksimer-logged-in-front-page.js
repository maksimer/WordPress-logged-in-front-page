jQuery(document).on('ready', function() {
	jQuery('.logged-in-front-page').find('li').appendTo('#front-static-pages ul');
	jQuery('label[for="maksimer_logged_in_front_page"]').closest('tr').remove();
});