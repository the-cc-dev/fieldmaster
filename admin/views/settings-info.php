<?php 

// extract args
extract( $args );

?>
<div class="wrap about-wrap fields-wrap">
	
	<h1><?php _e("Welcome to FieldMaster",'fields'); ?> <?php echo $version; ?></h1>
	<div class="about-text"><?php printf(__("Thank you for updating! FieldMaster %s is bigger and better than ever before. We hope you like it.", 'fields'), $version); ?></div>
	<div class="fields-icon logo">
		<i class="fields-sprite-logo"></i>
	</div>
	
	<h2 class="nav-tab-wrapper">
		<?php foreach( $tabs as $tab_slug => $tab_title ): ?>
			<a class="nav-tab<?php if( $active == $tab_slug ): ?> nav-tab-active<?php endif; ?>" href="<?php echo admin_url("edit.php?post_type=fields-field-group&page=fields-settings-info&tab={$tab_slug}"); ?>"><?php echo $tab_title; ?></a>
		<?php endforeach; ?>
	</h2>
	
<?php if( $active == 'new' ): ?>
	
	<h2 class="about-headline-callout"><?php _e("A smoother custom field experience", 'fields'); ?></h2>
	
	<div class="feature-section fields-three-col">
		<div>
			<img src="http://assets.advancedcustomfields.com/info/5.0.0/select2.png">
			<h3><?php _e("Improved Usability", 'fields'); ?></h3>
			<p><?php _e("Including the popular Select2 library has improved both usability and speed across a number of field types including post object, page link, taxonomy and select.", 'fields'); ?></p>
		</div>
		<div>
			<img src="http://assets.advancedcustomfields.com/info/5.0.0/design.png">
			<h3><?php _e("Improved Design", 'fields'); ?></h3>
			<p><?php _e("Many fields have undergone a visual refresh to make FieldMaster look better than ever! Noticeable changes are seen on the gallery, relationship and oEmbed (new) fields!", 'fields'); ?></p>
		</div>
		<div>
			<img src="http://assets.advancedcustomfields.com/info/5.0.0/sub-fields.png">
			<h3><?php _e("Improved Data", 'fields'); ?></h3>
			<p><?php _e("Redesigning the data architecture has allowed sub fields to live independently from their parents. This allows you to drag and drop fields in and out of parent fields!", 'fields'); ?></p>
		</div>
	</div>
	
	<hr />
	
	<h2 class="about-headline-callout"><?php _e("Goodbye Add-ons. Hello PRO", 'fields'); ?></h2>
	
	<div class="feature-section fields-three-col">
	
		<div>
			<h3><?php _e("Introducing FieldMaster PRO", 'fields'); ?></h3>
			<p><?php _e("We're changing the way premium functionality is delivered in an exciting way!", 'fields'); ?></p>
			<p><?php printf(__('All 4 premium add-ons have been combined into a new <a href="%s">Pro version of FieldMaster</a>. With both personal and developer licenses available, premium functionality is more affordable and accessible than ever before!', 'fields'), esc_url('http://www.advancedcustomfields.com/pro')); ?></p>
		</div>
		
		<div>
			<h3><?php _e("Powerful Features", 'fields'); ?></h3>
			<p><?php _e("FieldMaster PRO contains powerful features such as repeatable data, flexible content layouts, a beautiful gallery field and the ability to create extra admin options pages!", 'fields'); ?></p>
			<p><?php printf(__('Read more about <a href="%s">FieldMaster PRO features</a>.', 'fields'), esc_url('http://www.advancedcustomfields.com/pro')); ?></p>
		</div>
		
		<div>
			<h3><?php _e("Easy Upgrading", 'fields'); ?></h3>
			<p><?php printf(__('To help make upgrading easy, <a href="%s">login to your store account</a> and claim a free copy of FieldMaster PRO!', 'fields'), esc_url('http://www.advancedcustomfields.com/my-account/')); ?></p>
			<p><?php printf(__('We also wrote an <a href="%s">upgrade guide</a> to answer any questions, but if you do have one, please contact our support team via the <a href="%s">help desk</a>', 'fields'), esc_url('http://www.advancedcustomfields.com/resources/updates/upgrading-v4-v5/'), esc_url('http://support.advancedcustomfields.com')); ?>
			
		</div>
					
	</div>
	
	<hr />
	
	<h2 class="about-headline-callout"><?php _e("Under the Hood", 'fields'); ?></h2>
	
	<div class="feature-section fields-three-col">
		
		<div>
			<h4><?php _e("Smarter field settings", 'fields'); ?></h4>
			<p><?php _e("FieldMaster now saves its field settings as individual post objects", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("More AJAX", 'fields'); ?></h4>
			<p><?php _e("More fields use AJAX powered search to speed up page loading", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("Local JSON", 'fields'); ?></h4>
			<p><?php _e("New auto export to JSON feature improves speed", 'fields'); ?></p>
		</div>
		
		<br />
		
		<div>
			<h4><?php _e("Better version control", 'fields'); ?></h4>
			<p><?php _e("New auto export to JSON feature allows field settings to be version controlled", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("Swapped XML for JSON", 'fields'); ?></h4>
			<p><?php _e("Import / Export now uses JSON in favour of XML", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("New Forms", 'fields'); ?></h4>
			<p><?php _e("Fields can now be mapped to comments, widgets and all user forms!", 'fields'); ?></p>
		</div>
		
		<br />
		
		<div>
			<h4><?php _e("New Field", 'fields'); ?></h4>
			<p><?php _e("A new field for embedding content has been added", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("New Gallery", 'fields'); ?></h4>
			<p><?php _e("The gallery field has undergone a much needed facelift", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("New Settings", 'fields'); ?></h4>
			<p><?php _e("Field group settings have been added for label placement and instruction placement", 'fields'); ?></p>
		</div>
		
		<br />
		
		<div>
			<h4><?php _e("Better Front End Forms", 'fields'); ?></h4>
			<p><?php _e("fields_form() can now create a new post on submission", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("Better Validation", 'fields'); ?></h4>
			<p><?php _e("Form validation is now done via PHP + AJAX in favour of only JS", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("Relationship Field", 'fields'); ?></h4>
			<p><?php _e("New Relationship field setting for 'Filters' (Search, Post Type, Taxonomy)", 'fields'); ?></p>
		</div>
		
		<br />
		
		<div>
			<h4><?php _e("Moving Fields", 'fields'); ?></h4>
			<p><?php _e("New field group functionality allows you to move a field between groups & parents", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("Page Link", 'fields'); ?></h4>
			<p><?php _e("New archives group in page_link field selection", 'fields'); ?></p>
		</div>
		
		<div>
			<h4><?php _e("Better Options Pages", 'fields'); ?></h4>
			<p><?php _e("New functions for options page allow creation of both parent and child menu pages", 'fields'); ?></p>
		</div>
					
	</div>
		
	
	
<?php elseif( $active == 'changelog' ): ?>
	
	<p class="about-description"><?php printf(__("We think you'll love the changes in %s.", 'fields'), $version); ?></p>
	
	<?php
		
	$items = file_get_contents( fields_get_path('readme.txt') );
	$items = explode('= ' . $version . ' =', $items);
	
	$items = end( $items );
	$items = current( explode("\n\n", $items) );
	$items = array_filter( array_map('trim', explode("*", $items)) );
	
	?>
	<ul class="changelog">
	<?php foreach( $items as $item ): 
		
		$item = explode('http', $item);
			
		?>
		<li><?php echo $item[0]; ?><?php if( isset($item[1]) ): ?><a href="http<?php echo $item[1]; ?>" target="_blank">[...]</a><?php endif; ?></li>
	<?php endforeach; ?>
	</ul>

<?php endif; ?>
		
</div>
