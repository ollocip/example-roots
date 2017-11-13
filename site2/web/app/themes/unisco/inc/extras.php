<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package unisco
 */

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function unisco_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'unisco_pingback_header' );

/**
 * Top menu fallback
 * @since  1.0.0
 *
 * @param  array $args
 *
 * @return string
 */
function unisco_top_menu( $args ) {
	extract( $args );
	$link = $link_before
	        . '<a href="' . admin_url( 'nav-menus.php' ) . '" class="nav-link nav-add-menu-link text-center">' . $before . __( 'Add a menu', 'unisco' ) . $after . '</a>'
	        . $link_after;

	if ( ! current_user_can( 'manage_options' ) ) {
		$link = null;
	}
	// We have a list
	if ( false !== stripos( $items_wrap, '<ul' ) or false !== stripos( $items_wrap, '<ol' ) ) {
		$link = "<li class='nav-item'>$link</li>";
	}
	$output = sprintf( $items_wrap, $menu_id, $menu_class, $link );
	if ( ! empty ( $container ) ) {
		$output = "<$container class='$container_class' id='$container_id'>$output</$container>";
	}
	if ( $echo ) {
		echo wp_kses( $output, array(
			'a' => array(
				'href'  => array(),
				'title' => array(),
				'class' => array(),
				'id'    => array()
			)
		) );
	}

	return $output;
}

/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 *
 * @return int (Maybe) modified excerpt length.
 */
function unisco_custom_excerpt_length( $length ) {
	return 35;
}
add_filter( 'excerpt_length', 'unisco_custom_excerpt_length', 999 );

/**
 * Filter the "read more" excerpt string link to the post.
 *
 * @param string $more "Read more" excerpt string.
 *
 * @return string (Maybe) modified "read more" excerpt string.
 */
function unisco_excerpt_more( $more ) {
	return sprintf( '<br /><a class="read-more" href="%1$s">%2$s</a>',
		esc_url( get_permalink( get_the_ID() ) ),
		__( 'Read More', 'unisco' )
	);
}
add_filter( 'excerpt_more', 'unisco_excerpt_more' );

function unisco_search_form( $form ) {
	$form = '<form role="search" method="get" id="searchform" class="searchform" action="' . esc_url( home_url( '/' ) ). '" >
    <div><label class="screen-reader-text" for="s">' . __( 'Search for:', 'unisco' ) . '</label>
    <input type="text" placeholder="' . esc_attr__( 'Search', 'unisco' ) . '" value="' . get_search_query() . '" name="s" id="s" class="blog-search" />
    <input type="submit" id="searchsubmit" value="' . esc_attr__( 'Search', 'unisco' ) . '" class="btn btn-warning btn-blogsearch text-uppercase" />
    </div>
    </form>';

	return $form;
}
add_filter( 'get_search_form', 'unisco_search_form', 100 );

function unisco_format_comment( $comment, $args, $depth ) {

	// $GLOBALS['comment'] = $comment; ?>

    <div <?php comment_class( 'row' ); ?> id="comment-<?php comment_ID(); ?>">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2">
                    <div class="blodpost-tab-img">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), $size = '70' ); ?>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="blogpost-tab-description">
                        <h6 class="comment-author">
                            <a href="<?php echo esc_url( $comment->comment_author_url ); ?>"><?php echo esc_html( $comment->comment_author ); ?></a>
                        </h6>
						<?php comment_text(); ?>
						<?php if ( $comment->comment_approved == '0' ) : ?>
                            <em><?php esc_html_e( 'Your comment is awaiting moderation.', 'unisco' ) ?></em><br/>
						<?php endif; ?>
                        <p class="blogpost-rply">
							<?php comment_reply_link( array_merge( $args, array( 'depth'     => $depth,
							                                                     'max_depth' => $args['max_depth']
							) ) ) ?>
                            <a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>"><span><?php printf( '%1$s', get_comment_date(), get_comment_time() ) ?></span></a>
                        </p>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>

<?php }

function unisco_comment_form_submit_button( $submit_button, $args ) {
	?>
    <div class="col-12">
        <input type="submit" name="<?php echo esc_attr( $args['name_submit'] ); ?>"
               value="<?php echo esc_attr( $args['label_submit'] ); ?>"
               class="<?php echo esc_attr( $args['class_submit'] ); ?>"/>
    </div>
    <!-- // end .col-12 -->
	<?php
}
add_filter( 'comment_form_submit_button', 'unisco_comment_form_submit_button', 10, 2 );

/**
 * function to darken hex color
 * https://coderwall.com/p/dvecdg/darken-hex-color-in-php
 *
 * @param string $rgb
 * @param int $darker
 *
 * @return string
 */
function unisco_darken_color( $rgb, $darker=2 ) {
	$hash = (strpos($rgb, '#') !== false) ? '#' : '';
	$rgb = (strlen($rgb) == 7) ? str_replace('#', '', $rgb) : ((strlen($rgb) == 6) ? $rgb : false);
	if(strlen($rgb) != 6) return $hash.'000000';
	$darker = ($darker > 1) ? $darker : 1;

	list($R16,$G16,$B16) = str_split($rgb,2);

	$R = sprintf("%02X", floor(hexdec($R16)/$darker));
	$G = sprintf("%02X", floor(hexdec($G16)/$darker));
	$B = sprintf("%02X", floor(hexdec($B16)/$darker));

	return $hash.$R.$G.$B;
}

/**
 * Add custom form element to all widgets
 *
 * @param $t
 * @param $return
 * @param $instance
 *
 * @return array
 */
function unisco_in_widget_form( $t, $return, $instance ){
	if ( !isset( $instance['unisco_widget_size'] ) ) {
		$instance['unisco_widget_size'] = '12';
	}
	?>
    <p>
        <label for="<?php echo esc_attr( $t->get_field_id('unisco_widget_size') ); ?>"><?php echo esc_html('Size:','unisco'); ?></label>
        <select id="<?php echo esc_attr( $t->get_field_id('unisco_widget_size') ); ?>" name="<?php echo esc_attr( $t->get_field_name('unisco_widget_size') ); ?>">
            <option <?php selected( $instance['unisco_widget_size'], '1' ); ?> value="1">1</option>
            <option <?php selected( $instance['unisco_widget_size'], '2' ); ?> value="2">2</option>
            <option <?php selected( $instance['unisco_widget_size'], '3' ); ?> value="3">3</option>
            <option <?php selected( $instance['unisco_widget_size'], '4' ); ?> value="4">4</option>
            <option <?php selected( $instance['unisco_widget_size'], '5' ); ?> value="5">5</option>
            <option <?php selected( $instance['unisco_widget_size'], '6' ); ?> value="6">6</option>
            <option <?php selected( $instance['unisco_widget_size'], '7' ); ?> value="7">7</option>
            <option <?php selected( $instance['unisco_widget_size'], '8' ); ?> value="8">8</option>
            <option <?php selected( $instance['unisco_widget_size'], '9' ); ?> value="9">9</option>
            <option <?php selected( $instance['unisco_widget_size'], '10' ); ?> value="10">10</option>
            <option <?php selected( $instance['unisco_widget_size'], '11' ); ?> value="11">11</option>
            <option <?php selected( $instance['unisco_widget_size'], '12' ); ?> value="12">12</option>
        </select>
    </p>
	<?php
	return array( $t, $return, $instance );
}
add_action('in_widget_form', 'unisco_in_widget_form', 5, 3);

/**
 * Store custom form element value on widget save
 *
 * @param $instance
 * @param $new_instance
 * @param $old_instance
 *
 * @return mixed
 */
function unisco_in_widget_form_update( $instance, $new_instance, $old_instance ){
	$instance['unisco_widget_size'] = $new_instance['unisco_widget_size'];
	return $instance;
}
add_filter('widget_update_callback', 'unisco_in_widget_form_update',5,3);

/**
 * Modify widget output for custom form element
 *
 * @param $params
 *
 * @return mixed
 */
function unisco_dynamic_sidebar_params( $params ){
	global $wp_registered_widgets;
	$widget_id = $params[0]['widget_id'];
	$widget_obj = $wp_registered_widgets[$widget_id];
	$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
	$widget_num = $widget_obj['params'][0]['number'];
	if( isset( $widget_opt[$widget_num]['unisco_widget_size'] ) )
		$size = 'col-md-' . $widget_opt[$widget_num]['unisco_widget_size'] . ' ';
	else
		$size = 'col-md-12 ';
	$params[0]['before_widget'] = preg_replace('/class="/', 'class="'.$size,  $params[0]['before_widget'], 1);
	return $params;
}
add_filter('dynamic_sidebar_params', 'unisco_dynamic_sidebar_params');

/**
 * Register a custom theme page.
 */
function unisco_theme_page() {
	if( function_exists('is_plugin_active') && !is_plugin_active( 'unisco-pro/unisco-pro.php' ) ) {
		add_theme_page(
			__( 'Unisco', 'unisco' ),
			'Unisco',
			'manage_options',
			'theme-setup',
			'unisco_info_page_cb'
		);
	}
}
add_action( 'admin_menu', 'unisco_theme_page' );

/**
 * Theme page callback
 */
function unisco_info_page_cb() {
	ob_start();
    ?>
    <div class="about-wrap" style="max-width:900px;">
        <h1>Unisco Lite</h1>
        <br>
        <br>
        <h3><?php esc_html_e('Important info about menu','unisco'); ?></h3>
        <p><?php esc_html_e('This theme comes with two menu locations in the header area, namely, top left and top right. You should distribute your menu items among the top left and top right menus because site logo will appear in between these two menus.','unisco'); ?></p>
        <br>
        <h3><?php esc_html_e('How to setup a slide for slider?','unisco'); ?></h3>
        <p><?php esc_html_e('Create a post or page with slider content. Do not forget to add featured image since it will be used as background image for a slide. Go to Appearance > Customizer > Front Slider. Enable slider if not already. Under Slides > Slide 1, select the post or page you want to output content from.','unisco'); ?></p>
        <br>
        <div class="feature-section two-col">
            <div class="col">
                <h3><?php esc_html_e('PRO version benefits?','unisco'); ?></h3>
                <ol>
                    <li><?php esc_html_e('One click demo setup.','unisco'); ?></li>
                    <li><?php esc_html_e('Page Builder.','unisco'); ?></li>
                    <li><?php esc_html_e('Events.','unisco'); ?></li>
                    <li><?php esc_html_e('Courses.','unisco'); ?></li>
                    <li><?php esc_html_e('Custom forms specifically made for admissions and contact.','unisco'); ?></li>
                    <li><?php esc_html_e('Testimonials.','unisco'); ?></li>
                    <li><?php esc_html_e('Customize pages with page specific options.','unisco'); ?></li>
                    <li><?php esc_html_e('Typography powered with Google fonts.','unisco'); ?></li>
                    <li><?php esc_html_e('Page layouts - Right Sidebar, Left Sidebar, Boxed and Full Width.','unisco'); ?></li>
                    <li><?php esc_html_e('Instagram feeds.','unisco'); ?></li>
                    <li><?php esc_html_e('Google Maps.','unisco'); ?></li>
                    <li><?php esc_html_e('24x7 Support','unisco'); ?></li>
                </ol>
                <br>
                <a class="button button-large button-secondary" href="https://demo.snapthemes.io/unisco-pro/?utm_source=unisco_lite_info_page">See demo</a>
                <a class="button button-large button-primary" href="https://snapthemes.io/products/unisco-education-wordpress-theme/?utm_source=unisco_lite_info_page">Get Pro</a>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
    <?php
    $output = ob_get_clean();
    echo $output;
}

/**
 * Admin notice on theme activation
 */
function unisco_info_page_notice() {
	?>
    <div class="snapthemes-notice notice notice-success is-dismissible" style="border-left-color:#37BF91;padding:.75rem 1rem;">
        <div class="two-col" style="display:block;">
            <div class="col" style="display:inline-block;margin-right:10px;">
                <img src="<?php echo esc_url( get_theme_file_uri( '/images/st-icon.png' ) ); ?>" alt="SnapThemes">
            </div>
            <div class="col" style="display:inline-block;">
                <p style="font-size:14px;margin:0;"><strong><?php esc_html_e('Thank you for installing Unisco!','unisco'); ?></strong></p>
                <p style="margin:0;">
                    <?php echo sprintf( __('Information on how to set up menu and slider is available <a href="%1$s">here</a>.','unisco'),
                        esc_url( get_admin_url(null, apply_filters('unisco_theme_page_url','themes.php?page=theme-setup') ) )
                    ); ?>
                </p>
            </div>
        </div>
    </div>
	<?php
}
global $pagenow;
if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
	add_action( 'admin_notices', 'unisco_info_page_notice' );
}