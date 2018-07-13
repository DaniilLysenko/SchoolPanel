$('#addModal form').on('submit', (e) => {
	e.preventDefault();
	let name = $('#addModal form #student_name').val();
	let age = $('#addModal form #student_age').val();
	let sex = $('#addModal form #student_sex').val();
	let phone = $('#addModal form #student_phone').val();
	let _token = $('#addModal form #student__token').val();
	let data = JSON.stringify({student: {name, age, sex, phone, _token}});
	$.ajax({
		url: '/add',
		type: 'POST',
		dataType: 'json',
		contentType: 'application/json',
		data: data,
		success: (response) => {
			$('.students-table tbody').prepend(`
				<tr>
					<td>${response['student'].name}</td>
					<td>${response['student'].age}</td>
					<td>${response['student'].sex}</td>
					<td>
						<button class="btn btn-danger removeStudent" type="button" data-id="${response['student'].id}">Delete</button>
					</td>
					<td>
						<button class="btn btn-warning" data-toggle="modal" data-target="#editModal">Edit</button>
					</td>
					<td><button class="btn btn-primary openPage" data-id="${response['student'].id}">Page</button></td>
					<td><button class="btn btn-success openTeachers" data-id="${response['student'].id}">Teachers</button></td>
				</tr>
			`);
			$('#addModal').modal('hide');
			showSuccessAlert('Student added successfuly! :)');
		},
		error: err => {
			let error = err.responseJSON.error.info.children[0].errors[0].message;
			showDangerAlert(error);
		}
	})
});

$('.students-table').on('click', '.openPage', function() {
	let id = $(this).attr('data-id');
	$.ajax({
		url: '/single/'+id,
		type: 'GET',
		beforeSend: () => {
			$('#studentModal .modal-body').empty();
			$('#studentModal .modal-body').append('<div class="loader"></div>');
			$('#studentModal').modal('show');
		},
		success: (response) => {
			$('#studentModal .modal-title').text("");
			$('#studentModal .modal-body').empty();
			$('#studentModal .modal-title').text(response['student'].name);
			$('#studentModal .modal-body').append(`
				<div class="panel panel-default">
		          <ul class="list-group">
		            <li class="list-group-item active"><strong>Name: </strong>${response['student'].name}</li>
		            <li class="list-group-item"><img style="width: 100%;" src="${response['student'].avatar}"></li>
		            <li class="list-group-item"><strong>Sex: </strong>${response['student'].sex}</li>
		            <li class="list-group-item"><strong>Age: </strong>${response['student'].age}</li>
		            <li class="list-group-item"><strong>Phone number: </strong>${response['student'].phone}</li>
		          </ul>
		        </div>
			`);
		}
	});
});	

$('.students-table').on('click', '.removeStudent', function() {
	let id = $(this).attr('data-id');
	$.ajax({
		url: '/remove',
		type: 'POST',
		data: {id},
		complete: response => {
			if (!response.responseJSON.errors) {
				$('.st_col'+id).remove();
				showSuccessAlert('Student removed successfuly! :)');
			}
		}
	});
});

$('.table').on('click', '.editInfo', function() {
	$('#editModal .modal-title').text('Edit ' + $(this).attr('data-name') + ' info');
	$('#editModal form #edit_id').val($(this).attr('data-id'));
	$('#editModal').modal();
});

$('#editModal form').on('submit', function(e) {
	e.preventDefault();
	let formData = new FormData($(this)[0]);
	$.ajax({
		url: '/edit',
		data: formData,
		cache: false,
    	contentType: false,
    	processData: false,
    	type: 'POST',
    	complete: response => {
    		if (response.responseJSON.errors) {
    			showDangerAlert(response.responseJSON.errors);
    		} else {
    			showSuccessAlert("You\'ve updated image succefully");
    			$('#editModal').modal('hide');
    		}
    	}
	});
});

$('.students-table').on('click', '.openTeachers', function() {
	let id = $(this).attr('data-id');
	$.ajax({
		url: '/allTeachers/'+id,
		type: 'GET',
		success: (response) => {
			$('#teachersModal .modal-body .teachers tbody').empty();
			$('#teachersModal .modal-body .add_teacher').empty();
			response['teachers'].forEach(teacher => {
				$('#teachersModal .teachers tbody').append(`
				<tr class="teacher${teacher.id}">
                  <th scope="row">${teacher.name}</th>
                  <td>${teacher.course}</td>
                  <td>
                    <button class="btn btn-danger kill" type="button" data-t-id="${teacher.id}" data-s-id="${id}">Kill</button>
                  </td>
                </tr>
				`);
			});
			$('#teachersModal .modal-body .add_teacher').append(`
			  <h4>Choose teacher you want right now!</h4>
			  <form id="addTeacher">
			    <div class="form-group">
			      <label for="sel">Select teacher:</label>
			      <select class="form-control" id="sel" name="teacher" multiple></select>
			    </div>
			    <input type="hidden" value="${id}" id="stid">
			    <button class="btn btn-warning" type="submit" clas="choose">Choose</button>
			  </form>
			  <br><br>
			`);
			response['allTeachers'].forEach(teacher => {
				$('.add_teacher select').append(`
					<option value="${teacher.id}">${teacher.name}, ${teacher.course}</option>
				`);
			});
			$('#teachersModal').modal('show');
		}
	});	
});

$('#teachersModal').on('click', '.kill', function() {
	let tid = $(this).attr('data-t-id');
	let sid = $(this).attr('data-s-id');
	$.ajax({
		url: '/removeTeacher/'+sid+'/'+tid,
		type: 'POST',
		complete: response => {
			console.log(response);
			if (!response.responseJSON.errors) {
				$('.teacher'+tid).remove();
				showSuccessAlert(response.responseJSON.success);
			}
		}
	});
});

$('#teachersModal').on('submit', '#addTeacher', (e) => {
	e.preventDefault();
	let stid = $('#addTeacher #stid').val();
	let tid = $('#addTeacher #sel').val();
	$.ajax({
		url: '/addTeacher/'+stid,
		type: 'POST',
		data: {tid},
		success: (response) => {
			response['teacher'].forEach(teacher => {
				$('#teachersModal .teachers tbody').append(`
				<tr class="teacher${teacher.id}">
	              <th scope="row">${teacher.name}</th>
	              <td>${teacher.course}</td>
	              <td>
	                <button class="btn btn-danger kill" type="button" data-t-id="${teacher.id}" data-s-id="${stid}">Kill</button>
	              </td>
	            </tr>
				`);
			});
			showSuccessAlert('Teachers added successfuly! :)');			
		}
	});
});

$('#search_student #search_student_name').on('keyup', (e) => {
	let q = $('#search_student_name').val();
	if (q === "") {
		$('.students-table .searchBody').hide();
		$('.students-table .currentBody').show();
		$('.navigation').show();
		$('.show-more *').hide();	
	} 
	$.ajax({
		url: '/search',
		type: 'POST',
		data: {search_student: {'name': q, '_token': $('#search_student__token').val()}},
		success: (response) => {
			$('.students-table .searchBody').empty();
			response['students'].forEach(student => {
				$('.students-table .searchBody').append(`
				<tr class="st_col${student.id}">
					<td>${student.name}</td>
					<td>${student.age}</td>
					<td>${student.sex}</td>
					<td>
						<button class="btn btn-danger removeStudent" type="button" data-id="${student.id}">Delete</button>
					</td>
					<td>
						<button class="btn btn-warning editInfo" data-toggle="modal" data-id="${student.id}" data-name="${student.name}">Edit</button>
					</td>
					<td>
						<button class="btn btn-primary openPage" data-id="${student.id}">Page</button>
					</td>
					<td>
						<button class="btn btn-success openTeachers" data-id="${student.id}">Teachers</button>
					</td>
				</tr>
				`);	
			});
			$('.show-more input').val(q);
			if (response['students'].length > 0) {
				$('.students-table .currentBody').hide();
				$('.navigation').hide();
				$('.students-table .searchBody').show();
				$('.show-more button').show();
				$('.show-more button').attr('data-off',5);
			}
		},
		error: (error) => {
			console.log(error);
		}
	});
});

$('.container').on('click', '.show-more button', function() {
	let offset = $(this).attr('data-off');
	let q = $('.show-more input').val();
	$.ajax({
		url: '/search/'+q+'/'+offset,
		type: 'GET',
		beforeSend: function() {
			$('.show-more button').hide();
			$('.show-more .loader-st').show();
		},
		success: (response) => {
			$('.show-more input').val(q);
			if (response['students'].length > 0) {
				$('.show-more button').show();
				$('.show-more .loader-st').hide();
				response['students'].forEach(student => {
					$('.students-table .searchBody').append(`
					<tr class="st_col${student.id}">
						<td>${student.name}</td>
						<td>${student.age}</td>
						<td>${student.sex}</td>
						<td>
							<button class="btn btn-danger removeStudent" type="button" data-id="${student.id}">Delete</button>
						</td>
						<td>
							<button class="btn btn-warning editInfo" data-toggle="modal" data-id="${student.id}" data-name="${student.name}">Edit</button>
						</td>
						<td>
							<button class="btn btn-primary openPage" data-id="${student.id}">Page</button>
						</td>
						<td>
							<button class="btn btn-success openTeachers" data-id="${student.id}">Teachers</button>
						</td>
					</tr>
					`);	
				});
				let currentOff = parseInt($('.show-more button').attr('data-off')) + 5;
				$('.show-more button').attr('data-off', currentOff);
			} else {
				$('.show-more > div').hide();
			}
		},
	});
});	

function hideSuccessAlert()
{
	$('.alert-success').text("");
	$('.alert-success').hide();
}

function showSuccessAlert(text)
{
	$('.alert-success').text(text);
	$('.alert-success').show();
	setTimeout(hideSuccessAlert, 3000);
}

function hideDangerAlert()
{
	$('.alert-danger').text("");
	$('.alert-danger').hide();
}

function showDangerAlert(text)
{
	$('.alert-danger').text(text);
	$('.alert-danger').show();
	setTimeout(hideDangerAlert, 2000);
}