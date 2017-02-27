<div class="panel panel-default">
  <div class="panel-body">
		<table class="table table-bordered table-striped dataTable" id="groupsDatabale" width="100%">
			<thead><?php
				$checked = "";
				if($fbaccount->UserDefaultFbAccount()){
					echo "
					<tr>
						<td></td>
						<td>Timeline</td>
						<td colspan='3'>".lang('VIEW_PROFILE')."</td>
						<td>".lang('POST_STATUS')."</td>
					</tr>
					<tr class='groupName' id='me'>
						<td>
							<input type='checkbox' class='fbnode checkbox checkbox-style' name='selectGroup[0]' id='selectgroup_me' value='me' ".$checked.">
							<label for='selectgroup_me'></label>
						</td>
						<td class='groupTitle' id='group_me'>".$fbaccountDetails->getLastname()." ".$fbaccountDetails->getFirstname()."</td>
						<td colspan='3'><a href='https://www.facebook.com/".$fbaccount->UserDefaultFbAccount()."' target='_blank'>
							<span class='glyphicon glyphicon-link'></span>&nbsp; ".lang('VIEW_PROFILE')."</a>
						</td>
						<td>
						<span class='postStatus_me postStatus'></span>
						</td>
					</tr>";
				}
			?>
				<tr>
					<td colspan="6" class="groupsOptions">

								<div class="panel panel-default">
									<div class="panel-body">
										<div class="row">
											<div class="col-md-3 col-sm-6">
												<div class="form-group">
													<div class="input-group">
														<?php 
															$currentGroupCategory = Session::exists("groupscategory") ? Session::get("groupscategory") : -1; 
														?>
														<select id="groupscategory" name="groupscategory" class="form-control" onchange="this.form.submit()">	
															<option value="-1" <?php if($currentGroupCategory == -1) echo "selected" ?> ><?php l('-- Categories --'); ?></option>

															<?php
												  			foreach ($groupsCategories as $gc) {
												  				if($currentGroupCategory == $gc->id) 
												  					echo "<option value=".$gc->id." selected >".$gc->category_name."</option>";
												  				else
												  					echo "<option value=".$gc->id.">".$gc->category_name."</option>";
												  			}?>
												  		</select>
												  		<div class="input-group-btn">
												  			<button class='btn btn-danger' name="deleteCategory" value="<?php echo $currentGroupCategory; ?>" >
												  				<i class="fa fa-fw fa-trash"></i>
												  			</button>
												  		</div>
											  		</div>
									  			</div>
									  		</div>
											<div class="col-md-3  col-sm-6">
												<div class="form-group">
													<div class="input-group">	
										  				<input type="text" id="newCategoryName" name="newCategoryName" class="form-control" placeholder="<?php echo lang('ADD_NEW_CATEGORY'); ?>"/>
										  				<span class="input-group-btn">
														<button type="button" id="addCategory" data-toggle="tooltip" data-placement="top" title="<?php echo lang('ADD_NEW_CATEGORY'); ?>" class="btn btn-primary" >
															<i class="fa fa-fw fa-plus"></i>
														</button>
														</span>
													</div>
												</div>
											</div>

											<div class="col-md-2  col-sm-6">
												<div class="form-group">
													<button type="button" id="addSelectedGroups" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="<?php echo lang('ADD_SELECTED_GROUPS'); ?>"/><i class="fa fa-fw fa-plus"></i></button>
													
													<button id="deleteSelectedGroups"  class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="<?php echo lang('DELETE_SELECTED_GROUPS'); ?>"/><i class="fa fa-fw fa-trash"></i></button>
												</div>
											</div>

											<div class="col-md-4  col-sm-6">
												<div class="form-group groupVtoggle">
													<input type='checkbox' onchange="this.form.submit()" class='checkbox checkbox-style' name='showGroups' id='showGroups' <?php if($user->Options()->show_groups == 1) echo 'checked'; ?> >
													<label for='showGroups'></label>
													<label for='showGroups'><?php l('Show groups'); ?></label>
												</div>
												<div class="form-group">
													<input type='checkbox' onchange="this.form.submit()" class='checkbox checkbox-style' name='showPages' id='showPages' <?php if($user->Options()->show_pages == 1) echo 'checked'; ?>>
													<label for='showPages'></label>
													<label for='showPages'><?php l('Show pages'); ?></label>
												</div>
											</div>
										</div>
									</div>
								</div>

					</td>
				</tr>
				<tr>
					<td width="20px">
						<input type='checkbox' id="checkbox-all" class="check-all checkbox-style" name='selectAllGroup' <?php if(Input::Get("selectAllGroup")) echo "checked"?>>
						<label for="checkbox-all"></label>
					</td>
					<td><?php echo lang('NODE_NAME'); ?></td>
					<td><?php echo lang('NODE_TYPE'); ?></td>
					<td><?php echo lang('PRIVACY'); ?></td>
					<td><?php echo lang('VISIT_NODE'); ?></td>
					<td><?php echo lang('POST_STATUS'); ?></td>
				</tr>
			</thead>
			<tbody>
			<?php
				$privacy = array('OPEN' => 'eye-open', 'CLOSED' => 'eye-close', 'SECRET' => 'folder-close');
				$i = 0;
				if($userFbNodes){
					foreach($userFbNodes as $node){
						$nodeId = $node['id'];
						$nodeName = $node['name'];
						$nodeType = isset($node['privacy']) ? 'Group' : 'Page';
						$nodePrivacy = isset($node['privacy']) ? $node['privacy'] : '-';

						if(isset($_POST['selectGroup'][$i])) $checked = "checked='checked'";
						
						$tableBody = "<tr class='groupName' id='".$nodeId."'>";

						$tableBody .= "<td>
								<input type='checkbox' class='fbnode checkbox checkbox-style' name='selectGroup[".$i."]' id='selectgroup_".$node['id']."' value='".$nodeId."' ".$checked.">
								<label for='selectgroup_".$nodeId."'></label>
								</td>";


						$tableBody .= "<td class='groupTitle' id='group_".$nodeId."'><input type='hidden' name='selectGroupName[".$i."]' value='".$nodeName."' />".$nodeName."</td>";

						$tableBody .= "<td>".lang($nodeType)."</td>";
						
						$tableBody .= "<td>";

						$tableBody .= isset($node['privacy']) ? "<span class='glyphicon glyphicon-".$privacy[$nodePrivacy]."'></span>&nbsp;".lang($nodePrivacy)."<input type='hidden' name='selectGroupPrivacy[".$i."]' value='".$nodePrivacy."'>" : '-';

						$tableBody .= "</td>";

						$tableBody .= "<td><a href='https://www.facebook.com/".$nodeId."' target='_blank'><span class='glyphicon glyphicon-link'></span>&nbsp; ".lang('visit_'.ucwords($nodeType))."</a></td>";
								
						$tableBody .= "<td><span class='postStatus_".$nodeId." postStatus'></span></td></tr>";

						echo $tableBody;
					}
				}
			?>
			</tbody>
		</table>
		<?php 
			include "addgroupmodal.php";
		?>
		<script src="modules/home/blockgroups/js/addgroup.js"></script>
		<script src="modules/home/blockgroups/js/removegroup.js"></script>
		<script src="modules/home/blockgroups/js/addcategory.js"></script>
	</div>
</div>