(function($) {	
	$(document).ready(function() {
		$("input.uncheck").click(function() {
			$('input.uncheck').not(this).prop('checked', false);
		});
	});	
})(jQuery);
