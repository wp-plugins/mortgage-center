<?
add_filter('the_posts', 'MortgageCenter_Client::Activate');
add_filter('posts_request', 'MortgageCenter_Client::ClearQuery');
add_action('wp_head', 'MortgageCenter_Client::Header');
add_action('wp_footer', 'MortgageCenter_Client::Footer');

class MortgageCenter_Client {
	public $IsActivated = false;
	
	function Activate($posts){
		$blog_url = get_option('mortgage-center-url-slug');
		
		if (!preg_match('^/{$blog_url}', $GLOBALS['wp']->request))
			return $posts;
		
		self::$IsActivated = true;
		
		remove_filter('the_content', 'wpautop'); // keep wordpress from mucking up our HTML
		add_action('template_redirect', 'MortgageCenter_Client::OverrideTemplate');
		
		$formattedNow = date('Y-m-d H:i:s');
		return array(
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
		);
	}
	function ClearQuery($query) {
		if (self::$IsActivated)
			return 'SELECT NULL WHERE 1 = 0';
		else
			return $query;
	}
	function OverrideTemplate() {
		if (file_exists(TEMPLATEPATH . '/page.php'))
			include(TEMPLATEPATH . '/page.php');
		elseif (file_exists(TEMPLATEPATH . '/custom_template.php'))
			include(TEMPLATEPATH . '/custom_template.php'); // this is for the Thesis theme
		else
			include(TEMPLATEPATH . '/post.php');
		
		exit;
	}
	function LoadContent() {
		return '1';
	}
	function Header() {
		
	}
	function Footer() {
		
	}
}
?>