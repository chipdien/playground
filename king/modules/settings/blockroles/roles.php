<?php  if (!defined('ABSPATH')) exit('No direct script access allowed');
	
$user = new User();
if(!$user->hasPermission("admin")){
    die(lang("You don't have enough permissions to access this area"));
    exit();
}

$roles = new Roles();

?>
<h4 class="tab-title"><i class="fa fa-fw fa-users"></i> <?php l('Roles'); ?></h4>
<div class="managepermissionsErrors"></div>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<td><?php l('Group name'); ?></td>
			<td><?php l('Max posts per day'); ?> <small>(<?php l("Value must be <= 5000"); ?>)</small></td>
			<td><?php l('Max facebook accounts'); ?> <small>(<?php l("Value must be <= 50"); ?>)</small></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($roles->getRoles() as $role) { ?>
		<tr>
			<td><?php l($role->getName()); ?></td>
			<?php if(!isset(json_decode($role->getPermissions(),true)['admin'])) { ?>
					<td>
						<input type="number" id="maxPostsPerDay" data-roleid="<?php echo $role->getId(); ?>" name="maxPostsPerDay[<?php echo $role->getId(); ?>]" class="form-control" value="<?php echo $role->getMaxPostsPerDay(); ?>">
					</td>
					<td>
						<input type="number" id="maxFbAccounts" data-roleid="<?php echo $role->getId(); ?>" name="maxFbAccounts[<?php echo $role->getId(); ?>]" class="form-control" value="<?php echo $role->getMaxFbAccounts(); ?>">
					</td>
				<?php }else{
					echo "<td></td>";
					echo "<td></td>";
				} ?>
		</tr>
		<?php } ?>
	</tbody>
</table>
<script src="modules/settings/blockroles/js/update.js"></script>
