$('#add_student').on('submit', (e) => {
	e.preventDefault();
	let name = $('#add_student #name').val();
	let age = $('#add_student #age').val();
	let sex = $('#add_student #sex').val();
	let phone = $('#add_student #phone').val();
	let birthday = $('#add_student #birthday').val();
	$.ajax({
		url: '/ajax/student.php',
		type: 'POST',
		data: {name, age, sex, phone, birthday, type: 'addStudent'},
		success: (response) => {
			response = JSON.parse(response);
			if (response['student']) {
				$('.students-table tbody').prepend(`
					<tr>
						<td>${response['student'].name}</td>
						<td>${response['student'].age}</td>
						<td>${response['student'].sex}</td>
						<td>
							<button class="btn btn-danger removeStudent" type="button" data-id="${response['student'].id}">Delete</button>
						</td>
						<td>
							<button class="btn btn-warning" data-toggle="modal" data-target="#">Edit</button>
						</td>
						<td><button class="btn btn-primary openPage" data-id="${response['student'].id}">Page</button></td>
						<td><button class="btn btn-success openTeachers" data-id="${response['student'].id}">Teachers</button></td>
					</tr>
				`);
				$('#addModal').modal('hide');
				showSuccessAlert('Student added successfuly! :)');
			} else {
				showDangerAlert(response['error']);
			}
		}
	})
});

$('.table').on('click', '.openPage', function() {
	let id = $(this).attr('data-id');
	$.ajax({
		url: '/ajax/student.php',
		type: 'POST',
		data: {id, type: 'singleStudent'},
		beforeSend: () => {
			$('#studentModal .modal-body').empty();
			$('#studentModal .modal-body').append('<div class="loader"></div>');
			$('#studentModal').modal('show');
		},
		success: (response) => {
			response = JSON.parse(response);
			$('#studentModal .modal-title').text("");
			$('#studentModal .modal-body').empty();
			$('#studentModal .modal-title').text(response['student'].name);
			$('#studentModal .modal-body').append(`
				<div class="panel panel-default">
		          <ul class="list-group">
		            <li class="list-group-item active"><strong>Name: </strong>${response['student'].name}</li>
		            <li class="list-group-item"><img style="width: 100%;" src="${response['student'].image}"></li>
		            <li class="list-group-item"><strong>Sex: </strong>${response['student'].sex}</li>
		            <li class="list-group-item"><strong>Age: </strong>${response['student'].age}</li>
		            <li class="list-group-item"><strong>Phone number: </strong>${response['student'].birthday}</li>
		          </ul>
		        </div>
			`);
		}
	});
});	

$('.table').on('click', '.removeStudent', function() {
	let id = $(this).attr('data-id');
	$.ajax({
		url: '/ajax/student.php',
		type: 'POST',
		data: {id, type: 'removeStudent'},
		success: (response) => {
			$('.st_col'+id).remove();
			showSuccessAlert('Student removed successfuly! :)');
		}
	});
});

$('.table').on('click', '.openTeachers', function() {
	let id = $(this).attr('data-id');
	$.ajax({
		url: '/ajax/teacher.php',
		type: 'POST',
		data: {id, type: 'studentTeachers'},
		success: (response) => {
			response = JSON.parse(response);
			$('#teachersModal .modal-body').empty();
			$('#teachersModal .modal-body').append(`
	        <div class="teachers">
	          <table class="table">
	            <thead>
	              <tr>
	                <th scope="col">Name</th>
	                <th scope="col">Course</th>
	                <th scope="col">Kill</th>
	              </tr>
	            </thead>
	            <tbody>
	            </tbody>
	          </table>
	        </div>
	        <br><hr><br>
			`);
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
			$('#teachersModal .modal-body').append(`
				<div class="add_teacher">
				  <h4>Choose teacher you want right now!</h4>
				  <form id="addTeacher">
				    <div class="form-group">
				      <label for="sel">Select teacher:</label>
				      <select class="form-control" id="sel" name="teacher"></select>
				    </div>
				    <input type="hidden" value="${id}" id="stid">
				    <button class="btn btn-warning" type="submit" clas="choose">Choose</button>
				  </form>
				  <br><br>
				</div>
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
		url: '/ajax/teacher.php',
		type: 'POST',
		data: {tid, sid, type: 'removeTeacher'},
		success: (response) => {
			$('.teacher'+tid).remove();
			showSuccessAlert('Teacher removed successfuly! :)');
		}
	});
});

$('#teachersModal').on('submit', '#addTeacher', (e) => {
	e.preventDefault();
	let stid = $('#addTeacher #stid').val();
	let tid = $('#addTeacher #sel').val();
	$.ajax({
		url: '/ajax/teacher.php',
		type: 'POST',
		data: {stid, tid, type: 'addTeacher'},
		success: (response) => {
			response = JSON.parse(response);
			$('#teachersModal .teachers tbody').append(`
			<tr class="teacher${response['teacher'].id}">
              <th scope="row">${response['teacher'].name}</th>
              <td>${response['teacher'].course}</td>
              <td>
                <button class="btn btn-danger kill" type="button" data-t-id="${response['teacher'].id}" data-s-id="${stid}">Kill</button>
              </td>
            </tr>
			`);
			showSuccessAlert('Teacher added successfuly! :)');			
		}
	});
});

$('.table').on('click', '.editInfo', function() {
	$('#editModal .modal-title').text('Edit ' + $(this).attr('data-name') + ' info');
	$('#uploadImage #sid').val($(this).attr('data-id'));
	$('#editModal').modal();
});

$('#editModal').on('submit', '#uploadImage', (e) => {
	e.preventDefault();
	let formData = new FormData();
	formData.append('sid', $('#uploadImage #sid').val());
	formData.append('image', $('#uploadImage #image').prop('files')[0]);
	formData.append('type', 'uploadImage');
	$.ajax({
		url: '/ajax/student.php',
		data: formData,
		cache: false,
    	contentType: false,
    	processData: false,
    	type: 'POST',
    	success: (response) => {
    		response = JSON.parse(response);
    		if (response.result) {
    			showSuccessAlert("You\'ve updated image succefully");
    			$('#uploadImage #image').val("");
    		} else {
    			showDangerAlert("You have an error. Try adain!");
    		}
    	}
	});
});

$('#searchStudent #query').on('keyup', (e) => {
	let q = $('#searchStudent #query').val();
	if (q === "") $('.search-table').hide(); 
	$.ajax({
		url: '/ajax/student.php',
		type: 'POST',
		data: {q, type: 'searchStudent'},
		success: (response) => {
			response = JSON.parse(response);
			$('.search-table tbody').empty();
			response['result'].forEach(student => {
				$('.search-table tbody').append(`
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
			if (response['result'].length > 0) $('.search-table').show();
		},
		error: (error) => {
			console.log(error);
		}
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