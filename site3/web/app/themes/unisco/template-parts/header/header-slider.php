<?php
/**
 * Displays slider in header
 *
 * @package WordPress
 * @subpackage unisco
 * @since 1.0
 */

?>
<div id="unisco-slider" class="slider_img">
    <div id="carousel" class="carousel slide" data-ride="carousel">
		<?php
		// slides
		$slides = json_decode( get_theme_mod( 'unisco_slides' ) );
		?>
        <ol class="carousel-indicators">
            <?php
            if( ! empty( $slides ) ) {
                foreach( $slides as $key => $slide ) { ?>
                    <li data-target="#carousel" data-slide-to="<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $key === 0 ? 'active' : '' ); ?>"></li>
                <?php }
            } ?>
        </ol>
        <div class="carousel-inner" role="listbox">
	        <?php
	        if( ! empty( $slides ) ) {
		        foreach ( $slides as $key => $slide ) {
			        $queried_post = get_post( $slide->content );
			        $image        = get_the_post_thumbnail_url( $queried_post->ID, 'full' );
			        ?>
                    <div class="carousel-item<?php echo esc_attr( $key === 0 ? ' active' : '' ); ?>">
                        <img class="d-block"
                             src="<?php echo esc_url( $image ? $image : get_template_directory_uri() . '/images/slider.jpg' ); ?>"
                             alt="<?php echo esc_attr( $slide->title ); ?>">
                        <div class="carousel-caption d-md-block">
                            <div class="slider_title">
                                <h1><?php echo esc_html( $queried_post->post_title ); ?></h1>
						        <?php echo wp_kses_post( $queried_post->post_content ); ?>
						        <?php if ( $slide->button_1_text || $slide->button_2_text ): ?>
                                    <div class="slider-btn">
								        <?php if ( $slide->button_1_url ): ?>
                                            <a href="<?php echo esc_url( $slide->button_1_url ); ?>"
                                               class="btn btn-default"><?php echo esc_html( $slide->button_1_text ); ?></a>
								        <?php endif; ?>
								        <?php if ( $slide->button_2_url ): ?>
                                            <a href="<?php echo esc_url( $slide->button_2_url ); ?>"
                                               class="btn btn-default"><?php echo esc_html( $slide->button_2_text ); ?></a>
								        <?php endif; ?>
                                    </div>
						        <?php endif; ?>
                            </div>
                        </div>
                    </div>
		        <?php }
	        } else { ?>
                <div class="carousel-item active">
                    <img class="d-block" src="<?php echo esc_url( get_template_directory_uri() . '/images/slider.jpg' ); ?>" alt="<?php esc_attr_e( 'Please select slide content', 'unisco' ); ?>">
                    <div class="carousel-caption d-md-block">
                        <div class="slider_title">
                            <h1><?php esc_html_e( 'Please select slide content', 'unisco' ); ?></h1>
                        </div>
                    </div>
                </div>
	        <?php } ?>
        </div>
        <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
            <i class="icon-arrow-left fa-slider" aria-hidden="true"></i>
            <span class="sr-only"><?php esc_html_e( 'Previous', 'unisco' ); ?></span>
        </a>
        <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
            <i class="icon-arrow-right fa-slider" aria-hidden="true"></i>
            <span class="sr-only"><?php esc_html_e( 'Next', 'unisco' ); ?></span>
        </a>
    </div>
</div>