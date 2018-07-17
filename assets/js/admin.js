$('#signIn').on('submit', (e) => {
	e.preventDefault();
	let _username = $('#signIn #username').val();
	let _password = $('#signIn #password').val();
	let _token = $('#signIn #token').val();
	$.ajax({
		url: '/login',
		type: 'POST',
		data: {_username, _password, _token},
		success: (response) => {
			console.log(response);
			if (response.error) {
				$('.alert-error').show();
				$('.alert-error').empty();
				$('.alert-error').text(response.error);
			} else {
				// document.location.href = '/school';
			}
		}
	})
});