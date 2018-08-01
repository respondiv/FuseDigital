jQuery(document).ready(function ($) {

	$.ajax({
		type : 'post',
		url: SystemStatusParameters.test_memory_path,
		success: function(response) {
			var get_memory_array = String(response).split('\n'),
				get_memory;
			$(get_memory_array).each(function(index, el) {
				var temp_memory = el.replace( /^\D+/g, '');
				if ('%'+temp_memory == el) get_memory = temp_memory;
			});
			var	memory_string;
			if (get_memory < 96) {
				memory_string = $('.real-memory .error');
			} else {
				memory_string = $('.real-memory .yes');
			}
			memory_string.text(memory_string.text().replace("%d%", get_memory));
			$('.real-memory .calculating').hide();
			memory_string.show();
		},
		error: function(response) {
			var get_memory_array = String(response.responseText).split('\n'),
				get_memory;
			$(get_memory_array).each(function(index, el) {
				var temp_memory = el.replace( /^\D+/g, '');
				if ('%'+temp_memory == el) get_memory = temp_memory;
			});
			var	memory_string;
			if (get_memory < 96) {
				memory_string = $('.real-memory .error');
			} else {
				memory_string = $('.real-memory .yes');
			}
			memory_string.text(memory_string.text().replace("%d%", get_memory));
			$('.real-memory .calculating').hide();
			memory_string.show();
		}
	});
});
