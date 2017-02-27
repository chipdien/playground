<div id="addToCategoryModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php echo lang("ADD_GROUP_TO_CATEGORY"); ?></h4>
			</div>
			<div class="modal-body">
				<div class="addCateMsgBoxModal"></div>
				<select name="groupscategoriesAdd" class="form-control groupscategories">
					<?php
		  			foreach ($groupsCategories as $gc) {
		  				echo "<option value=".$gc->id.">".$gc->category_name."</option>";
		  			}?>
		  		</select>
			</div>
			<div class="modal-footer">
				<a type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("CLOSE"); ?></a>
				<a type="button" id="modalAddCateBtn" class="btn btn-primary"><?php echo lang("ADD"); ?></a>
			</div>
		</div>
	</div>
</div>