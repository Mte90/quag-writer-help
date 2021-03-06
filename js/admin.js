jQuery(document).ready(function($) {
	function ricerca(action) {
		var data = {
			action: 'quag_search'/* + action*/,
			search: $('#quag_' + action).val()
		};
		$('#quag').html("Ricerca in corso...");
		$.post(ajaxurl, data, function(response) {
            if(response.charAt( response.length-1 ) == '0') {
                response = response.slice(0, -1);
            }
			$('#quag').html(response)
		}).fail(function(){
			alert("error");
		});
	}
    
	//Al click sul pulsante avvia la chiamata ajax
	$('#quag_ok').click(function() {
		ricerca('search');
    });
    $('#quag_ok_dashboard').click(function() {
		ricerca('search_dashboard');
    });
    
	//Se premo invio e ho il focus sul campo di ricerca avvia la chiamata ajax
	$('#quag_search').keypress(function(e){
		if (e.which == 13 || e.keyCode == 13) {
			ricerca('search');
			e.preventDefault();
			e.stopPropagation(); 
			return false;
		}
	});
    $('#quag_search_dashboard').keypress(function(e){
		if (e.which == 13 || e.keyCode == 13) {
			ricerca('search_dashboard');
			e.preventDefault();
			e.stopPropagation(); 
			return false;
		}
	});
    
	//Se clicca il tag avvia la ricerca
	$('.tag_top,.tag_dashboard_top').click(function() {
		$('#quag_search').val($(this).data('name'));
		ricerca('search');
    });
    $('.tag_dashboard_top').click(function() {
		$('#quag_search_dashboard').val($(this).data('name'));
		ricerca('search_dashboard');
    });
}); 
