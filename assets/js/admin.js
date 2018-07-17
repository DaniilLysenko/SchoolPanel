$('#signIn').on('submit', (e) => {
	e.preventDefault();
	let login = $('#signIn #login').val();
	let password = $('#signIn #password').val();
	$.ajax({
		url: '/login',
		type: 'POST',
		data: {login, password},
		success: (response) => {
			if (response.error) {
				$('.alert-error').show();
				$('.alert-error').empty();
				$('.alert-error').text(response.error);
			} else {
				document.location.href = '/school';
			}
		},
		error: err => {
			throw new Error(err);
		}
	})
});