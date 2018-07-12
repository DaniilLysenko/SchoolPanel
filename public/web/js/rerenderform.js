$('#addModal form').on('submit', (e) => {
	e.preventDefault();
	let name = $('#addModal form #student_name').val();
	let age = $('#addModal form #student_age').val();
	let sex = $('#addModal form #student_sex').val();
	let phone = $('#addModal form #student_phone').val();
	let _token = $('#addModal form #student__token').val();
	$.ajax({
		url: '/add',
		type: 'POST',
		data: {student: {name, age, sex, phone, _token}}
	})
	.done(response => {
		console.log(response);
	})
	.fail((jqXHR, textStatus, errorThrown) => {
        if (typeof jqXHR.responseJSON !== 'undefined') {
            if (jqXHR.responseJSON.hasOwnProperty('form')) {
            	console.log(jqXHR.responseJSON);
                $('#addModal form').html(jqXHR.responseJSON.form);
            }
     		console.log(jqXHR.responseJSON.message);
        } else {
            console.log(errorThrown);
        }
	})
});