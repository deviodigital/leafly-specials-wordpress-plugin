<?php
/**
 * Shortcodes
 *
 * @package     LeaflySpecials\Shortcodes
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
class LSShortcodeCache {
	
	// Path to cache folder (with trailing /)
	public $cache_path = 'wp-content/plugins/leafly-specials/cache/';
	// Length of time to cache a file (in seconds)
	public $cache_time = 3600;
	// Cache file extension
	public $cache_extension = '.cache';

	// This is just a functionality wrapper function
	public function get_data($shortcode, $url)
	{
		if($data = $this->get_cache($shortcode)){
			return $data;
		} else {
			$data = $this->do_curl($url);
			$this->set_cache($shortcode, $data);
			return $data;
		}
	}

	public function set_cache($shortcode, $data)
	{
		file_put_contents($this->cache_path . $this->safe_filename($shortcode) . $this->cache_extension, $data);
	}

	public function get_cache($shortcode)
	{
		if($this->is_cached($shortcode)){
			$filename = $this->cache_path . $this->safe_filename($shortcode) . $this->cache_extension;
			return file_get_contents($filename);
		}

		return false;
	}

	public function is_cached($shortcode)
	{
		$filename = $this->cache_path . $this->safe_filename($shortcode) . $this->cache_extension;

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
 * LeaflySpecials Shortcode
 *
 * @since       1.0.0
 * @param       array $atts Shortcode attributes
 * @param       string $content
 * @return      string $return The LeaflySpecials
 */
 
function leafly_specials_shortcode($atts){
	
	extract(shortcode_atts(array(
		'slug' => '',
		'limit' => '5',
		'title' => 'yes',
		'details' => 'yes',
		'permalink' => 'yes',
		'fineprint' => 'yes',
		'viewall' => 'yes',
	), $atts));
	
	ob_start();

        if( $slug !== '' ) {
				$cache = new LSShortcodeCache();
				$cache->cache_path = 'wp-content/plugins/leafly-specials/cache/';
				$cache->cache_time = 3600;

				if($data = $cache->get_cache('shortcode')){
					$body = json_decode($data,true);
				} else {
					$data = $cache->do_curl( 'http://data.leafly.com/locations/'. $slug .'/specials?skip=0&take=100' );
					$cache->set_cache('shortcode', $data);
					$body = json_decode($data,true);
				}
				
				if ($data == "Authentication parameters missing") {
					echo $data;
				} else {
				$i = 1;
				foreach( $body as $special) {
					echo "<div class='leafly-specials-plugin-meta'>";

					if('yes' == $title ) {
						// Display special title
						echo "<span class='leafly-specials-plugin-meta-item'><h3>". $special['title'] ."</h3></span>";
					}
					
					if('yes' == $details ) {
						// Display special details
						echo "<p><span class='leafly-specials-plugin-meta-item'>". $special['details'] ."</span></p>";
					}
					
					if('yes' == $fineprint ) {
						// Display special fineprint
						echo "<p><span class='leafly-specials-plugin-meta-item fineprint'><em>". $special['finePrint'] ."</em></span></p>";
					}
					
					if('yes' == $permalink ) {
						// Display special link
						echo "<p><span class='leafly-specials-plugin-meta-item'><a href=". $special['permalink'] ." target='_blank'>View Printable Coupon</a></p>";
					}
					
					echo "</div>";
					
					// Check special count
					if ($i++ == $limit) break;
				}

				if('yes' == $viewall ) {
					// Display a link to Leafly profile
					echo "<p><span class='leafly-specials-plugin-meta-item'><a class='leafly-specials-plugin-viewall' href='https://www.leafly.com/dispensary-info/". $slug ."/specials' target='_blank'>View all specials &rarr;</a></span></p>";
				}
            }
        } else {
            _e( 'No location has been specified!', 'leafly-specials' );
        }
		
		$output_string=ob_get_contents();
		ob_end_clean();

		return $output_string;

}

add_shortcode('leaflyspecials', 'leafly_specials_shortcode');
 
?>
