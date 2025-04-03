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
	let toggleclick = 0;
	
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

		$(".toggle-easter-egg").each(function (i, e) {
			e.addEventListener("click", function (e) {
				if (++toggleclick > 4) {
					setCookie("egg", "true", <?php echo isset($_COOKIE['egg']) ? -1 : 365; ?>);
					location.reload();
				}
			});
		});
	});
</script>
