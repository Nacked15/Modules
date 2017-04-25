$('.checkbox_item').click(function(){
	var hotel = $(this).val(),
		status = $(this).attr('data-stat'),
		ischecked = $('#checkbox_'+hotel).is(':checked');
	var nuevos = [], reactivar = [], desactivar = [];

		// comprabar que contiene ischeked
		console.log(ischecked);

		if (status === 0 && ischecked == true) {
			nuevos.push(hotel);
		} else if(status === 1 && ischecked == false) {
			desactivar.push(hotel);
		} else if (status === 2 && ischecked == true) {
			reactivar.push(hotel);
		}


});

$('#saveHotelList').click(function(){
	$.ajax({
        synch:'true',
        type: 'POST',
        url: _root_ + 'design/saveHotelList',
        data: {nuevos: nuevos, reactivar: reactivar, inactivos: desactivar},
        success: function(a){
            that.getListSessions();
        }
    });
});