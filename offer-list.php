<?php
/*
Plugin Name: 	Offer list
Description: 	Now you can build a list of blocks representing your offers. Users can use search input to find these positions easly.
Version: 		1.0.0
Author:			Mateusz Styrna
Author URI:		https://mateusz-styrna.pl/
Plugin URI:		https://wordpress.org/plugins/offer-list/
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//actions
add_action('admin_init', 'offl_register_settings');
add_action('admin_menu', 'offl_settings_menu');
add_action('init', 'offl_list_post_type');
add_action('wp_enqueue_scripts', 'offl_assets');

//shortcode
add_shortcode('offer_list', 'offl_display');

function offl_list_post_type() {
	$labels = array(
		'name' 					=> __( 'Offer list positions', 'Post Type General Name', 'offer-list' ),
		'singular_name' 		=> __( 'Offer list position', 'Post Type Singular Name', 'offer-list' ),
		'menu_name' 			=> __( 'Offer list', 'offer-lsit' ),
		'name_admin_bar' 		=> __( 'Offer list', 'offer-list' ),
		'archives' 				=> __( 'Archive', 'offer-list' ),
		'attributes' 			=> __( 'Attributes', 'offer-list' ),
		'all_items' 			=> __( 'All list positions', 'offer-list' ),
		'add_new_item'          => __( 'Add new position', 'offer-list' ),
        'add_new'               => __( 'Add new position', 'offer-list' ),
        'new_item'              => __( 'New position', 'offer-list' ),
        'edit_item'             => __( 'Edit position', 'offer-list' ),
        'update_item'           => __( 'Update position', 'offer-list' ),
        'search_items'          => __( 'Search', 'offer-list' ),
        'featured_image'        => __( 'Possition image', 'offer-list' ),
        'set_featured_image'    => __( 'Set position image', 'offer-list' ),
        'remove_featured_image' => __( 'Remove position image', 'offer-list' ),
        'use_featured_image'    => __( 'Use position image', 'offer-list' ),
        'insert_into_item'      => __( 'Insert', 'offer-list' ),
        'uploaded_to_this_item' => __( 'Send', 'offer-list' )
	);
	$args = array(
        'label'                 => __( 'Offer list', 'offer-list' ),
        'description'           => __( 'All positions', 'offer-list' ),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields'),
        'hierarchical'          => false,
        'public'                => true,
        'rewrite'               => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-grid-view',
        'menu_position'         => 2,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => false,
        'capability_type'       => 'page',
        'delete_with_user'      => false
    );
    register_post_type( 'offer_list', $args );
}

function offl_display() {
	if (!is_admin()) {
			wp_enqueue_style('offl-css');
			wp_enqueue_script('offl-search-js');
			wp_enqueue_script('offl-hide-js');
			ob_start();
		?>
		<div class="offl">
			<?php if (get_option('offl_searchbar_enabled') && wp_validate_boolean(get_option('offl_searchbar_enabled'))) { ?>
			<input type="text" name="offl_search" class="offl__search" id="offl__search" placeholder="<?php echo esc_html(get_option('offl_searchbar_placeholder')); ?>">
			<?php
			}
			$args = array(
		        'post_type'=> 'offer_list',
		        'post_status' => 'publish',
		        'posts_per_page' => -1
		    );
	
		    $result = new WP_Query( $args );
		    ?>
		    <ul class="offl__list">
		    <?php
		    if ($result->have_posts()) {
		        while ($result->have_posts()) {
		            $result->the_post();
		    ?>
			<li class="offl__item">
				<a class="offl__link" href="<?php echo esc_url(get_post_meta(get_the_ID(), 'url', true)); ?>">
					<?php the_post_thumbnail( 'full', ['class' => 'offl__img'] ); ?>
					<div class="offl__content">
						<h<?php echo esc_html(get_option('offl_h_tag')); ?> class="offl__title"><?php the_title(); ?></h<?php echo esc_html(get_option('offl_h_tag')); ?>>
						<?php the_content(); ?>
				    </div>
				</a>
			</li>
			<?php
		        wp_reset_postdata();
				}
				?>
			</ul>
			<button class="offl__show_all_btn" id="offl_show_all_btn"><?php echo __('Show all.', 'offer-list'); ?></button>
				<?php
		    } else {
		    	?>
		    	<p><?php echo __('No positions found.', 'offer-list'); ?></p>
		    	<?php
		    }
		    ?>
		</div>
		<?php
		return ob_get_clean();
			
	}
}

function offl_register_settings() {
	register_setting('offer_list_options', 'offl_searchbar_enabled', array('default' => true));
	register_setting('offer_list_options', 'offl_searchbar_placeholder', array('default' => 'Search...'));
	register_setting('offer_list_options', 'offl_visible_positions_number', array('default' => 6));
	register_setting('offer_list_options', 'offl_h_tag', array('default' => 2));
}

function offl_settings_menu() {
	add_submenu_page( 'edit.php?post_type=offer_list', 'Settings', 'Settings', 'manage_options', 'offer_list_settings', 'offl_settings_page', 3 );
}

function offl_settings_page() {
	if (!empty($_POST['offl_modify'])) {
		if (wp_validate_boolean($_POST['offl_searchbar_enabled'])) {
			$is_enabled = isset($_POST['offl_searchbar_enabled']) ? 1 : 0;
			update_option('offl_searchbar_enabled', $is_enabled);
		}

		$offl_searchbar_placeholder = sanitize_text_field($_POST['offl_searchbar_placeholder']);
		if (isset($offl_searchbar_placeholder))
			update_option('offl_searchbar_placeholder', $offl_searchbar_placeholder);
		
		update_option('offl_visible_positions_number', intval($_POST['offl_visible_positions_number']));

		if (intval($_POST['offl_h_tag']) > 0 && intval($_POST['offl_h_tag']) < 7)
			update_option('offl_h_tag', intval($_POST['offl_h_tag']));
		?>
		<div class="notice notice-success"><p><?php echo __('Settings updated.', 'offer-list'); ?></p></div>
		<?php
	}

	$searchbar_enabled = get_option('offl_searchbar_enabled');
	$searchbar_placeholder = get_option('offl_searchbar_placeholder');
	$visible_positions_number = get_option('offl_visible_positions_number');
	$h_tag = get_option('offl_h_tag');
	?>
	<h1>Settings</h1>
	<form method="POST">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="offl_searchbar_enabled"><?php echo __('Show searchbar:', 'offer-list'); ?> </label>
					</th>
					<td>
						<input id="offl_searchbar_enabled" type="checkbox" name="offl_searchbar_enabled" <?php if ($searchbar_enabled == true) echo "checked=true"; ?>>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="offl_searchbar_placeholder"><?php echo __('Searchbar placeholder text:', 'offer-list'); ?> </label>
					</th>
					<td>
						<input type="text" name="offl_searchbar_placeholder" id="offl_searchbar_placeholder" value="<?php echo esc_html($searchbar_placeholder); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="offl_visible_positions_number"><?php echo __('How many positions should be visible before "show all" button:', 'offer-list'); ?> </label>
					</th>
					<td>
						<input type="number" name="offl_visible_positions_number" id="offl_visible_positions_number" value="<?php echo esc_html($visible_positions_number); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="offl_searchbar_placeholder"><?php echo __('What heading level/tag do you want to use in positions:', 'offer-list'); ?> </label>
					</th>
					<td>
						<input type="number" max="6" min="1" name="offl_h_tag" id="offl_searchbar_placeholder" value="<?php echo esc_html($h_tag); ?>">
						<p class="description">(1-6, e.g &#60;h1&#62; = 1, &#60;h2&#62; = 2, &#60;h3&#62; = 3)</p>
					</td>
				</tr>
				<tr>
					<td>
						<input type="hidden" name="offl_modify" value="true">
						<input type="submit" class="button-primary" value="<?php echo __('Save', 'offer-list'); ?>">
					</td>
				</tr>
			</tbody>
		</table>
		<p style="font-size: 25px;"><?php echo __("To place a list on your website, simply use"); ?> <strong style="cursor: pointer;" id="offl_shortcode">[offer_list]</strong> <?php echo __("shortcode"); ?>.</p><input id="offl_shortcode_input" type="text" style="position: absolute; left: -999999px; top: -999999px; cursor: pointer;" value="[offer_list]">
		<script type="text/javascript">
			document.querySelector('#offl_shortcode').addEventListener('click', ()=>{
				document.querySelector('#offl_shortcode_input').focus();
				document.querySelector('#offl_shortcode_input').select();
				document.execCommand("Copy");
				document.querySelector('#offl_shortcode').innerText = '[offer_list] <?php echo __("(Copied!)"); ?>';
			});
		</script>
	</form>
	<?php
}

function offl_assets() {
	wp_register_style( 'offl-css',  plugins_url( '/css/style.css', __FILE__ ), array(), '1.0.0', 'all');
	wp_register_script( 'offl-search-js',  plugins_url( '/js/search.js', __FILE__ ), '', '1.0.0', true);
	wp_register_script( 'offl-hide-js',  plugins_url( '/js/hide.js', __FILE__ ), '', '1.0.0', true);
	$script_params = array(
	    'number' => get_option('offl_visible_positions_number'),
	);
	wp_localize_script('offl-search-js', 'positions', $script_params);
	wp_localize_script('offl-hide-js', 'positions', $script_params);
}

load_plugin_textdomain('offer-list', false, basename( dirname( __FILE__ ) ) . '/languages' );
?>
