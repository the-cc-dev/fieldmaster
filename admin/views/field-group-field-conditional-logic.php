<?php 

// vars
$field = fields_extract_var( $args, 'field');
$groups = fields_extract_var( $field, 'conditional_logic');
$disabled = empty($groups) ? 1 : 0;


// UI needs at least 1 conditional logic rule
if( empty($groups) ) {
	
	$groups = array(
		
		// group 0
		array(
			
			// rule 0
			array()
		
		)
		
	);
	
}

?>
<tr data-name="conditional_logic" class="fields-field">
	<td class="fields-label">
		<label><?php _e("Conditional Logic",'fields'); ?></label>
	</td>
	<td class="fields-input">
		<?php 
		
		fields_render_field(array(
			'type'			=> 'radio',
			'name'			=> 'conditional_logic',
			'prefix'		=> $field['prefix'],
			'value'			=> $disabled ? 0 : 1,
			'choices'		=> array(
								1	=> __("Yes",'fields'),
								0	=> __("No",'fields'),
			),
			'layout'		=> 'horizontal',
			'class'			=> 'conditional-toggle'
		));
		
		?>
		<div class="rule-groups" <?php if($disabled): ?>style="display:none;"<?php endif; ?>>
			
			<?php foreach( $groups as $group_id => $group ): 
				
				// validate
				if( empty($group) ) {
				
					continue;
					
				}
				
				// vars
				// $group_id must be completely different to $rule_id to avoid JS issues
				$group_id = "group_{$group_id}";
				$h4 = ($group_id == "group_0") ? __("Show this field if",'fields') : __("or",'fields');
				
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
							
							
							// vars		
							// $group_id must be completely different to $rule_id to avoid JS issues
							$rule_id = "rule_{$rule_id}";
							$prefix = "{$field['prefix']}[conditional_logic][{$group_id}][{$rule_id}]";
							
							?>
							<tr class="rule" data-id="<?php echo $rule_id; ?>">
								<td class="param">
									<?php 
									
									$choices = array();
									$choices[ $rule['field'] ] = $rule['field'];
									
									// create field
									fields_render_field(array(
										'type'		=> 'select',
										'prefix'	=> $prefix,
										'name'		=> 'field',
										'value'		=> $rule['field'],
										'choices'	=> $choices,
										'class'		=> 'conditional-rule-param',
										'disabled'	=> $disabled,
									));										
		
									?>
								</td>
								<td class="operator">
									<?php 	
									
									$choices = array(
										'=='	=>	__("is equal to",'fields'),
										'!='	=>	__("is not equal to",'fields'),
									);
									
									
									// create field
									fields_render_field(array(
										'type'		=> 'select',
										'prefix'	=> $prefix,
										'name'		=> 'operator',
										'value'		=> $rule['operator'],
										'choices' 	=> $choices,
										'class'		=> 'conditional-rule-operator',
										'disabled'	=> $disabled,
									)); 	
									
									?>
								</td>
								<td class="value">
									<?php 
									
									$choices = array();
									$choices[ $rule['value'] ] = $rule['value'];
									
									// create field
									fields_render_field(array(
										'type'		=> 'select',
										'prefix'	=> $prefix,
										'name'		=> 'value',
										'value'		=> $rule['value'],
										'choices'	=> $choices,
										'class'		=> 'conditional-rule-value',
										'disabled'	=> $disabled,
									));
									
									?>
								</td>
								<td class="add">
									<a href="#" class="fields-button add-conditional-rule"><?php _e("and",'fields'); ?></a>
								</td>
								<td class="remove">
									<a href="#" class="fields-icon fields-icon-minus remove-conditional-rule"></a>
								</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					
				</div>
			<?php endforeach; ?>
			
			<h4><?php _e("or",'fields'); ?></h4>
			
			<a href="#" class="fields-button add-conditional-group"><?php _e("Add rule group",'fields'); ?></a>
			
		</div>
		
	</td>
</tr>