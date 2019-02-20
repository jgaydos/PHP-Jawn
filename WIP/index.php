<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>RICK - ETL</title>
	<link rel="stylesheet" href="css/jquery.ui.theme.css" />
	<link rel="stylesheet" href="css/bootstrap.css" />
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.ui.min.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/beautifyhtml.js"></script>
	<style>
	.droppable-active { background-color: #ffe !important; }
	.tools a { cursor: pointer; font-size: 80%; }
	.form-body .col-md-6, .form-body .col-md-12 { min-height: 400px; }
	.draggable { cursor: move; }
	</style>
	<script>
	$(document).ready(function() {
		setup_draggable();

		$("#n-columns").on("change", function() {
			var v = $(this).val();
			if(v==="1") {
				var $col = $('.form-body .col-md-12').toggle(true);
				$('.form-body .col-md-6 .draggable').each(function(i, el) {
					$(this).remove().appendTo($col);
				})
				$('.form-body .col-md-6').toggle(false);
			} else {
				var $col = $('.form-body .col-md-6').toggle(true);
				$(".form-body .col-md-12 .draggable").each(function(i, el) {
					$(this).remove().appendTo(i % 2 ? $col[1] : $col[0]);
				});
				$('.form-body .col-md-12').toggle(false);
			}
		});

		$("#copy-to-clipboard").on("click", function() {
			var $copy = $(".form-body").parent().clone().appendTo(document.body);
			$copy.find(".tools, :hidden").remove();
			$.each(["draggable", "droppable", "sortable", "dropped",
				"ui-sortable", "ui-draggable", "ui-droppable", "form-body"], function(i, c) {
				$copy.find("." + c).removeClass(c);
			})
			var html = html_beautify($copy.html());
			$copy.remove();

			$modal = get_modal(html).modal("show");
			$modal.find(".btn").remove();
			$modal.find(".modal-title").html("Copy HTML");
			$modal.find(":input:first").select().focus();

			return false;
		})


	});

	var setup_draggable = function() {
		$( ".draggable" ).draggable({
			appendTo: "body",
			helper: "clone"
		});
		$( ".droppable" ).droppable({
			accept: ".draggable",
			helper: "clone",
			hoverClass: "droppable-active",
			drop: function( event, ui ) {
				$(".empty-form").remove();
				var $orig = $(ui.draggable)
				if(!$(ui.draggable).hasClass("dropped")) {
					var $el = $orig
						.clone()
						.addClass("dropped")
						.css({"position": "static", "left": null, "right": null})
						.appendTo(this);

					// update id
					var id = $orig.find(":input").attr("id");

					if(id) {
						id = id.split("-").slice(0,-1).join("-") + "-"
							+ (parseInt(id.split("-").slice(-1)[0]) + 1)

						$orig.find(":input").attr("id", id);
						$orig.find("label").attr("for", id);
					}

					// tools
					$('<p class="tools">\
						<a class="edit-link">Edit HTML<a> | \
						<a class="remove-link text-danger">Remove</a></p>').appendTo($el);
				} else {
					if($(this)[0]!=$orig.parent()[0]) {
						var $el = $orig
							.clone()
							.css({"position": "static", "left": null, "right": null})
							.appendTo(this);
						$orig.remove();
					}
				}
			}
		}).sortable();

	}

	var get_modal = function(content) {
		var modal = $('<div class="modal" style="overflow: auto;" tabindex="-1">\
			<div class="modal-dialog">\
				<div class="modal-content">\
					<div class="modal-header">\
						<a type="button" class="close"\
							data-dismiss="modal" aria-hidden="true">&times;</a>\
						<h4 class="modal-title">Edit HTML</h4>\
					</div>\
					<div class="modal-body ui-front">\
						<textarea class="form-control" \
							style="min-height: 200px; margin-bottom: 10px;\
							font-family: Monaco, Fixed">'+content+'</textarea>\
						<button class="btn btn-success">Update</button>\
					</div>\
				</div>\
			</div>\
			</div>').appendTo(document.body);

		return modal;
	};

	$(document).on("click", ".edit-link", function(ev) {
		var $el = $(this).parent().parent();
		var $el_copy = $el.clone();

		var $edit_btn = $el_copy.find(".edit-link").parent().remove();

		var $modal = get_modal(html_beautify($el_copy.html())).modal("show");
		$modal.find(":input:first").focus();
		$modal.find(".btn-success").click(function(ev2) {
			var html = $modal.find("textarea").val();
			if(!html) {
				$el.remove();
			} else {
				$el.html(html);
				$edit_btn.appendTo($el);
			}
			$modal.modal("hide");
			return false;
		})
	});

	$(document).on("click", ".remove-link", function(ev) {
		$(this).parent().parent().remove();
	});

	</script>
</head>
<body style="background-color: #ddd;">
	<nav class="navbar navbar-default navbar-fixed" role="navigation">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
		<a class="navbar-brand" href="#">PHP-RICK: ETL</a>
		</div>
		<form class="navbar-form navbar-left">
		<div class="form-group">
			<select class="form-control" id="n-columns">
				<option value="1">1 Column</option>
				<option value="2">2 Columns</option>
			</select>
		</div>
        <button type="submit" class="btn btn-success">Save</button>
        <button type="submit" class="btn btn-primary">Load</button>
		<button type="submit" class="btn btn-primary" data-clipboard-text = "testing" id="copy-to-clipboard">Copy Source</button>
		</form>
	</nav>
	<div style="margin-top: -20px;">
		<div class="row" style="margin-right: 0px;">
			<div class="col-md-3" style="padding: 0px 30px 0px 30px; background-color: #fff;">
				<!--<h3>Components</h3>-->
				<form role="form" style="margin-top:10px;">

                    <!-- EXTRACT -->
                    <h4 style="margin-top:0px;">Extract</h4>
					<div class="form-group draggable well" style="background-color: rgb(86, 61, 124, 0.2);">

					<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">First and last name</span>
					</div>
					<input type="text" aria-label="First name" class="form-control">
					<input type="text" aria-label="Last name" class="form-control">
					</div>

						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon1">Type</span>
							</div>
							<select class="form-control" id="e-select-1" aria-describedby="basic-addon1">
								<option value="csv">CSV</option>
								<option value="xlsx">XLSX</option>
								<option value="query">QUERY</option>
							</select>
						</div>

						<label for="e-input-source-1">Source</label>
						<input type="text" class="form-control" id="e-input-source-1" placeholder="Enter absolute path">
						<p class="help-block">Example: /home/jgaydos/drop/file.csv</p>

                        <label for="e-input-table-1" >Table Name</label>
						<input type="text" class="form-control" id="e-input-table-1"
							placeholder="Enter table name">
						<p class="help-block">For internal SQLite database table.</p>
					</div>

                    <!-- TRANSFORM -->
                    <h4 style="margin-top:0px;">Transform</h4>
					<div class="form-group draggable well" style="background-color: rgb(40, 167, 69, 0.2);">

						<label for="t-input-table-1">Table Name</label>
						<input type="text" class="form-control" id="t-input-table-1"
							placeholder="Enter table name">
						<p class="help-block">For internal SQLite database table.</p>
					</div>

                    <!-- LOAD -->
                    <h4 style="margin-top:0px;">Load</h4>
					<div class="form-group draggable well" style="background-color: rgb(0, 123, 255, 0.2);">

                        <label for="l-select--1">Type</label>
                        <select class="form-control" id="l-select-1">
							<option value="csv">CSV</option>
							<option value="xlsx">XLSX</option>
							<option value="query">QUERY</option>
						</select>

						<label for="l-input-source-1">Target</label>
						<input type="text" class="form-control" id="l-input-source-1" placeholder="Enter absolute path">
						<p class="help-block">Example: /home/jgaydos/drop/file.csv</p>

                        <label for="l-input-table-1">Table Name</label>
						<input type="text" class="form-control" id="l-input-table-1"
							placeholder="Enter table name">
						<p class="help-block">For internal SQLite database table.</p>
					</div>

				</form>
			</div>
			<div class="col-md-9" style="padding: 30px;">
				<div style="background-color: #fff; border-radius: 5px; padding: 20px;
						box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175); ">
					<div class="text-muted empty-form text-center" style="font-size: 24px;">Drag & Drop elements to build ETL job.</div>
					<div class="row form-body">
						<div class="col-md-12 droppable sortable">
						</div>
						<div class="col-md-6 droppable sortable" style="display: none;">
						</div>
						<div class="col-md-6 droppable sortable" style="display: none;">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</body>
</html>
