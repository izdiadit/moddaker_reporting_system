<!-- SweetAlert2 -->
<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="assets/plugins/toastr/toastr.min.js"></script>
<!-- Switch Toggle -->
<script src="assets/plugins/bootstrap4-toggle/js/bootstrap4-toggle.min.js"></script>
<!-- Select2 -->
<script src="assets/plugins/select2/js/select2.full.min.js"></script>

<!-- dropzonejs -->
<script src="assets/plugins/dropzone/min/dropzone.min.js"></script>
<script src="assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- DateTimePicker -->
<script src="assets/dist/js/jquery.datetimepicker.full.min.js"></script>
<!-- Bootstrap Switch -->
<script src="assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- MOMENT -->
<script src="assets/plugins/moment/moment.min.js"></script>
<script>
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: " اختر من هنا ",
			width: "100%"
		});
	})
	window.start_load = function(selector = 'body') {
		$(selector).prepend('<div id="preloader2"></div>')
	}
	window.end_load = function() {
		$('#preloader2').fadeOut('fast', function() {
			$(this).remove();
		})
	}
	window.viewer_modal = function($src = '') {
		start_load()
		var t = $src.split('.')
		t = t[1]
		if (t == 'mp4') {
			var view = $("<video src='" + $src + "' controls autoplay></video>")
		} else {
			var view = $("<img src='" + $src + "' />")
		}
		$('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
		$('#viewer_modal .modal-content').append(view)
		$('#viewer_modal').modal({
			show: true,
			backdrop: 'static',
			keyboard: false,
			focus: true
		})
		end_load()

	}
	window.uni_modal = function($title = '', $url = '', $size = "") {
		start_load()
		$.ajax({
			url: $url,
			error: err => {
				console.log()
				alert("An error occured")
			},
			success: function(resp) {
				if (resp) {
					$('#uni_modal .modal-title').html($title)
					$('#uni_modal .modal-body').html(resp)
					if ($size != '') {
						$('#uni_modal .modal-dialog').addClass($size)
					} else {
						$('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md")
					}
					$('#uni_modal').modal({
						show: true,
						backdrop: 'static',
						keyboard: false,
						focus: true
					})
					end_load()
				}
			}
		})
	}
	window._conf = function($msg = '', $func = '', $params = []) {
		$('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")")
		$('#confirm_modal .modal-body').html($msg)
		$('#confirm_modal').modal('show')
	}
	window.alert_toast = function($msg = 'TEST', $bg = 'success', $pos = '') {
		var Toast = Swal.mixin({
			toast: true,
			position: $pos || 'top-end',
			showConfirmButton: false,
			timer: 5000
		});
		Toast.fire({
			icon: $bg,
			title: $msg
		})
	}
	$(function() {
		bsCustomFileInput.init();

		$('.summernote').summernote({
			height: 300,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ol', 'ul', 'paragraph', 'height']],
				['table', ['table']],
				['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
			]
		})

		$('.datetimepicker').datetimepicker({
			format: 'Y/m/d H:i',
		})


	})
	$(".switch-toggle").bootstrapToggle();
	$('.number').on('input keyup keypress', function() {
		var val = $(this).val()
		val = val.replace(/[^0-9]/, '');
		val = val.replace(/,/g, '');
		val = val > 0 ? parseFloat(val).toLocaleString("en-US") : 0;
		$(this).val(val)
	})
</script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.js"></script>

<!-- PAGE assets/plugins -->
<!-- jQuery Mapael -->
<script src="assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="assets/plugins/raphael/raphael.min.js"></script>
<script src="assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
<!-- ChartJS -->
<script src="assets/plugins/chart.js/Chart.min.js"></script>

<!-- AdminLTE for demo purposes -->
<script src="assets/dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="assets/dist/js/pages/dashboard2.js"></script>
<!-- DataTables  & Plugins -->
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="assets/plugins/jszip/jszip.min.js"></script>
<script src="assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
	/////////// Full Screen:
	function openFullscreen(id) {
		var elem = document.getElementById(id);

		if (elem.requestFullscreen) {
			elem.requestFullscreen();
		} else if (elem.msRequestFullscreen) {
			/* For IE 11 */
			elem.msRequestFullscreen();
		} else if (elem.webkitRequestFullscreen) {
			/* For Safari */
			elem.webkitRequestFullscreen();
		}
		
	}

	function closeFullscreen() {

		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.msExitFullscreen) {
			/* For IE 11 */
			document.msExitFullscreen();
		} else if (document.webkitExitFullscreen) {
			/* For Safari */
			document.webkitExitFullscreen();
		}

		
	}

	function toggleFullscreen(id) {
		// To change the text and icon of the button:
		let button = document.getElementById(id).firstElementChild.getElementsByTagName('a')[0];

		if (document.fullscreenElement === null) {
			openFullscreen(id);
			button.innerHTML = '<i class="fas fa-compress-arrows-alt"></i>  تصغير';
		} else {
			closeFullscreen();
			button.innerHTML = '<i class="fas fa-expand-arrows-alt"></i>  ملء الشاشة';
		}
	}



	/////////// Slicing buttons Styles:
	$(document).ready(
		$('.data-slicing-btns li a').each(function() {
			if (this.href == document.URL) {
				this.style.backgroundColor = '#5d6c34';
				this.style.color = 'white';
			}
			if (document.URL.split('?')[1] == 'page=report_students') {
				let elem = $(".data-slicing-btns li:nth-child(1) a");
				elem[0].style.backgroundColor = '#5d6c34';
				elem[0].style.color = 'white';
			}
		})
	)
</script>