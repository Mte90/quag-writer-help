jQuery(document).ready(function($) {
	function ricerca() {
		var data = {
			action: 'quag_search',
			search: $('#quag_search').val()
		};
		$('#quag').html("Ricerca in corso...");
		$.post(ajaxurl, data, function(response) {
			$('#quag').html(response)
		}).fail(function(){
			alert("error");
		});
	}
	//Al click sul pulsante avvia la chiamata ajax
	$('#quag_ok').click(function() {
		ricerca();
});
	//Se premo invio e ho il focus sul campo di ricerca avvia la chiamata ajax
	$('#quag_search').keypress(function(e){
		if (e.which == 13 || e.keyCode == 13) {
			ricerca();
			e.preventDefault();
			e.stopPropagation(); 
			return false;
		}
		});
	//Se clicca il tag avvia la ricerca
	$('.tag_top').click(function() {
		$('#quag_search').val($(this).data('name'));
		ricerca();
		});
}); 
