<?
add_filter('the_posts', 'MortgageCenter_Client::Activate');
add_filter('posts_request', 'MortgageCenter_Client::ClearQuery');

class MortgageCenter_Client {
	static $IsActivated = false;
	static $ZillowApiKey = 'X1-ZWz1c55uzwlk3v_6zfs6';
	
	static function Activate($posts){
		global $MortgageCenter_States;
		
		$abbreviated_state = get_option('mortgage-center-state');
		$mortgage_url = get_option('mortgage-center-url-slug');
		$full_state = $MortgageCenter_States[$abbreviated_state];
		
		if (!preg_match("/{$mortgage_url}/", $GLOBALS['wp']->request))
			return $posts;
		
		self::$IsActivated = true;
		
		remove_filter('the_content', 'wpautop'); // keep wordpress from mucking up our HTML
		add_action('template_redirect', 'MortgageCenter_Client::OverrideTemplate');
		add_action('wp_head', 'MortgageCenter_Client::Header');
		add_action('wp_footer', 'MortgageCenter_Client::Footer');
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
				<div class="mortgage-center-container-body">
					<div id="ccWidgetWrapper">
					    <style>
					        #ccWidgetWrapper
					        {
					            margin: 0;
					            padding: 0;
					            width: 420px;
					            height: 590px;
					            border: 6px solid #a8e1ff;
					            padding: 2px;
					            background-color: #86c1e0;
					        }
					        #ccWidgetFooter a
					        {
					            font-size: 9px;
					            font-family: arial;
					            text-decoration: none;
					        }
					        #ccWidgetFooter a:hover
					        {
					            text-decoration: underline;
					        }
					        #ccWidgetFooter p
					        {
					            font-size: 9px;
					            font-family: arial;
					        }
					        #ccWidgetFooter h3
					        {
					            font-size: 9px;
					            font-family: arial;
					        }
					    </style>
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
					<ul>
						<li>FHA Loan</li>
						<li>Refinancing</li>
						<li>Home Equity Loan</li>
						<li>Can You Afford a Mortgage?</li>
						<li>Types of Mortgages</li>
						<li>Mortgage Insurance</li>
						<li>Finding Mortgages With Bad Credit</li>
						<li>Understanding Fees and Closing Costs</li>
						<li>Estimate Your Credit Score</li>
						<li>What to Ask Mortgage Lenders</li>
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
				<div class="mortgage-center-container-body"></div>
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
HEAD;
	}
	static function Footer() {
		
	}
}
?>