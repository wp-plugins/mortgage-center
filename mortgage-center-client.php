<?
add_filter('the_posts', 'MortgageCenter_Client::Activate');
add_filter('posts_request', 'MortgageCenter_Client::ClearQuery');

class MortgageCenter_Client {
	static $IsActivated = false;
	static $ZillowApiKey = 'X1-ZWz1c55uzwlk3v_6zfs6';
	static $MortgageNewsSource = 'http://pipes.yahoo.com/pipes/pipe.run?_id=7c4d648c424678eb7374f68882f4dc08&_render=rss';
	static $Options = null;
	
	static function Activate($posts){
		global $MortgageCenter_States;
		
		self::$Options = array(
			'state'      => get_option('mortgage-center-state'),
			'url-slug'   => get_option('mortgage-center-url-slug')
		);
		$full_state = $MortgageCenter_States[self::$Options['state']];
		
		if (!preg_match("/{$mortgage_url}/", $GLOBALS['wp']->request))
			return $posts;
		
		self::$IsActivated = true;
		
		remove_filter('the_content', 'wpautop'); // keep wordpress from mucking up our HTML
		add_action('template_redirect', 'MortgageCenter_Client::OverrideTemplate');
		add_action('wp_head', 'MortgageCenter_Client::Header');
		//add_action('wp_footer', 'MortgageCenter_Client::Footer'); // disclaimers?
		wp_enqueue_script('jquery');
		
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
			'post_title'     => $full_state . ' Mortgage Information',
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
		$news = self::GetMortgageNews();
		$url_slug = self::$Options['url-slug'];
		
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
							<td id="mortgage-center-rates-cur-30yf"></td>
							<td id="mortgage-center-rates-last-30yf"></td>
						</tr>
						<tr>
							<td>15 Year Fixed</td>
							<td id="mortgage-center-rates-cur-15yf"></td>
							<td id="mortgage-center-rates-last-15yf"></td>
						</tr>
						<tr class="mortgage-center-secondary">
							<td>5/1 ARM</td>
							<td id="mortgage-center-rates-cur-51arm"></td>
							<td id="mortgage-center-rates-last-51arm"></td>
						</tr>
					</table>
				</div>
				<div class="mortgage-center-container-bottom mortgage-center-container-cap">
					<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
					<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
				</div>
			</div>
			
			<a name="mc-monthly-payments"></a>
			<div class="mortgage-center-container">
				<div class="mortgage-center-container-top mortgage-center-container-cap">
					<div class="mortgage-center-container-top-left mortgage-center-container-left"></div>
					<h3>Calculate Your Monthly Payment</h3>
					<div class="mortgage-center-container-top-right mortgage-center-container-right"></div>
				</div>
				<div class="mortgage-center-container-body">
					<form id="mortgage-center-calc-input">
						<label for="mortgage-center-calc-hp">Home Price</label>
						<input type="text" id="mortgage-center-calc-hp" />
						<label for="mortgage-center-calc-pd">Percent Down (%)</label>
						<input type="text" id="mortgage-center-calc-pd" />
						<label for="mortgage-center-calc-zip">Zip</label>
						<input type="text" id="mortgage-center-calc-zip" />
						<input type="button" value="Calculate" id="mortgage-center-calc-submit" />
					</form>
					<table style="text-align: center;">
						<tr>
							<th>Loan Type</th>
							<th>Monthly Payment</th>
							<th>Rate</th>
							<th>Principal & Interest</th>
							<th>Insurance</th>
							<th>Tax</th>
						</tr>
						<tr class="mortgage-center-secondary">
							<td>30 Year Fixed</td>
							<td id="mortgage-center-calc-30yf-lt"></td>
							<td id="mortgage-center-calc-30yf-mp"></td>
							<td id="mortgage-center-calc-30yf-r"></td>
							<td id="mortgage-center-calc-30yf-pi"></td>
							<td id="mortgage-center-calc-30yf-t"></td>
						</tr>
						<tr>
							<td>15 Year Fixed</td>
							<td id="mortgage-center-calc-15yf-lt"></td>
							<td id="mortgage-center-calc-15yf-mp"></td>
							<td id="mortgage-center-calc-15yf-r"></td>
							<td id="mortgage-center-calc-15yf-pi"></td>
							<td id="mortgage-center-calc-15yf-t"></td>
						</tr>
						<tr class="mortgage-center-secondary">
							<td>5/1 ARM</td>
							<td id="mortgage-center-calc-51arm-lt"></td>
							<td id="mortgage-center-calc-51arm-mp"></td>
							<td id="mortgage-center-calc-51arm-r"></td>
							<td id="mortgage-center-calc-51arm-pi"></td>
							<td id="mortgage-center-calc-51arm-t"></td>
						</tr>
					</table>
				</div>
				<div class="mortgage-center-container-bottom mortgage-center-container-cap">
					<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
					<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
				</div>
			</div>
			
			<a name="mc-closing-costs"></a>
			<div class="mortgage-center-container">
				<div class="mortgage-center-container-top mortgage-center-container-cap">
					<div class="mortgage-center-container-top-left mortgage-center-container-left"></div>
					<h3>Closing.com Closing Cost Calculator</h3>
					<div class="mortgage-center-container-top-right mortgage-center-container-right"></div>
				</div>
				<div id="mortgage-center-cc-container" class="mortgage-center-container-body">
					<div id="ccWidgetWrapper">
					    <div id="ccSmartClosingCalculator">
					        <div id="ccFlashPlaceholder">
					            <p>
					                To use the <em>Smart</em>Closing™ Calculator, please install Flash Player for your
					                browser by clicking the button below.
					            </p>
					            <p>
					                <a href="http://www.adobe.com/go/getflashplayer">
					                    <img alt="Get Adobe Flash player" src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" />
					                </a>
					            </p>
					        </div>
					    </div>
					
					    <script src="http://yui.yahooapis.com/2.8.0r4/build/yuiloader/yuiloader-min.js" type="text/javascript"></script>
					
					    <script type="text/javascript">
						  //<![CDATA[
						    var ccAutoloadJsOptions = { file: ["cc.widget.calculator.js"], 
						    	onSuccess: function() { 
						    		CC.widget.calculator("narrow", "ccFlashPlaceholder", {
						    			dataService_apiKey: "AF6DD2F8-ADE8-11DE-A322-202E56D89593"
						    		});
						    	}
						    };
						  //]]>
					    </script>
					
					    <script src="http://www.closing.com/partner/assets/js/cc/cc.js" type="text/javascript"></script>
					
					    <div id="ccWidgetFooter">
					        <h3>
					            <a href="http://www.closing.com">Closing.com"s</a> Disclaimer</h3>
					        <p>
					            Though ClosingCorp makes certain efforts to ensure that the results, rates, estimates,
					            reports, and other data made available on our site and through our services are
					            reasonably accurate and reliable for their intended purposes, <strong>SUCH INFORMATION
					                IS NOT GUARANTEED</strong> and may be subject to other terms and conditions.
					            Neither ClosingCorp nor any authorized licensees of our services and content assume
					            responsibility for the accuracy, timeliness, correctness, or completeness of such
					            estimates, reports, or information, virtually all of which is originated by others.</p>
					        <p>
					            As a service to our users, Closing.com incorporates rate, mortgage, and other calculators
					            on certain of its pages. The results and reports provided by these calculators are
					            intended for hypothetical, illustrative, and comparative purposes only. Calculators
					            are not intended to offer any tax, legal, or financial advice and <strong>ALL INFORMATION,
					                REPORTS, AND ESTIMATES PROVIDED ARE WITHOUT REPRESENTATION OR WARRANTY AS TO THEIR
					                RELEVANCE, ACCURACY, CORRECTNESS, OR COMPLETENESS</strong>. Please consult with
					            qualified professionals to discuss your situation. <a href="http://www.closing.com/Home/Disclaimer">
					                More</a></p>
					    </div>
					</div>
	
				</div>
				<div class="mortgage-center-container-bottom mortgage-center-container-cap">
					<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
					<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
				</div>
			</div>
			
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
						<li><a href="/$url_slug/fha-loan">Refinancing</a></li>
						<li><a href="/$url_slug/fha-loan">Home Equity Loan</a></li>
						<li><a href="/$url_slug/can-you-afford-a-mortgage">Can You Afford a Mortgage?</a></li>
						<li><a href="/$url_slug/fha-loan">Types of Mortgages</a></li>
						<li><a href="/$url_slug/fha-loan">Mortgage Insurance</a></li>
						<li><a href="/$url_slug/fha-loan">Finding Mortgages With Bad Credit</a></li>
						<li><a href="/$url_slug/fha-loan">Understanding Fees and Closing Costs</a></li>
						<li><a href="/$url_slug/fha-loan">Estimate Your Credit Score</a></li>
						<li><a href="/$url_slug/fha-loan">What to Ask Mortgage Lenders</a></li>
					</ul>
				</div>
				<div class="mortgage-center-container-bottom mortgage-center-container-cap">
					<div class="mortgage-center-container-bottom-left mortgage-center-container-left"></div>
					<div class="mortgage-center-container-bottom-right mortgage-center-container-right"></div>
				</div>
			</div>
			
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
		</div>
HTML;
	}
	static function Header() {
		echo <<<HEAD
			<link rel="stylesheet" type="text/css" href="{$wpurl}/wp-content/plugins/mortgage-center/css/client.css" />
			<script src="{$wpurl}/wp-content/plugins/mortgage-center/js/client.js"></script>
HEAD;
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
	static function Footer() {
		
	}
}
?>