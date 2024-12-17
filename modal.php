<?php
// Вывод различных модальных окон
function get_modal() {
	return '
	<div class="modal fade" tabindex="-1" id="'.$_POST["id"].'">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">' . $_POST['title'] . '</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        <p>' . $_POST['msg'] . '</p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
	        <button type="button" class="btn btn-primary">' . $_POST['btn'] . '</button>
	      </div>
	    </div>
	  </div>
	</div>
	';
}

if (isset($_POST['title'])) {
	echo get_modal();
}
function get_loading() {
	return '
	<div class="modal loading" tabindex="-1" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false">
	  <div class="modal-dialog" style="max-width:0px!important">
	    <div class="modal-content loading-body">
	      <div class="modal-body">
	        <p><center><i class="fa-2x fas fa-sync fa-spin"></i></center></p>
	      </div>
	    </div>
	  </div>
	</div>
	';
}

if (isset($_POST['loading'])) {
	echo get_loading();
}
?>
