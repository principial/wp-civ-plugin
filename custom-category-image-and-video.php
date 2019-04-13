<?php
/**
 * Plugin Name: Custom category image and video
 * Plugin URI: https://pilipen-ko.pro/custom-category-image-and-video
 * Description: Plugin for adding custom image and video to category.
 * Version: 1.0.0
 * Author: Serhii Pylypenko
 * Author URI: https://pilipen-ko.pro
 * Author email: pilja.ne@gmail.com
 *
 * todo: add output video with YouTube API or Vimeo player SDK
 */

if ( ! class_exists( 'CUSTOM_CATEGORY_IMAGE_AND_VIDEO' ) ) {

    class CUSTOM_CATEGORY_IMAGE_AND_VIDEO {

        public function __construct() {
            //
        }

        /**
         * Initialize the class and start calling our hooks and filters
         * @since 1.0.0
        */
        public function init() {
            add_action( 'category_add_form_fields', array ( $this, 'add_category_image' ), 10, 2 );
            add_action( 'category_add_form_fields', array ( $this, 'add_category_video' ), 15, 2 );
            add_action( 'created_category', array ( $this, 'save_category_image' ), 10, 2 );
            add_action( 'created_category', array ( $this, 'save_category_video' ), 10, 2 );
            add_action( 'category_edit_form_fields', array ( $this, 'update_category_image' ), 10, 2 );
            add_action( 'category_edit_form_fields', array ( $this, 'update_category_video' ), 10, 2 );
            add_action( 'edited_category', array ( $this, 'updated_category_image' ), 10, 2 );
            add_action( 'edited_category', array ( $this, 'updated_category_video' ), 10, 2 );
            add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
            add_action( 'admin_footer', array( $this, 'add_assets' ) );
            add_action( 'wp_enqueue_scripts',  array( $this, 'add_public_assets') );
            add_filter( 'category_description', array ( $this, 'add_image_video_to_description'), 10, 2 );
        }

        /**
         * Load media WP function
         */
        public function load_media() {
            wp_enqueue_media();
        }

        /**
         * Add scripts and styles
         * @since 1.0.0
         */
        public function add_assets() {
            wp_enqueue_script('custom-img-vid-script', plugin_dir_url(__FILE__ ) . 'assets/js/script.js', array() , '1.0.0');
            wp_enqueue_style('custom-img-vid-style', plugin_dir_url(__FILE__ ) . 'assets/css/style.css', array() , '1.0.0');
        }

        public function add_public_assets() {
            wp_enqueue_script('custom-img-vid-script', plugin_dir_url(__FILE__ ) . 'public/js/script.js', array() , '1.0.0');
            wp_enqueue_style('custom-img-vid-style', plugin_dir_url(__FILE__ ) . 'public/css/style.css', array() , '1.0.0');
        }

        /**
         * Add a form field in the new category page
         * @since 1.0.0
         * @param $taxonomy
         */
        public function add_category_image ( $taxonomy ) { ?>
            <div class="form-field term-image-wrap">
                <label for="category-image-id"><?php _e('Image', 'hero-theme'); ?></label>
                <input type="hidden" id="category-image-id" name="category-image-id" class="custom_media_url" value="">
                <div id="category-image-wrapper"></div>
                <p>
                    <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', 'hero-theme' ); ?>" />
                    <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', 'hero-theme' ); ?>" />
                </p>
            </div>
            <?php
        }

        /**
         * Save the form field
         * @since 1.0.0
         * @param $term_id
         * @param $tt_id
         */
        public function save_category_image ( $term_id, $tt_id ) {
            if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
                $image = $_POST['category-image-id'];
                add_term_meta( $term_id, 'category-image-id', $image, true );
            }
        }

        /**
         * Edit the form field
         * @since 1.0.0
         * @param $term
         * @param $taxonomy
         */
        public function update_category_image ( $term, $taxonomy ) { ?>
            <tr class="form-field term-image-wrap">
                <th scope="row">
                    <label for="category-image-id"><?php _e( 'Image', 'hero-theme' ); ?></label>
                </th>
                <td>
                    <?php $image_id = get_term_meta ( $term -> term_id, 'category-image-id', true ); ?>
                    <input type="hidden" id="category-image-id" name="category-image-id" value="<?php echo $image_id; ?>">
                    <div id="category-image-wrapper">
                        <?php if ( $image_id ) { ?>
                            <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
                        <?php } ?>
                    </div>
                    <p>
                        <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', 'hero-theme' ); ?>" />
                        <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', 'hero-theme' ); ?>" />
                    </p>
                </td>
            </tr>
            <?php
        }

        /**
         * Update the form field value
         * @since 1.0.0
         * @param $term_id
         * @param $tt_id
         */
        public function updated_category_image ( $term_id, $tt_id ) {
            if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
                $image = $_POST['category-image-id'];
                update_term_meta ( $term_id, 'category-image-id', $image );
            } else {
                update_term_meta ( $term_id, 'category-image-id', '' );
            }
        }

        /**
         * Add a form field in the new category page
         * @since 1.0.0
         * @param $taxonomy
         */
        public function add_category_video ( $taxonomy ) { ?>
            <div class="form-field term-video-wrap">
                <label for="category-video-link"><?php _e('Video link', 'hero-theme'); ?></label>
                <input type="hidden" id="category-video-thumb" name="category-video-thumb" class="custom_media_url" value="">
                <div class="term-video-input">
                    <input type="text" id="category-video-link" name="category-video-link" class="custom_media_url" value="">
                </div>
                <p class="description">Please, insert embed link like https://vimeo.com/33110953 or https://youtu.be/RK1K2bCg4J8</p>
                <div id="category-video-wrapper"></div>
                <p>
                    <input type="button" class="button button-secondary ct_tax_video_button" id="ct_tax_video_button" name="ct_tax_video_button" value="<?php _e( 'Add Video', 'hero-theme' ); ?>" />
                    <input type="button" class="button button-secondary ct_tax_video_remove" id="ct_tax_video_remove" name="ct_tax_video_remove" value="<?php _e( 'Remove Video', 'hero-theme' ); ?>" />
                </p>
            </div>
            <?php
        }

        /**
         * Save the form field
         * @since 1.0.0
         * @param $term_id
         * @param $tt_id
         */
        public function save_category_video ( $term_id, $tt_id ) {
            if( isset( $_POST['category-video-link'] ) && '' !== $_POST['category-video-link'] ){
                $video = $_POST['category-video-link'];
                add_term_meta( $term_id, 'category-video-link', $video, true );
            }
            if( isset( $_POST['category-video-thumb'] ) && '' !== $_POST['category-video-thumb'] ){
                $image = $_POST['category-video-thumb'];
                add_term_meta( $term_id, 'category-video-thumb', $image, true );
            }
        }

        /**
         * Edit the form field
         * @since 1.0.0
         * @param $term
         * @param $taxonomy
         */
        public function update_category_video ( $term, $taxonomy ) { ?>
            <tr class="form-field term-video-wrap">
                <th scope="row">
                    <label for="category-video-link"><?php _e('Video link', 'hero-theme'); ?></label>
                </th>
                <td>
                    <?php $thumb = get_term_meta ( $term -> term_id, 'category-video-thumb', true ); ?>
                    <?php $video = get_term_meta ( $term -> term_id, 'category-video-link', true ); ?>
                    <input type="hidden" id="category-video-thumb" name="category-video-thumb" class="custom_media_url" value="<?php echo $thumb; ?>">
                    <div class="term-video-input">
                        <input type="text" id="category-video-link" name="category-video-link" class="custom_media_url" value="<?php echo $video; ?>">
                    </div>
                    <p class="description">Please, insert embed link like https://vimeo.com/33110953 or https://youtu.be/RK1K2bCg4J8</p>
                    <div id="category-video-wrapper">
                        <?php if ( $thumb ) { ?>
                            <?php echo '<img class="custom_media_image" src="'.$thumb.'" style="margin:0;padding:0;max-height:100px;float:none;" />'; ?>
                        <?php } ?>
                    </div>
                    <p>
                        <input type="button" class="button button-secondary ct_tax_video_button" id="ct_tax_video_button" name="ct_tax_video_button" value="<?php _e( 'Add Video', 'hero-theme' ); ?>" />
                        <input type="button" class="button button-secondary ct_tax_video_remove" id="ct_tax_video_remove" name="ct_tax_video_remove" value="<?php _e( 'Remove Video', 'hero-theme' ); ?>" />
                    </p>
                </td>
            </tr>
            <?php
        }

        /**
         * Update the form field value
         * @since 1.0.0
         * @param $term_id
         * @param $tt_id
         */
        public function updated_category_video ( $term_id, $tt_id ) {
            if( isset( $_POST['category-video-link'] ) && '' !== $_POST['category-video-link'] ){
                $video = $_POST['category-video-link'];
                update_term_meta ( $term_id, 'category-video-link', $video );
            } else {
                update_term_meta ( $term_id, 'category-video-link', '' );
            }
             if( isset( $_POST['category-video-thumb'] ) && '' !== $_POST['category-video-thumb'] ){
                $video = $_POST['category-video-thumb'];
                update_term_meta ( $term_id, 'category-video-thumb', $video );
            } else {
                update_term_meta ( $term_id, 'category-video-thumb', '' );
            }
        }

        /**
         * Display image and video frame on the category page archive before description
         * @since 1.0.0
         * @param $desc
         * @param $cat_id
         * @return mixed
         */
        public function add_image_video_to_description($desc, $cat_id) {
            // Get the current category ID, e.g. if we're on a category archive page
            $category = get_category( get_query_var( 'cat' ) );
            $cat_id = $category->cat_ID;
            // Get the image ID for the category
            $image_id = get_term_meta ( $cat_id, 'category-image-id', true );
            // Get the video thumbnail for the category
            $video_thumb = get_term_meta ( $cat_id, 'category-video-thumb', true );
            // Get the video link for the category
            $video_link = get_term_meta ( $cat_id, 'category-video-link', true );
            //Prepare link depending on the service
            if (strpos( $video_link, 'youtu' ) !== false) {
                $video_link = 'https://www.youtube.com/embed/'.substr($video_link, strrpos($video_link, '/') + 1);
            } elseif (strpos( $video_link, 'vimeo' ) !== false) {
                $video_link = 'https://player.vimeo.com/video/'.substr($video_link, strrpos($video_link, '/') + 1);
            }
            $html = '';
            // Echo the image
            if (!empty($image_id)) {
                $html .= '<div class="category-image">'.wp_get_attachment_image($image_id, 'large').'</div>';
            }
            // Echo the video frame
            if (!empty($video_link)) {
                $html .= '<div class="video-frame-wrap"><iframe id="category-description" class="video-iframe" src="' . $video_link . '" width="640" height="360" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
            }
            //We can use thumbnail to output video with YouTube API or Vimeo player SDK
            //Now it use only for admin panel
            if (0 && !empty($video_thumb)) {
                $html .= '<img class="custom_media_image" src="' . $video_thumb . '" style="margin:0;padding:0;max-height:100px;float:none;" />';
            }
            return $html.$desc;
        }

    }

    $CUSTOM_CATEGORY_IMAGE_AND_VIDEO = new CUSTOM_CATEGORY_IMAGE_AND_VIDEO();
    $CUSTOM_CATEGORY_IMAGE_AND_VIDEO -> init();

}