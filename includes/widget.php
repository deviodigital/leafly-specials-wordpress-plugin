<?php
/**
 * Widget
 *
 * @package     LeaflySpecials\Widget
 * @since       1.0.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

/*
 * SimpleCache v1.4.1
 *
 * By Gilbert Pellegrom
 * http://dev7studios.com
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */
class LSWidgetCache {
	
	// Path to cache folder (with trailing /)
	public $cache_path = 'wp-content/plugins/leafly-specials/cache/';
	// Length of time to cache a file (in seconds)
	public $cache_time = 3600;
	// Cache file extension
	public $cache_extension = '.cache';

	// This is just a functionality wrapper function
	public function get_data($widget, $url)
	{
		if($data = $this->get_cache($widget)){
			return $data;
		} else {
			$data = $this->do_curl($url);
			$this->set_cache($widget, $data);
			return $data;
		}
	}

	public function set_cache($widget, $data)
	{
		file_put_contents($this->cache_path . $this->safe_filename($widget) . $this->cache_extension, $data);
	}

	public function get_cache($widget)
	{
		if($this->is_cached($widget)){
			$filename = $this->cache_path . $this->safe_filename($widget) . $this->cache_extension;
			return file_get_contents($filename);
		}

		return false;
	}

	public function is_cached($widget)
	{
		$filename = $this->cache_path . $this->safe_filename($widget) . $this->cache_extension;

		if(file_exists($filename) && (filemtime($filename) + $this->cache_time >= time())) return true;

		return false;
	}

	//Helper function for retrieving data from url
	public function do_curl($url)
	{
		if(function_exists("curl_init")){
			$appid = get_option("app_id");
			$appkey = get_option("app_key");

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('app_id: '. $appid .'','app_key: '. $appkey .''));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		} else {
			return file_get_contents($url);
		}
	}

	//Helper function to validate filenames
	private function safe_filename($filename)
	{
		return preg_replace('/[^0-9a-z\.\_\-]/i','', strtolower($filename));
	}
}

/**
 * Leafly Specials Widget
 *
 * @since       1.0.0
 */
class leaflyspecials_widget extends WP_Widget {

    /**
     * Constructor
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function leaflyspecials_widget() {
        parent::WP_Widget(
            false,
            __( 'Leafly Specials', 'leafly-specials' ),
            array(
                'description'  => __( 'Display your recent dispensary specials from leafly.', 'leafly-specials' )
            )
        );
    }

    /**
     * Widget definition
     *
     * @access      public
     * @since       1.0.0
     * @see         WP_Widget::widget
     * @param       array $args Arguments to pass to the widget
     * @param       array $instance A given widget instance
     * @return      void
     */
    public function widget( $args, $instance ) {
        if( ! isset( $args['id'] ) ) {
            $args['id'] = 'leafly_specials_widget';
        }

        $title = apply_filters( 'widget_title', $instance['title'], $instance, $args['id'] );

        echo $args['before_widget'];

        if( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        do_action( 'leafly_specials_before_widget' );

        if( $instance['slug'] ) {
				$cache = new LSWidgetCache();
				$cache->cache_path = 'wp-content/plugins/leafly-specials/cache/';
				$cache->cache_time = 3600;

				if($data = $cache->get_cache('widget')){
					$body = json_decode($data,true);
				} else {
					$data = $cache->do_curl( 'http://data.leafly.com/locations/'. $instance['slug'] .'/specials?skip=0&take=100' );
					$cache->set_cache('widget', $data);
					$body = json_decode($data,true);
				}
				
				if ($data == "Authentication parameters missing") {
					echo $data;
				} else {
				$i = 1;

				foreach( $body as $special) {
					echo "<div class='leafly-specials-plugin-meta'>";

					if('on' == $instance['specialtitle'] ) {
						// Display special title
						echo "<span class='leafly-specials-plugin-meta-item'><h3>". $special['title'] ."</h3></span>";
					}
					
					if('on' == $instance['specialdetails'] ) {
						// Display special details
						echo "<p><span class='leafly-specials-plugin-meta-item'>". $special['details'] ."</span></p>";
					}
					
					if('on' == $instance['specialfineprint'] ) {
						// Display special fineprint
						echo "<p><span class='leafly-specials-plugin-meta-item fineprint'><em>". $special['finePrint'] ."</em></span></p>";
					}
					
					if('on' == $instance['specialpermalink'] ) {
						// Display special link
						echo "<p><span class='leafly-specials-plugin-meta-item'><a href=". $special['permalink'] ." target='_blank'>View Printable Coupon</a></p>";
					}

					echo "</div>";
					
					// Check special count
					if ($i++ == $instance['limit']) break;

				}

				if('on' == $instance['viewall'] ) {
					// Display if user would shop again if they say YES
					echo "<p><span class='leafly-specials-plugin-meta-item'><a class='leafly-specials-plugin-viewall' href='https://www.leafly.com/dispensary-info/". $instance['slug'] ."/specials' target='_blank'>View all specials &rarr;</a></span></p>";
				}

            }
        } else {
            _e( 'No location has been specified!', 'leafly-specials' );
        }

        do_action( 'leafly_specials_after_widget' );
        
        echo $args['after_widget'];
    }


    /**
     * Update widget options
     *
     * @access      public
     * @since       1.0.0
     * @see         WP_Widget::update
     * @param       array $new_instance The updated options
     * @param       array $old_instance The old options
     * @return      array $instance The updated instance options
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']      		= strip_tags( $new_instance['title'] );
        $instance['slug']   			= strip_tags( $new_instance['slug'] );
        $instance['limit']   			= strip_tags( $new_instance['limit'] );
        $instance['specialtitle']		= $new_instance['specialtitle'];
        $instance['specialdetails']		= $new_instance['specialdetails'];
        $instance['specialpermalink']	= $new_instance['specialpermalink'];
        $instance['specialfineprint']	= $new_instance['specialfineprint'];
        $instance['viewall']			= $new_instance['viewall'];

        return $instance;
    }


    /**
     * Display widget form on dashboard
     *
     * @access      public
     * @since       1.0.0
     * @see         WP_Widget::form
     * @param       array $instance A given widget instance
     * @return      void
     */
    public function form( $instance ) {
        $defaults = array(
            'title'  		    => 'Leafly Specials',
            'slug'  			=> '',
            'limit'  			=> '5',
            'specialtitle' 		=> '',
            'specialdetails' 	=> '',
            'specialpermalink'	=> '',
            'specialfineprint'	=> '',
			'viewall'			=> ''
        );

        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'leafly-specials' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'slug' ) ); ?>"><?php _e( 'Location slug (ex: denver-relief):', 'leafly-specials' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'slug' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'slug' ) ); ?>" type="text" value="<?php echo $instance['slug']; ?>" />
        </p>
		
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php _e( 'Amount of specials to show:', 'leafly-specials' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" type="number" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" min="1" max="999" value="<?php echo $instance['limit']; ?>" />
        </p>
		
	    <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['specialtitle'], 'on'); ?> id="<?php echo $this->get_field_id('specialtitle'); ?>" name="<?php echo $this->get_field_name('specialtitle'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'specialtitle' ) ); ?>"><?php _e( 'Display special title?', 'leafly-specials' ); ?></label>
        </p>

	    <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['specialdetails'], 'on'); ?> id="<?php echo $this->get_field_id('specialdetails'); ?>" name="<?php echo $this->get_field_name('specialdetails'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'specialdetails' ) ); ?>"><?php _e( 'Display special details?', 'leafly-specials' ); ?></label>
        </p>

	    <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['specialfineprint'], 'on'); ?> id="<?php echo $this->get_field_id('specialfineprint'); ?>" name="<?php echo $this->get_field_name('specialfineprint'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'specialfineprint' ) ); ?>"><?php _e( 'Display special fine print?', 'leafly-specials' ); ?></label>
        </p>

	    <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['specialpermalink'], 'on'); ?> id="<?php echo $this->get_field_id('specialpermalink'); ?>" name="<?php echo $this->get_field_name('specialpermalink'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'specialpermalink' ) ); ?>"><?php _e( 'Display link to special URL?', 'leafly-specials' ); ?></label>
        </p>

	    <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['viewall'], 'on'); ?> id="<?php echo $this->get_field_id('viewall'); ?>" name="<?php echo $this->get_field_name('viewall'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'viewall' ) ); ?>"><?php _e( 'Display link to all specials on Leafly?', 'leafly-specials' ); ?></label>
        </p>

		<?php
    }
}


/**
 * Register the new widget
 *
 * @since       1.0.0
 * @return      void
 */
function leaflyspecials_register_widget() {
    register_widget( 'leaflyspecials_widget' );
}
add_action( 'widgets_init', 'leaflyspecials_register_widget' );
