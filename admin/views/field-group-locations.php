<?php

// vars
$rule_types = apply_filters('fields/location/rule_types', array(
	__("Post",'fields') => array(
		'post_type'		=>	__("Post Type",'fields'),
		'post_status'	=>	__("Post Status",'fields'),
		'post_format'	=>	__("Post Format",'fields'),
		'post_category'	=>	__("Post Category",'fields'),
		'post_taxonomy'	=>	__("Post Taxonomy",'fields'),
		'post'			=>	__("Post",'fields')
	),
	__("Page",'fields') => array(
		'page_template'	=>	__("Page Template",'fields'),
		'page_type'		=>	__("Page Type",'fields'),
		'page_parent'	=>	__("Page Parent",'fields'),
		'page'			=>	__("Page",'fields')
	),
	__("User",'fields') => array(
		'current_user'		=>	__("Current User",'fields'),
		'current_user_role'	=>	__("Current User Role",'fields'),
		'user_form'			=>	__("User Form",'fields'),
		'user_role'			=>	__("User Role",'fields')
	),
	__("Forms",'fields') => array(
		'attachment'	=>	__("Attachment",'fields'),
		'taxonomy'		=>	__("Taxonomy Term",'fields'),
		'comment'		=>	__("Comment",'fields'),
		'widget'		=>	__("Widget",'fields')
	)
));

$rule_operators = apply_filters( 'fields/location/rule_operators', array(
	'=='	=>	__("is equal to",'fields'),
	'!='	=>	__("is not equal to",'fields'),
));

?>
<div class="fields-field">
	<div class="fields-label">
		<label><?php _e("Rules",'fields'); ?></label>
		<p><?php _e("Create a set of rules to determine which edit screens will use these fields API",'fields'); ?></p>
	</div>
	<div class="fields-input">
		<div class="rule-groups">

			<?php foreach( $field_group['location'] as $group_id => $group ):

				// validate
				if( empty($group) ) {

					continue;

				}


				// $group_id must be completely different to $rule_id to avoid JS issues
				$group_id = "group_{$group_id}";
				$h4 = ($group_id == "group_0") ? __("Show this field group if",'fields') : __("or",'fields');

				?>

				<div class="rule-group" data-id="<?php echo $group_id; ?>">

					<h4><?php echo $h4; ?></h4>

					<table class="fields-table -clear">
						<tbody>
							<?php foreach( $group as $rule_id => $rule ):

								// valid rule
								$rule = wp_parse_args( $rule, array(
									'field'		=>	'',
									'operator'	=>	'==',
									'value'		=>	'',
								));


								// $group_id must be completely different to $rule_id to avoid JS issues
								$rule_id = "rule_{$rule_id}";

								?>
								<tr data-id="<?php echo $rule_id; ?>">
								<td class="param"><?php

									// create field
									fields_render_field(array(
										'type'		=> 'select',
										'prefix'	=> "fields_field_group[location][{$group_id}][{$rule_id}]",
										'name'		=> 'param',
										'value'		=> $rule['param'],
										'choices'	=> $rule_types,
										'class'		=> 'location-rule-param'
									));

								?></td>
								<td class="operator"><?php

									// create field
									fields_render_field(array(
										'type'		=> 'select',
										'prefix'	=> "fields_field_group[location][{$group_id}][{$rule_id}]",
										'name'		=> 'operator',
										'value'		=> $rule['operator'],
										'choices' 	=> $rule_operators,
										'class'		=> 'location-rule-operator'
									));

								?></td>
								<td class="value"><?php

									$this->render_location_value(array(
										'group_id'	=> $group_id,
										'rule_id'	=> $rule_id,
										'value'		=> $rule['value'],
										'param'		=> $rule['param'],
										'class'		=> 'location-rule-value'
									));

								?></td>
								<td class="add">
									<a href="#" class="fields-button add-location-rule"><?php _e("and",'fields'); ?></a>
								</td>
								<td class="remove">
									<a href="#" class="fields-icon fields-icon-minus remove-location-rule"></a>
								</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

				</div>
			<?php endforeach; ?>

			<h4><?php _e("or",'fields'); ?></h4>

			<a href="#" class="fields-button add-location-group"><?php _e("Add rule group",'fields'); ?></a>

		</div>
	</div>
</div>
<script type="text/javascript">
if( typeof fields !== 'undefined' ) {

	fields.postbox.render({
		'id': 'fields-field-group-locations',
		'label': 'left'
	});

}
</script>
