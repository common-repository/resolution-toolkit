<?php

add_action( 'widgets_init', function(){
    register_widget( 'Resolution_Lite_Flickr' );
});

class Resolution_Lite_Flickr extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'kp-flickr-widget', 'description' => esc_html__('Display images from Flickr',  'resolution-toolkit'));
        $control_ops = array('width' => 'auto', 'height' => 'auto');
        parent::__construct('kp-flickr-widget', esc_html__('(Resolution) Flickr',  'resolution-toolkit'), $widget_ops, $control_ops);
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $id = strip_tags($instance['id']);
        $number_images = $instance['number_of_images'];
        
        echo wp_kses_post($before_widget);
        if (!empty($title)) {
            echo wp_kses_post($before_title) . $title . $after_title;
        }
        ?>
        <div class="flickr-wrap clearfix" id="flickr-feed-1" data-limit = "<?php echo wp_kses_post($number_images); ?>" data-flickr-id = "<?php echo wp_kses_post($id); ?>">                    
            <ul class="kopa-flickr-widget clearfix"></ul>
        </div>
        
        <?php
        echo wp_kses_post($after_widget);
    }

    function update($new_instance, $old_instance) {

        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['id'] = strip_tags($new_instance['id']);
        $instance['number_of_images'] = $new_instance['number_of_images'];
        return $instance;
    }

    function form($instance) {

        $instance = wp_parse_args((array) $instance, array(
            'title' => esc_html__('Flickr',  'resolution-toolkit'),
            'id' => '',
            'number_of_images'=>6,
            ));
        $title = strip_tags($instance['title']);
        $id = strip_tags($instance['id']);
        $number_of_images =  $instance['number_of_images'];
        ?>
        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('title')); ?>"><?php echo esc_html__('Title:', 'resolution-toolkit'); ?></label>
            <input class="widefat" id="<?php echo wp_kses_post($this->get_field_id('title')); ?>" name="<?php echo wp_kses_post($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />

        </p>
        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('id')); ?>"><?php echo esc_html__('ID:', 'resolution-toolkit'); ?></label>
            <input class="widefat" id="<?php echo wp_kses_post($this->get_field_id('id')); ?>" name="<?php echo wp_kses_post($this->get_field_name('id')); ?>" type="text" value="<?php echo esc_attr($id); ?>" />

        </p>
        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('number_of_images')); ?>"><?php echo esc_html__('Number of images', 'resolution-toolkit'); ?></label>
            <input class="widefat" id="<?php echo wp_kses_post($this->get_field_id('number_of_images')); ?>" name="<?php echo wp_kses_post($this->get_field_name('number_of_images')); ?>" type="number" value="<?php echo wp_kses_post($number_of_images); ?>" />

        </p>
        <?php
    }

}
