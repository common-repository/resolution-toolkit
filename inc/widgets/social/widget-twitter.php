<?php

add_action( 'widgets_init', function() { register_widget( 'Resolution_Toolkit_Twitter'); } );

class Resolution_Toolkit_Twitter extends WP_Widget {

    public $kpb_group = 'Social';

    function __construct() {
        $widget_ops = array('classname' => 'kp-twitter-widget', 'description' => esc_html__('Show latest tweet',  'resolution-toolkit'));
        $control_ops = array('width' => 'auto', 'height' => 'auto');
        parent::__construct('Resolution_Toolkit_Twitter', esc_html__('(Resolution) Tweet',  'resolution-toolkit'), $widget_ops, $control_ops);
    }

    public function widget( $args, $instance ) {
        
        ob_start();
        extract( $args );
        extract( $instance );
        echo $before_widget;

        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        
        if($title)
            echo $before_title . $title .$after_title;  

        require_once( plugin_dir_path( __FILE__ ) . 'api/class-twitter-api-exchange.php');

        $username = $instance['username'];
        $number   = $instance['number'];        
        $settings = array(
            'consumer_key'              => trim( $instance['consumer_key'] ),
            'consumer_secret'           => trim( $instance['consumer_secret'] ),
            'oauth_access_token'        => trim( $instance['oauth_access_token'] ),
            'oauth_access_token_secret' => trim( $instance['oauth_access_token_secret'] ),
        );

        $url           = "https://api.twitter.com/1.1/statuses/user_timeline.json";
        $requestMethod = "GET";
        $getfield      = "?screen_name={$username}&count={$number}";
        $twitter       = new TwitterAPIExchange($settings);
        $data          = json_decode( $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(), TRUE);

        if(!empty($data) && is_array($data)){
            if (isset($data["error"]) || 
                !empty($data["error"]) || 
                isset($data["errors"]) || 
                !empty($data["errors"])
                ) {
                esc_html_e( 'Sorry, there was a problem when load.', 'resolution-toolkit');
        } else {
            ?>
            <div class="tweets">

                <ul class="tweetList">
                    <?php
                    foreach ($data as $items){     
                        preg_match('!https?://[\S]+!', $items['text'], $matches);

                        $url = '';

                        if (isset($matches) && !empty($matches))
                            $url = $matches[0];                    

                        $pattern = '~http://[^\s]*~i';

                        $title = preg_replace($pattern, '', $items['text']);                
                        ?>
                        <li class="clearfix item-fa fa-twitter">
                            <div class="twitter-item">
                                <p>
                                    <?php echo esc_attr($title); ?>
                                    <?php if (!empty($url)) : ?>
                                    <a href="<?php echo esc_url($url); ?>"><?php echo esc_attr($url); ?></a>
                                <?php endif; ?>
                                <span class="tweet-time">
                                    <?php
                                    $date = date_create($items['created_at']);
                                    if (version_compare(PHP_VERSION, '5.3') >= 0) {
                                        $created_at = $date->getTimestamp();
                                        echo resolution_lite_get_human_time_diff($created_at);
                                    } else {
                                        echo date_format($date, "Y/m/d H:i");
                                    }
                                    ?>      
                                </span>
                            </p>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        <?php
            }

        }
        echo $after_widget;

        $content = ob_get_clean();
        echo $content;
    }

    function update($new_instance, $old_instance) {

        $instance = $old_instance;
        $instance['title']                      = strip_tags($new_instance['title']);
        $instance['username']                   = strip_tags($new_instance['username']);
        $instance['number']                     = $new_instance['number'];
        $instance['consumer_key']               = strip_tags($new_instance['consumer_key']);
        $instance['consumer_secret']            = strip_tags($new_instance['consumer_secret']);
        $instance['oauth_access_token']         = strip_tags($new_instance['oauth_access_token']);
        $instance['oauth_access_token_secret']  = strip_tags($new_instance['oauth_access_token_secret']);
        return $instance;
    }

    function form($instance) {

        $instance = wp_parse_args((array) $instance, array(
            'title'                     => '', 
            'username'                  => '',
            'number'                    =>2,
            'consumer_key'              => '',
            'consumer_secret'           => '',
            'oauth_access_token'        => '',
            'oauth_access_token_secret' => ''
            ));
        $title                      = strip_tags($instance['title']);
        $username                   = strip_tags($instance['username']);
        $number_of_tweets           = $instance['number'];
        $consumer_key               = strip_tags($instance['consumer_key']);
        $consumer_secret            = strip_tags($instance['consumer_secret']);
        $oauth_access_token         = strip_tags($instance['oauth_access_token']);
        $oauth_access_token_secret  = strip_tags($instance['oauth_access_token_secret']);
        ?>
        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('title')); ?>"><?php echo esc_html__('Title:',  'resolution-toolkit'); ?></label>
            <input class="widefat" id="<?php echo wp_kses_post($this->get_field_id('title')); ?>" name="<?php echo wp_kses_post($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />

        </p>

        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('username')); ?>"><?php echo esc_html__('Username:',  'resolution-toolkit'); ?></label>
            <input class="widefat" id="<?php echo wp_kses_post($this->get_field_id('username')); ?>" name="<?php echo wp_kses_post($this->get_field_name('username')); ?>" type="text" value="<?php echo wp_kses_post($username); ?>" />
        </p>

        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('number')); ?>"><?php echo esc_html__('Number of tweets', 'resolution-toolkit'); ?></label>
            <input id="<?php echo wp_kses_post($this->get_field_id('number')); ?>" name="<?php echo wp_kses_post($this->get_field_name('number')); ?>" type="number" value="<?php echo wp_kses_post($number_of_tweets); ?>" />

        </p>

        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('consumer_key')); ?>"><?php echo esc_html__('Consumer key',  'resolution-toolkit'); ?></label>
            <input class="widefat" id="<?php echo wp_kses_post($this->get_field_id('consumer_key')); ?>" name="<?php echo wp_kses_post($this->get_field_name('consumer_key')); ?>" type="text" value="<?php echo wp_kses_post($consumer_key); ?>" />
        </p>

        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('consumer_secret')); ?>"><?php echo esc_html__('Consumer secret',  'resolution-toolkit'); ?></label>
            <input class="widefat" id="<?php echo wp_kses_post($this->get_field_id('consumer_secret')); ?>" name="<?php echo wp_kses_post($this->get_field_name('consumer_secret')); ?>" type="text" value="<?php echo wp_kses_post($consumer_secret); ?>" />
        </p>

        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('oauth_access_token')); ?>"><?php echo esc_html__('Oauth access token',  'resolution-toolkit'); ?></label>
            <input class="widefat" id="<?php echo wp_kses_post($this->get_field_id('oauth_access_token')); ?>" name="<?php echo wp_kses_post($this->get_field_name('oauth_access_token')); ?>" type="text" value="<?php echo wp_kses_post($oauth_access_token); ?>" />
        </p>

        <p>
            <label for="<?php echo wp_kses_post($this->get_field_id('oauth_access_token_secret')); ?>"><?php echo esc_html__('Oauth access token secret',  'resolution-toolkit'); ?></label>
            <input class="widefat" id="<?php echo wp_kses_post($this->get_field_id('oauth_access_token_secret')); ?>" name="<?php echo wp_kses_post($this->get_field_name('oauth_access_token_secret')); ?>" type="text" value="<?php echo wp_kses_post($oauth_access_token_secret); ?>" />
        </p>

        <?php
    }

}