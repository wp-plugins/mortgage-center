<?
add_filter('the_posts', 'MortgageCenter_Client::Activate');
add_filter('posts_request', 'MortgageCenter_Client::ClearQuery');

class MortgageCenter_Client {
	static $IsActivated = false;
	static $ZillowApiKey = 'X1-ZWz1c55uzwlk3v_6zfs6';
	
	static function Activate($posts){
		$mortgage_url = get_option('mortgage-center-url-slug');
		
		if (!preg_match("/{$mortgage_url}/", $GLOBALS['wp']->request))
			return $posts;
		
		self::$IsActivated = true;
		
		remove_filter('the_content', 'wpautop'); // keep wordpress from mucking up our HTML
		add_action('template_redirect', 'MortgageCenter_Client::OverrideTemplate');
		add_action('wp_head', 'MortgageCenter_Client::Header');
		add_action('wp_footer', 'MortgageCenter_Client::Footer');
		
		$formattedNow = date('Y-m-d H:i:s');
		return array((object)array(
			'ID'             => -1,
			'comment_status' => 'closed',
			'post_author'    => 0,
			'post_content'   => self::LoadContent(),
			'post_date'      => $formattedNow,
			'post_date_gmt'  => $formattedNow,
			'post_name'      => 'mortgage-center',
			'post_status'    => 'publish',
			'post_title'     => 'Mortgage Center',
			'post_type'      => 'page'
		));
	}
	static function ClearQuery($query) {
		if (self::$IsActivated)
			return 'SELECT NULL WHERE 1 = 0';
		else
			return $query;
	}
	static function OverrideTemplate() {
		if (file_exists(TEMPLATEPATH . '/page.php'))
			include(TEMPLATEPATH . '/page.php');
		elseif (file_exists(TEMPLATEPATH . '/custom_template.php'))
			include(TEMPLATEPATH . '/custom_template.php'); // this is for the Thesis theme
		else
			include(TEMPLATEPATH . '/post.php');
		
		exit;
	}
	static function LoadContent() {
		get_option('mortgage-center-state');
		
		return <<<HTML
		<div class="mortgage-center">
			<div class="mortgage-center-header">
				<div class="mortgage-center-header-left"></div>
				<div class="mortgage-center-header-middle">
					<a href="#mc-rates">Rates</a> |
					<a href="#mc-monthly-payments">Monthly Payments</a> |
					<a href="#mc-closing-costs">Closing Costs</a> |
					<a href="#mc-help">Help</a> |
					<a href="#mc-news">News</a>
				</div>
				<div class="mortgage-center-header-right"></div>
			</div>
		</div>
		
		<a name="mc-rates"></a>
		<div class="mortgage-center-container">
			<div class="mortgage-center-container-top mortgage-center-container-cap">
				<div class="mortgage-center-container-top-left mortgage-center-container-left"></div>
				<h3>Market Statistics</h3>
				<div class="mortgage-center-container-top-right mortgage-center-container-right"></div>
			</div>
			<div class="mortgage-center-container-body">1234567890</div>
			<div class="mortgage-center-container-bottom mortgage-center-container-cap">
				<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
				<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
			</div>
		</div>
HTML;
	}
	static function Header() {
		echo <<<HEAD
			<link rel="stylesheet" type="text/css" href="{$wpurl}/wp-content/plugins/mortgage-center/css/client.css" />
HEAD;
	}
	static function Footer() {
		
	}
}
?>