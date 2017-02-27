<!-- jQuery and jQuery UI (REQUIRED) -->
<link rel="stylesheet" type="text/css" href="vendor/elfinder/themes/css/jquery-ui.min.css">
<script src="vendor/elfinder/themes/js/jquery-ui.min.js"></script>		<!-- Latest compiled and minified CSS -->
<!-- elFinder CSS (REQUIRED) -->
<link rel="stylesheet" type="text/css" href="vendor/elfinder/themes/css/elfinder.css">
<link rel="stylesheet" type="text/css" href="vendor/elfinder/themes/css/theme.css">

<!-- elFinder JS (REQUIRED) -->
<script src="vendor/elfinder/themes/js/elfinder.min.js"></script>
<?php if(file_exists(ABSPATH . 'vendor/elfinder/themes/js/i18n/elfinder.'.lang("LANG_I18N").'.js')){ ?>
	<script src="vendor/elfinder/themes/js/i18n/elfinder.<?php echo lang('LANG_I18N'); ?>.js"></script>
<?php } ?>

<script type="text/javascript" charset="utf-8">
	$().ready(function() {

		$('#mediaLibraryImage').click(function(){
			$('#mediaLibModalImage').modal('show');
		    getMediaLibImage();
		    $("#URLFrom").val("image");
		});

		$('#mediaLibraryImageLink').click(function(){
			$('#mediaLibModalImage').modal('show');
		    getMediaLibImage();
		    $("#URLFrom").val("link");
		});

		$('#mediaLibraryVideo').click(function(){
			$('#mediaLibModalVideo').modal('show');
		    var fv = $('#elfinderVideo').elfinder({
		        url : 'modules/home/blockpostform/elfinder/connector.php',
		        onlyMimes: ['video/mp4','text/plain'],
		        docked: false,
		        lang: '<?php l("LANG_I18N") ?>',
		        dialog: { width: 600, modal: true },
		        closeOnEditorCallback: true,
		        getFileCallback: function(data) {
		            $('#video').val(data.url);
		            $( "#video" ).trigger('propertychange');
		            $('#mediaLibModalVideo').modal('hide');
		        }
		    }).elfinder('instance');
		});

	});

	function getMediaLibImage(){
	    $('#elfinderImage').elfinder({
	        url : 'modules/home/blockpostform/elfinder/connector.php',
	        //onlyMimes: ['image','text/plain'],
	        docked: false,
	        lang: '<?php l("LANG_I18N") ?>',
	        dialog: { width: 600, modal: true },
	        closeOnEditorCallback: true,
	        getFileCallback: function(data) {
	        	if($("#URLFrom").val() == "image"){
					$("#imageURL").val(data.url);
					$( "#imageURL" ).trigger('propertychange');
	        	}else{
	        		$("#picture").val(data.url);
	        		$( "#picture" ).trigger('propertychange');
	        	}
		        $('#mediaLibModalImage').modal('hide');
		    }
	    }).elfinder('instance');
	}
</script>

<div id="mediaLibModalImage" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php l("Media library"); ?></h4>
			</div>
			<div class="modal-body">
				<div id="elfinderImage"></div>
			</div>
		</div>
	</div>
</div>

<div id="mediaLibModalVideo" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php l("Media library"); ?></h4>
			</div>
			<div class="modal-body">
				<div id="elfinderVideo"></div>
			</div>
		</div>
	</div>
</div>

