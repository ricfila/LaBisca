<div id="modal" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content" id="modal-content"></div>
	</div>
</div>

<script>
	/*
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})
	*/
	$(document).ready(function(){
		$('[data-bs-toggle="tooltip"]').each(function(i) {
			let cont = $(this).attr('data-container');
			$(this).tooltip({
				container: cont,
				html: true,
				customClass: 'tooltip'
			});
		});
		aggiornalogo();
	});
</script>
