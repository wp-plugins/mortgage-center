<?
add_filter('the_posts', array(&$this, 'MortgageCenter_Admin::AddMenu'));
add_filter('wp_head', array(&$this, 'MortgageCenter_Admin::AddMenu'));
add_filter('posts_request', array(&$this, 'MortgageCenter_Admin::AddMenu'));
add_action('wp_footer', array(&$this, 'MortgageCenter_Admin::AddMenu'));

class MortgageCenter_Client {
	public $IsActivated = false;
	
	function LoadClient($posts){
		$blog_url = $GLOBALS['$wp']->request;
		global $wp_query;

		$cityStateRegex = "/". $this->slug ."\/((?P<neighborhood>[^\/]+)\/)?(?P<city>[^\/]+)\/(?P<state>\w{2})/";
		$zipRegex = "/". $this->slug ."\/(?P<zip>\d{5})/";
		$cityStateRegexSuccess = preg_match($cityStateRegex, $wp->request, $cityStateUrlMatch);
		$zipRegexSuccess = preg_match($zipRegex, $wp->request, $zipUrlMatch);
		
		if ($cityStateRegexSuccess > 0) {
			$this->city = trim(ucwords(str_replace('-', ' ', $cityStateUrlMatch['city'])));
			$this->state = trim(strtoupper(str_replace('-', ' ', $cityStateUrlMatch['state'])));
			
			if ($cityStateUrlMatch['neighborhood'] != '') {
				$this->neighborhood = trim(ucwords(str_replace('-', ' ', $cityStateUrlMatch['neighborhood'])));
				$this->location_for_display = $this->neighborhood . ', ' . $this->city . ', ' . $this->state;
			} else {
				$this->location_for_display = $this->city . ', ' . $this->state;
			}
		} else if ($zipRegexSuccess > 0) {
			$this->zip = $zipUrlMatch['zip'];
			$this->location_for_display = $this->zip;
		} else {
			$this->is_lme = false;
			return $posts;
		}
		
		$this->is_lme = true;
		
		$wp_query->is_page = true;
		//Not sure if this one is necessary but might as well set it like a true page
		$wp_query->is_single = true;
		$wp_query->is_home = false;
		$wp_query->is_archive = false;
		//$wp_query->is_category = false;
		//Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
		unset($wp_query->query["error"]);
		$wp_query->query_vars["error"]="";
		$wp_query->is_404 = false;
		
		return $posts;
	}
}
?>