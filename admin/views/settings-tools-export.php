<?php 

// vars
$field_groups = fields_extract_var( $args, 'field_groups');

?>
<div class="wrap fields-settings-wrap">
	
	<h2><?php _e('Tools', 'fields'); ?></h2>
	
	<div class="fields-box">
		<div class="title">
			<h3><?php _e('Export Field Groups to PHP', 'fields'); ?></h3>
		</div>
		
		<div class="inner">
			<p><?php _e("The following code can be used to register a local version of the selected field group(s). A local field group can provide many benefits such as faster load times, version control & dynamic fields/settings. Simply copy and paste the following code to your theme's functions.php file or include it within an external file.", 'fields'); ?></p>
			
			<textarea class="pre" readonly="true"><?php
			
			echo "if( function_exists('fields_add_local_field_group') ):" . "\r\n" . "\r\n";
			
			foreach( $field_groups as $field_group ) {
								
				// code
				$code = var_export($field_group, true);
				
				
				// change double spaces to tabs
				$code = str_replace("  ", "\t", $code);
				
				
				// replace
				$replace = array(
					'/([\t\r\n]+?)array/'			=> 'array',
					'/[0-9]+ => array/'				=> 'array'
				);
				
				
				// textdomain
				if( $domain = fields_get_setting('export_textdomain') ) {
					
					$replace["/'title' => (.+),/"] = "'title' => __($1, '$domain'),";
					$replace["/'label' => (.+),/"] = "'label' => __($1, '$domain'),";
					$replace["/'instructions' => (.+),/"] = "'instructions' => __($1, '$domain'),";
					
				}
				
				
				// correctly formats "=> array("
				$code = preg_replace( array_keys($replace), array_values($replace), $code);
				
				
				// echo
				echo "fields_add_local_field_group({$code});" . "\r\n" . "\r\n";
			
			}
			
			echo "endif;";
			
			?></textarea>
            
		</div>
		
	</div>
	
</div>
<div class="fields-hidden">
	<style type="text/css">
		textarea.pre {
			width: 100%;
			padding: 15px;
			font-size: 14px;
			line-height: 1.5em;
			resize: none;
		}
	</style>
	<script type="text/javascript">
	(function($){
		
		var i = 0;
		
		$(document).on('click', 'textarea.pre', function(){
			
			if( i == 0 )
			{
				i++;
				
				$(this).focus().select();
				
				return false;
			}
					
		});
		
		$(document).on('keyup', 'textarea.pre', function(){
		
		    $(this).height( 0 );
		    $(this).height( this.scrollHeight );
		
		});
	
		$(document).ready(function(){
			
			$('textarea.pre').trigger('keyup');
	
		});
	
	})(jQuery);
	</script>
</div>
