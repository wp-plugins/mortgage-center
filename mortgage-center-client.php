<?
add_filter('the_posts', 'MortgageCenter_Client::Activate');
add_filter('posts_request', 'MortgageCenter_Client::ClearQuery');

class MortgageCenter_Client {
	static $IsActivated = false;
	static $ZillowApiKey = 'X1-ZWz1c55uzwlk3v_6zfs6';
	static $MortgageNewsSource = 'http://pipes.yahoo.com/pipes/pipe.run?_id=7c4d648c424678eb7374f68882f4dc08&_render=rss';
	static $Options = null;
	static $RequestedArticleName = null;
	
	static function Activate($posts){
		global $MortgageCenter_States;
		
		self::$Options = array(
			'state'					=> get_option('mortgage-center-state'),
			'url-slug'				=> get_option('mortgage-center-url-slug'),
			'zillow-profile-name'	=> get_option('mortgage-center-zillow-profile-name'),
			'calc-price'			=> get_option('mortgage-center-calc-price'),
			'calc-down'				=> get_option('mortgage-center-calc-down'),
			'calc-zip'				=> get_option('mortgage-center-calc-zip'),
			'panels-to-display'		=> get_option('mortgage-center-panels-to-display')
		);
		
		if (preg_match('/' . preg_quote(self::$Options['url-slug'], '/') . '(?:\/(?P<article>[^\/]+))?\/?$/i', $_SERVER['REQUEST_URI'], $activation_matches) == 0)
			return $posts;
		
		self::$IsActivated = true;
		self::$RequestedArticleName = $activation_matches['article'];
		
		remove_filter('the_content', 'wpautop'); // keep wordpress from mucking up our HTML
		add_action('template_redirect', 'MortgageCenter_Client::OverrideTemplate');
		add_action('wp_head', 'MortgageCenter_Client::Header');
		add_action('wp_footer', 'MortgageCenter_Client::Footer');
		wp_enqueue_script('jquery');
		
		if (!self::$Options['state'])
			$post_title = 'National Mortgage Information';
		else
			$post_title = $MortgageCenter_States[self::$Options['state']] . ' Mortgage Information';
		$formattedNow = date('Y-m-d H:i:s');
		
		if (self::$RequestedArticleName) {
			$article_contents = file_get_contents(ABSPATH . 'wp-content/plugins/mortgage-center/articles/' . self::$RequestedArticleName . '.html');
			$article_content_lines = explode("\n", $article_contents, 2);
			$post_title = $article_content_lines[0];
		}
		
		return array((object)array(
			'ID'				=> -1,
			'comment_status'	=> 'closed',
			'post_author'		=> 0,
			'post_content'		=> self::LoadContent(),
			'post_date'			=> $formattedNow,
			'post_date_gmt'		=> $formattedNow,
			'post_name'			=> 'mortgage-center',
			'post_status'		=> 'publish',
			'post_title'		=> $post_title,
			'post_type'			=> 'page'
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
		$zillowApiKey = self::$ZillowApiKey;
		$stateAbbrev = self::$Options['state'];
		$url_slug = self::$Options['url-slug'];
		$zillow_profile_hash = '';
		
		if (self::$Options['zillow-profile-name'])
			$zillow_profile_hash = '#{scrnnm=' . self::$Options['zillow-profile-name'] . '}';
		
		if (self::$RequestedArticleName)
			$content = self::LoadContentNews();
		else
			$content = self::LoadContentPrimary();
		
		$html = <<<HTML
		<script>
			MortgageCenter.zillowApiKey = '{$zillowApiKey}';
			MortgageCenter.state = '{$stateAbbrev}';
		</script>
		<div class="mortgage-center">
			<div class="mortgage-center-header">
				<div class="mortgage-center-header-left"></div>
				<div class="mortgage-center-header-middle">
HTML;
		if (self::$Options['panels-to-display']['rates'])
			$html .= '<a href="/$url_slug/#mc-rates">Rates</a> | ';
		if (self::$Options['panels-to-display']['calculator'])
			$html .= '<a href="/$url_slug/#mc-monthly-payments">Monthly Payments</a> | ';
		if (self::$Options['panels-to-display']['closing-costs'])
			$html .= '<a href="/$url_slug/#mc-closing-costs">Closing Costs</a> | ';
		if (self::$Options['panels-to-display']['articles'])
			$html .= '<a href="/$url_slug/#mc-help">Help</a> | ';
		if (self::$Options['panels-to-display']['news'])
			$html .= '<a href="/$url_slug/#mc-news">News</a>';

		$html .= <<<HTML
					<div id="mortgage-center-powered-by">
						powered by
						<a href="http://www.zillow.com/mortgage$zillow_profile_hash"><img src="$blog_url/wp-content/plugins/mortgage-center/images/zmm_logo_small.gif" alt="Zillow Mortgages" /></a>
					</div>
				</div>
				<div class="mortgage-center-header-right"></div>
			</div>
			$content
		</div>
HTML;
		return $html;
	}
	static function LoadContentPrimary()
	{
		global $MortgageCenter_States;
		
		$url_slug = self::$Options['url-slug'];
		$news = self::GetMortgageNews();
		$zillow_profile_hash = '';
		$blog_url = get_bloginfo('wpurl');
		$calc_price = self::$Options['calc-price'];
		$calc_down = self::$Options['calc-down'];
		$calc_zip = self::$Options['calc-zip'];
		$html = '';
		
		if (self::$Options['zillow-profile-name'])
			$zillow_profile_hash = '#{scrnnm=' . self::$Options['zillow-profile-name'] . '}';
		
		if (self::$Options['state'])
		{
			$full_state = $MortgageCenter_States[self::$Options['state']];
			$full_state_for_link = str_replace(' ', '_', $full_state) + '_';
		}
		else
		{
			$full_state = 'National';
			$full_state_for_link = '';
		}
		
		if (self::$Options['panels-to-display']['rates'])
		{
			$html .= <<<HTML
			<a name="mc-rates"></a>
			<div class="mortgage-center-container">
				<div class="mortgage-center-container-top mortgage-center-container-cap">
					<div class="mortgage-center-container-top-left mortgage-center-container-left"></div>
					<h3>Mortgage Rates</h3>
					<div class="mortgage-center-container-top-right mortgage-center-container-right"></div>
				</div>
				<div class="mortgage-center-container-body">
					<table>
						<tr>
							<td></td>
							<th style="width: 90px;">Last Week</th>
							<th style="width: 90px;">Current</th>
						</tr>
						<tr class="mortgage-center-secondary">
							<td>30 Year Fixed</td>
							<td id="mortgage-center-rates-cur-30yf" class="mortgage-center-rate"></td>
							<td id="mortgage-center-rates-last-30yf" class="mortgage-center-rate"></td>
						</tr>
						<tr>
							<td>15 Year Fixed</td>
							<td id="mortgage-center-rates-cur-15yf" class="mortgage-center-rate"></td>
							<td id="mortgage-center-rates-last-15yf" class="mortgage-center-rate"></td>
						</tr>
						<tr class="mortgage-center-secondary">
							<td>5/1 ARM</td>
							<td id="mortgage-center-rates-cur-51arm" class="mortgage-center-rate"></td>
							<td id="mortgage-center-rates-last-51arm" class="mortgage-center-rate"></td>
						</tr>
					</table>
					<div id="mortgage-center-compare-rates" class="mortgage-center-branding-link">
						<a href="http://www.zillow.com/{$full_state_for_link}Mortgage_Rates/$zillow_profile_hash">Compare $full_state Mortgage Rates</a> 
					</div>
				</div>
				<div class="mortgage-center-container-bottom mortgage-center-container-cap">
					<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
					<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
				</div>
			</div>
HTML;
		}
			
		if (self::$Options['panels-to-display']['calculator'])
		{
			$html .= <<<HTML
			<a name="mc-monthly-payments"></a>
			<div class="mortgage-center-container">
				<div class="mortgage-center-container-top mortgage-center-container-cap">
					<div class="mortgage-center-container-top-left mortgage-center-container-left"></div>
					<h3>Calculate Your Monthly Payment</h3>
					<div id="mortgage-center-more-calculators">
						<a href="http://www.zillow.com/mortgage/calculator/Calculators.htm$zillow_profile_hash">More Mortgage Calculators</a>
					</div>
					<div class="mortgage-center-container-top-right mortgage-center-container-right"></div>
				</div>
				<div class="mortgage-center-container-body">
					<form id="mortgage-center-calc-input">
						<label for="mortgage-center-calc-hp">Home Price:</label>
						<input type="text" id="mortgage-center-calc-hp" style="width: 60px;" value="$calc_price" />
						<label for="mortgage-center-calc-pd">Percent Down:</label>
						<input type="text" id="mortgage-center-calc-pd" style="width: 20px;" value="$calc_down" />
						<label for="mortgage-center-calc-zip">Zip:</label>
						<input type="text" id="mortgage-center-calc-zip" style="width: 40px;" value="$calc_zip" />
						<input type="button" value="Calculate" id="mortgage-center-calc-submit" />
					</form>
					<table id="mortgage-center-calculator-table" style="text-align: center; display: none;">
						<tr>
							<th>Loan Type</th>
							<th>Monthly Payment</th>
							<th>Rate</th>
							<th>Principal & Interest</th>
							<th>Mortgage Insurance</th>
							<th>Property Tax</th>
						</tr>
						<tr class="mortgage-center-secondary">
							<td>30 Year Fixed</td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-30yf-mp"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-30yf-r"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-30yf-pi"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-30yf-i"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-30yf-t"></td>
						</tr>
						<tr>
							<td>15 Year Fixed</td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-15yf-mp"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-15yf-r"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-15yf-pi"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-15yf-i"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-15yf-t"></td>
						</tr>
						<tr class="mortgage-center-secondary">
							<td>5/1 ARM</td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-51arm-mp"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-51arm-r"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-51arm-pi"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-51arm-i"></td>
							<td class="mortgage-center-calc-value" id="mortgage-center-calc-51arm-t"></td>
						</tr>
					</table>
					<div class="clear"></div>
					<div id="mortgage-center-compare-rates" class="mortgage-center-branding-link">
						<a href="http://www.zillow.com/mortgage/$zillow_profile_hash">Shop for Mortgage Loans</a> 
					</div>
				</div>
				<div class="mortgage-center-container-bottom mortgage-center-container-cap">
					<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
					<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
				</div>
			</div>
HTML;
		}
			
		if (self::$Options['panels-to-display']['closing-costs'])
		{
			$html .= <<<HTML
			<a name="mc-closing-costs"></a>
			<div class="mortgage-center-container">
				<div class="mortgage-center-container-top mortgage-center-container-cap">
					<div class="mortgage-center-container-top-left mortgage-center-container-left"></div>
					<h3>Closing.com Closing Cost Calculator</h3>
					<div class="mortgage-center-container-top-right mortgage-center-container-right"></div>
				</div>
				<div id="mortgage-center-cc-container" class="mortgage-center-container-body">
					<iframe src="{$blog_url}/wp-content/plugins/mortgage-center/closing-costs.html" id="mortgage-center-closing-costs-iframe" scrolling="no">
					</iframe>
				</div>
				<div class="mortgage-center-container-bottom mortgage-center-container-cap">
					<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
					<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
				</div>
			</div>
HTML;
		}
			
		if (self::$Options['panels-to-display']['articles'])
		{
			$html .= <<<HTML
			<a name="mc-help"></a>
			<div class="mortgage-center-container">
				<div class="mortgage-center-container-top mortgage-center-container-cap">
					<div class="mortgage-center-container-top-left mortgage-center-container-left"></div>
					<h3>Mortgage Articles</h3>
					<div class="mortgage-center-container-top-right mortgage-center-container-right"></div>
				</div>
				<div class="mortgage-center-container-body">
					<ul id="mortgage-center-articles">
						<li><a href="/$url_slug/fha-loan">FHA Loan</a></li>
						<li><a href="/$url_slug/refinancing">Refinancing</a></li>
						<li><a href="/$url_slug/home-equity-loan">Home Equity Loan</a></li>
						<li><a href="/$url_slug/can-you-afford-a-mortgage">Can You Afford a Mortgage?</a></li>
						<li><a href="/$url_slug/types-of-mortgages">Types of Mortgages</a></li>
						<li><a href="/$url_slug/mortgage-insurance">Mortgage Insurance</a></li>
						<li><a href="/$url_slug/finding-mortgages-with-bad-credit">Finding Mortgages With Bad Credit</a></li>
						<li><a href="/$url_slug/understanding-fees-and-closing-costs">Understanding Fees and Closing Costs</a></li>
						<li><a href="/$url_slug/what-to-ask-mortgage-lenders">What to Ask Mortgage Lenders</a></li>
					</ul>
				</div>
				<div class="mortgage-center-container-bottom mortgage-center-container-cap">
					<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
					<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
				</div>
			</div>
HTML;
		}
			
		if (self::$Options['panels-to-display']['news'])
		{
			$html .= <<<HTML
			<a name="mc-news"></a>
			<div class="mortgage-center-container">
				<div class="mortgage-center-container-top mortgage-center-container-cap">
					<div class="mortgage-center-container-top-left mortgage-center-container-left"></div>
					<h3>Mortgage News</h3>
					<div class="mortgage-center-container-top-right mortgage-center-container-right"></div>
				</div>
				<div class="mortgage-center-container-body">{$news}</div>
				<div class="mortgage-center-container-bottom mortgage-center-container-cap">
					<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
					<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
				</div>
			</div>
HTML;
		}
		
		return $html;
	}
	static function LoadContentNews()
	{
		$article_contents = file_get_contents(ABSPATH . 'wp-content/plugins/mortgage-center/articles/' . self::$RequestedArticleName . '.html');
		$article_content_lines = explode("\n", $article_contents, 2);
		return $article_content_lines[1];
	}
	static function GetMortgageNews()
	{
		include_once(ABSPATH . WPINC . '/feed.php');
		
		$news = fetch_feed(self::$MortgageNewsSource);
		$news_items = $news->get_items();
		
		$news_html = '<ul>';
		foreach ($news_items as $news_item)
		{
			$title = $news_item->get_title();
			$link = $news_item->get_permalink();
			//$date = $news_item->get_date('j F Y | g:i a');
			$source = $news_item->get_description();
			 
			$news_html .= <<<HTML
			<li>
				<a href="$link" title="$title">$title</a>
				<span class="mortgage-center-news-source">(via $source)</span>
			</li>
HTML;
		}
		$news_html .= '</ul>';
		
		return $news_html;
	}
	static function Header() {
		$blog_url = get_bloginfo('wpurl');
		echo <<<HEAD
			<link rel="stylesheet" type="text/css" href="{$blog_url}/wp-content/plugins/mortgage-center/css/client.css" />
			<script src="{$blog_url}/wp-content/plugins/mortgage-center/js/client.js"></script>
HEAD;
	}
	static function Footer() {
		$current_year = date('Y');
		$zillow_profile_hash = '';
		
		if (self::$Options['zillow-profile-name'])
			$zillow_profile_hash = '#{scrnnm=' . self::$Options['zillow-profile-name'] . '}';
		
		echo <<<FOOTER
			<p>&copy; Zillow, Inc., {$current_year}. Use is subject to <a href="http://www.zillow.com/corp/Terms.htm$zillow_profile_hash" target="_blank">Terms of Use</a>.</p>
FOOTER;
		return $content;
	}
}
?>