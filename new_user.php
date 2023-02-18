<?php
if ($_SESSION['login_type'] != 1) {
	echo '<script> location.replace("./index.php?page=report_general_view"); </script>';
}

// Set the languages of the user: ////////////////////////////////////////////////
include 'langs.php';

$user_langs = (empty($languages))? array_keys($langs) : explode(',',$languages);

print_r($user_langs);
//////////////////////////////////////////////////////////////////////////////////
?>
<div class="col-lg-12" dir="rtl">
	<div class="card">
		<div class="card-body">
			<form action="" id="manage_user">
				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6 border-right">
						<div class="form-group">
							<label for="" class="control-label">الاسم الأول</label>
							<input type="text" name="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? $firstname : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">الاسم الأخير</label>
							<input type="text" name="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? $lastname : '' ?>">
						</div>
						<!-- <?php //if($_SESSION['login_type'] == 1): 
								?> -->
						<div class="form-group">
							<label for="" class="control-label">نوع المستخدم</label>
							<select name="type" id="type" class="custom-select custom-select-sm">
								<option value="6" <?php echo isset($type) && $type == 6 ? 'selected' : '' ?>>طرف ثالث</option>
								<option value="5" <?php echo isset($type) && $type == 5 ? 'selected' : '' ?>>مانح جزئي</option>
								<option value="4" <?php echo isset($type) && $type == 4 ? 'selected' : '' ?>>مانح</option>
								<option value="3" <?php echo isset($type) && $type == 3 ? 'selected' : '' ?>>مالك</option>
								<option value="2" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>مدير برنامج</option>
								<option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>مدير نظام</option>
							</select>
						</div>

						<div class="form-group" id="langs-group" style="display: none;">
							<label class="control-label">نسخ لغات مدّكر</label>
							<div class="chb-group form-control form-control-sm">
								<select class="form-control form-control-sm select2" multiple="multiple" name="languages[]">
									<option></option>
									<?php
									// Set the languages checkboxes:
									foreach ($langs as $key => $value) {
										if (in_array($key, $user_langs)) {
											echo "<option class='form-control form-control-sm' value='$key' selected>$value</option>";
										} else {
											echo "<option class='form-control form-control-sm' value='$key'>$value</option>";
										}
									}
									?>
								</select>
							</div>
						</div>
						<!-- <?php //else: 
								?>
							<input type="hidden" name="type" value="3">
						<?php //endif; 
						?> -->
						<div class="form-group">
							<label for="" class="control-label">صورة الملف الشخصي</label>
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))">
								<label class="custom-file-label" for="customFile">اختر ملفًّا</label>
							</div>
						</div>
						<div class="form-group d-flex justify-content-center align-items-center">
							<img src="<?php echo isset($avatar) ? 'assets/uploads/' . $avatar : '' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail ">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">البريد الإلكتروني</label>
							<input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
							<small id="#msg"></small>
						</div>

						<div class="form-group">
							<label class="control-label">كلمة المرور</label>
							<input type="password" class="form-control form-control-sm" name="password" <?php echo !isset($id) ? "required" : '' ?>>
							<small><i><?php echo isset($id) ? "دع الحقل فارغا إن لم ترغب في تعديل كلمة المرور" : '' ?></i></small>
						</div>
						<div class="form-group">
							<label class="label control-label">تأكيد كلمة المرور</label>
							<input type="password" class="form-control form-control-sm" name="cpass" <?php echo !isset($id) ? 'required' : '' ?>>
							<small id="pass_match" data-status=''></small>
						</div>
					</div>
				</div>
				<hr>
				<div class="col-lg-12 text-right justify-content-center d-flex">
					<button class="btn btn-primary mr-2">حفظ</button>
					<button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=user_list'">إلغاء</button>
				</div>
			</form>
		</div>
	</div>
</div>
<style>
	img#cimg {
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}

	.form-group {
		direction: rtl;
	}

	.control-label {
		float: right;
	}

	.chb-group {
		display: flex;
		flex-direction: row;
		border-style: none;
	}

	.chb-group input[type='checkbox'] {
		width: 20px;
		margin: 7px;
	}
</style>
<script>
	var typeInput = document.getElementById('type');
	if (typeInput.value == 5) {
		document.getElementById('langs-group').style.display = 'block';
	}

	typeInput.onchange = function() {
		if (typeInput.value == 5) {
			document.getElementById('langs-group').style.display = 'block';
		} else {
			document.getElementById('langs-group').style.display = 'none';
		}
	}

	$('[name="password"],[name="cpass"]').keyup(function() {
		var pass = $('[name="password"]').val()
		var cpass = $('[name="cpass"]').val()
		if (cpass == '' || pass == '') {
			$('#pass_match').attr('data-status', '')
		} else {
			if (cpass == pass) {
				$('#pass_match').attr('data-status', '1').html('<i class="text-success">كلمة المرور متوافقة.</i>')
			} else {
				$('#pass_match').attr('data-status', '2').html('<i class="text-danger">التأكيد لا يوافق كلمة المرور.</i>')
			}
		}
	})

	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
	$('#manage_user').submit(function(e) {
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		if ($('[name="password"]').val() != '' && $('[name="cpass"]').val() != '') {
			if ($('#pass_match').attr('data-status') != 1) {
				if ($("[name='password']").val() != '') {
					$('[name="password"],[name="cpass"]').addClass("border-danger")
					end_load()
					return false;
				}
			}
		}
		$.ajax({
			url: 'ajax.php?action=save_user',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp) {
				if (resp == 1) {
					alert_toast('نجح حفظ البيانات.', "success");
					setTimeout(function() {
						location.replace('index.php?page=user_list')
					}, 750)
				} else if (resp == 2) {
					$('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
					$('[name="email"]').addClass("border-danger")
					end_load()
				}
			}
		})
	})
</script>