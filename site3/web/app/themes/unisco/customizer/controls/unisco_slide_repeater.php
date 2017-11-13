<?php

if ( ! class_exists( 'WP_Customize_Control' ) )
	return NULL;

class Unisco_Slide_Repeater_Control extends WP_Customize_Control {
	/**
	 * Render the control's content.
	 * Allows the content to be overridden without having to rewrite the wrapper.
	 * @return  void
	 */
	public function render_content() {
		?>
        <script>
            var entityMap = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                '\'': '&#39;',
                '/': '&#x2F;'
            };
            function uniscoEscapeHtml(string) {
                'use strict';
                //noinspection JSUnresolvedFunction
                string = String(string).replace(new RegExp('\r?\n', 'g'), '<br />');
                string = String(string).replace(/\\/g, '&#92;');
                return String(string).replace(/[&<>"'\/]/g, function (s) {
                    return entityMap[s];
                });
            }
            function uniscoUpdateSliderValues() {
                var values = [];
                var slideOptions = jQuery('.unisco_slide_options');
                slideOptions.each( function() {
                    var slideContent = jQuery(this).find('.unisco_slide_content').val();
                    var slideButton1Text = jQuery(this).find('.unisco_slide_button_1_text').val();
                    var slideButton1Url = jQuery(this).find('.unisco_slide_button_1_url').val();
                    var slideButton2Text = jQuery(this).find('.unisco_slide_button_2_text').val();
                    var slideButton2Url = jQuery(this).find('.unisco_slide_button_2_url').val();
                    values.push({
                        'content': uniscoEscapeHtml( slideContent ),
                        'button_1_text': uniscoEscapeHtml( slideButton1Text ),
                        'button_1_url': encodeURI( slideButton1Url ),
                        'button_2_text': uniscoEscapeHtml( slideButton2Text ),
                        'button_2_url': encodeURI( slideButton2Url )
                    });
                });
                jQuery('#<?php echo esc_attr( $this->id ); ?>').val( JSON.stringify(values) ).trigger('change');
            }
            (function ($) {
                $(document).ready(function () {
                    $(document).on('keyup', '.unisco_slide_button_1_text,.unisco_slide_button_1_url,.unisco_slide_button_2_text,.unisco_slide_button_2_url', function(){
                        uniscoUpdateSliderValues();
                    });
                    $(document).on('change', '.unisco_slide_content', function(){
                        uniscoUpdateSliderValues();
                    });
                    $('#unisco_add_slide').on('click', function(e){
                        e.preventDefault();
                        var count = $('.unisco_slides_control').find('.unisco_slide_options').length;
                        var slide = $(this).prev('.unisco_slide_options').clone();
                        slide.find('.unisco_slide_count').text(count+1);
                        slide.find('.unisco_remove_slide').removeClass('hidden');
                        $(this).before(slide);
                        uniscoUpdateSliderValues();
                    });
                    $(document).on('click', '.unisco_remove_slide', function(e){
                        e.preventDefault();
                        $(this).parent().parent().remove();
                        uniscoUpdateSliderValues();
                    });
                    $(document).on('click', '.unisco_slide_label', function(e){
                        var $this = $(this);
                        $this.find('.dashicons').toggleClass('dashicons-arrow-up').toggleClass('dashicons-arrow-down');
                        $this.next('.unisco_slide_options_wrap').slideToggle('fast', function(){
                            $this.toggleClass('unisco_slide_fold');
                        });
                    });
                    $('.unisco_slides_control').sortable({
                        update: function () {
                            uniscoUpdateSliderValues();
                        }
                    });
                });
            })(jQuery);
        </script>
        <style>
            .unisco_slide_options {
                border: 1px solid #d8d8d8;
                padding: 10px 10px 0;
                margin: 0 0 10px;
            }
            .unisco_slide_options_wrap {
                margin-top: 10px;
            }
            .unisco_slide_label {
                color: #8f959a;
                margin-top: 0;
                margin-bottom: 0;
                border-bottom: 1px solid #d8d8d8;
                padding-bottom: 10px;
                cursor: pointer;
            }
            .unisco_slide_fold {
                border-bottom: 0;
                padding-bottom: 10px;
            }
            .unisco_remove_slide {
                margin-bottom: 10px!important;
            }
        </style>
		<div class="unisco_slider_repeater">
            <input type="hidden" id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo esc_html( $this->link() ); ?>>
            <label for="">
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            </label>
            <div class="unisco_slides_control">
                <?php
                $defaults = json_decode( $this->setting->default );
                $value = json_decode( $this->value() );
                if( is_array( $value ) ) {
	                foreach ( $value as $key => $slide ) {
	                    $default_slide = $defaults[$key];
		                $key = $key + 1;
		                ?>
                        <div class="unisco_slide_options">
                            <h3 class="unisco_slide_label">
				                <?php esc_html_e( 'Slide', 'unisco' ); ?>
                                <span class="unisco_slide_count"><?php echo esc_html( $key ); ?></span>
                                <span class="dashicons dashicons-arrow-up" style="float:right;"></span>
                            </h3>
                            <div class="unisco_slide_options_wrap">
                                <label class="customize-control customize-control-text">
                                    <span class="customize-control-title"><?php esc_html_e( 'Post/Page for slide content', 'unisco' ); ?></span>
                                    <select class="unisco_slide_content" id="">
                                        <option value=""><?php esc_html_e('None', 'unisco'); ?></option>
	                                    <?php
	                                    $args = array( 'post_type' => array('post','page'), 'posts_per_page' => -1, 'nopaging' => true );
	                                    $query = new WP_Query($args);
	                                    foreach( $query->posts as $post ): ?>
                                            <option value="<?php echo esc_attr($post->ID); ?>" <?php selected( $post->ID, isset( $slide->content ) ? $slide->content : '' ); ?>><?php echo esc_html($post->post_title); ?></option>
	                                    <?php endforeach; ?>
                                    </select>
                                </label>
                                <label class="customize-control customize-control-text">
                                    <span class="customize-control-title"><?php esc_html_e( 'Button 1', 'unisco' ); ?></span>
                                    <input type="text" class="unisco_slide_button_1_text"
                                           placeholder="<?php echo esc_attr( 'Text', 'unisco' ); ?>"
                                           value="<?php echo esc_attr( isset( $slide->button_1_text ) ? $slide->button_1_text : '' ); ?>">
                                    <input type="url" class="unisco_slide_button_1_url"
                                           placeholder="<?php echo esc_attr( 'Url', 'unisco' ); ?>"
                                           value="<?php echo esc_attr( isset( $slide->button_1_url ) ? $slide->button_1_url : '' ); ?>">
                                </label>
                                <label class="customize-control customize-control-text">
                                    <span class="customize-control-title"><?php esc_html_e( 'Button 2', 'unisco' ); ?></span>
                                    <input type="text" class="unisco_slide_button_2_text"
                                           placeholder="<?php esc_attr_e( 'Text', 'unisco' ); ?>"
                                           value="<?php echo esc_attr( isset( $slide->button_2_text ) ? $slide->button_2_text : '' ); ?>">
                                    <input type="url" class="unisco_slide_button_2_url"
                                           placeholder="<?php echo esc_attr( 'Url', 'unisco' ); ?>"
                                           value="<?php echo esc_attr( isset( $slide->button_2_url ) ? $slide->button_2_url : '' ); ?>">
                                </label>
                                <button type="button"
                                        class="button unisco_remove_slide <?php echo esc_attr( $key == 1 ? 'hidden' : '' ); ?>"><?php esc_html_e( 'Remove', 'unisco' ); ?></button>
                            </div>
                            <div class="clear"></div>
                        </div>
		                <?php
	                }
                }
                ?>
                <button type="button" id="unisco_add_slide" class="button button-primary unisco_add_slide"><?php esc_html_e('Add Slide','unisco'); ?></button>
            </div>
        </div>

		<?php
	}

}