<h4 class="tab-title"><i class="fa fa-facebook"></i> <?php echo lang('FB_ACCOUNTS'); ?></h4>

<div class="input-group">
	<?php
	if(Input::Get("loadGroups")){
		$loadGroups = Input::Get("loadGroups") == "on" ? "checked" : "";
	} else {
		$loadGroups = isset($user->Options()->load_groups) && $user->Options()->load_groups == 1 ? "checked" : "";
	}
	?>
	<input type="checkbox" class='checkbox-style' id="loadGroups" name="loadGroups" aria-label="<?php l('Load my groups'); ?>" <?php echo $loadGroups;?> />
	<label for="loadGroups"></label>
	<span class="input-text"><?php l('Load my groups'); ?></span>
</div>

<div class="input-group">
	<?php
	if(Input::Get("loadPages")){
		$loadPages = Input::Get("loadPages") == "on" ? "checked" : "";
	} else {
		$loadPages = isset($user->Options()->load_pages) && $user->Options()->load_pages == 1 ? "checked" : "";
	}
	?>
	<input type="checkbox" class='checkbox-style' id="loadPages" name="loadPages" aria-label="<?php l('Load my pages'); ?>" <?php echo $loadPages;?> />
	<label for="loadPages"></label>
	<span class="input-text"><?php l('Load my pages'); ?></span>
</div>

<div class="input-group">
	<?php
	if(Input::Get("loadOwnPages")){
		$loadOwnPages = Input::Get("loadOwnPages") == "on" ? "checked" : "";
	} else {
		$loadOwnPages = isset($user->Options()->load_own_pages) && $user->Options()->load_own_pages == 1 ? "checked" : "";
	}
	?>
	<input type="checkbox" class='checkbox-style' id="loadOwnPages" name="loadOwnPages" aria-label="<?php l('Load my own pages'); ?>" <?php echo $loadOwnPages;?> />
	<label for="loadOwnPages"></label>
	<span class="input-text"><?php l('Load my own pages'); ?></span>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="input-group">
			<label for="limitImportGroups">
				<?php echo lang('Maximum groups to import'); ?>
			</label>
			<?php 
				if(!Input::get('limitImportGroups') && isset($user->Options()->limitImportGroups)){
					$limitImportGroups = $user->Options()->limitImportGroups;
				}else{
					$limitImportGroups = Input::get('limitImportGroups');
				}
			?>
			<input name="limitImportGroups" id="limitImportGroups" type="number" value="<?php echo $limitImportGroups; ?>" class="form-control"  min="1" max="5000">
		</div>	
	</div>
	<div class="col-md-6">
		<div class="input-group">
			<label for="limitImportPages">
				<?php echo lang('Maximum pages to import'); ?>
			</label>
			<?php 
				if(!Input::get('limitImportPages') && isset($user->Options()->limitImportPages)){
					$limitImportPages = $user->Options()->limitImportPages;
				}else{
					$limitImportPages = Input::get('limitImportPages');
				}
			?>
			<input name="limitImportPages" id="limitImportPages" type="number" value="<?php echo $limitImportPages; ?>" class="form-control"  min="1" max="5000">
		</div>
	</div>
</div>
<br />
<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addNewFbAccount" style='float:right;'><?php echo lang('ADD_UPDATE_FACEBOOK_ACCOUNT'); ?></button>
<div class="clear"></div>

<table class='table table-bordered table-striped'>
	<thead>
		<tr>
			<td><?php echo lang('FB_USER_ID'); ?> (Scoped ID)</td>
			<td><?php echo lang("FIRSTNAME"); ?></td>
			<td><?php echo lang("LASTNAME"); ?></td>
			<td></td>
		</tr>
	</thead>
	<tbody id="fbAccounts">
		<?php 
			if($fbaccount->getAll()){
				foreach($fbaccount->getAll() as $fba){
					echo "<tr>";
					echo "<td>".$fba->getFbId()."</td>
						<td>".$fba->getFirstname()."</td>
						<td>".$fba->getLastname()."</td>
						<td>
							<a href='settings.php?action=deletefbaccount&id=".$fba->getFbId()."' title='".lang('DELETE')."' class='btn btn-danger'>
							<span class='glyphicon glyphicon-trash'></span> ".lang('DELETE')."
							</a>";

							if($fba->getFbId() == $fbaccount->UserDefaultFbAccount()){
								echo "<span class='btn btn-default'>
										<span class='glyphicon glyphicon-ok'></span>
										".lang('DEFAULT')."</span>";
							}

					echo "</td></tr>";
				}
			}else{
				echo "<tr><td colspan='4' class='nodata'>".lang('NO_FB_ACCOUNT_AVAILABLE')."</td></tr>";
			}
		?>
	</tbody>
</table>
<?php include "add_fbaccount.php";?>