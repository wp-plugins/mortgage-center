<?
add_action('admin_menu', 'MortgageCenter_Admin::AddMenu');

class MortgageCenter_Admin {
	static function AddMenu() {
		add_options_page('Mortgage Center Options', 'Mortgage Center', 'manage_options', 'mortgage-center-options', 'MortgageCenter_Admin::Initialize');
	}
	static function SavePostedData() {
		update_option('mortgage-center-state', $_POST['mc-state']);
		update_option('mortgage-center-url-slug', $_POST['mc-url-slug']);
		update_option('mortgage-center-zillow-profile-name', $_POST['mc-zillow-profile-name']);
		update_option('mortgage-center-calc-price', $_POST['mc-calc-price']);
		update_option('mortgage-center-calc-down', $_POST['mc-calc-down']);
		update_option('mortgage-center-calc-zip', $_POST['mc-calc-zip']);
		update_option('mortgage-center-panels-to-display', array(
			'rates'			=> $_POST['mc-panels-rates'],
			'calculator'	=> $_POST['mc-panels-calculator'],
			'closing-costs'	=> $_POST['mc-panels-closing-costs'],
			'articles'		=> $_POST['mc-panels-articles'],
			'news'			=> $_POST['mc-panels-news']
		));
	}
	static function Initialize() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br/></div>
		<h2>Mortgage Center Options</h2>
<?
		if (isset($_POST['Submit'])) {
			self::SavePostedData();
?>
		<div id="message" class="updated fade">
			<p><strong>Options Saved</p></strong>
		</div>
<?
		}
		
		global $MortgageCenter_States;
		
		$saved_state = get_option('mortgage-center-state');
		$saved_url_slug = get_option('mortgage-center-url-slug');
		$saved_zillow_name = get_option('mortgage-center-zillow-profile-name');
		$saved_calc_price = get_option('mortgage-center-calc-price');
		$saved_calc_down = get_option('mortgage-center-calc-down');
		$saved_calc_zip = get_option('mortgage-center-calc-zip');
		$saved_panels_to_display = get_option('mortgage-center-panels-to-display');
?>
		<form method="post">
			<table class="form-table">
				<tr>
					<th>
						<label for="mc-state">State to load data for</label>
					</th>
					<td>
						<select id="mc-state" name="mc-state">
<?
		foreach ($MortgageCenter_States as $abbreviation => $state) {
			?><option value="<?= $abbreviation ?>"><?= $state ?></option><?
		}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label for="mc-url-slug">Plugin URL</label>
					</th>
					<td>
						<?= get_bloginfo('wpurl') ?>/<input type="text" id="mc-url-slug" name="mc-url-slug" value="<?= $saved_url_slug ?>" />
					</td>
				</tr>
				<tr>
					<th>
						<label for="mc-url-slug">Zillow profile name</label>
					</th>
					<td>
						<input type="text" id="mc-zillow-profile-name" name="mc-zillow-profile-name" value="<?= $saved_zillow_name ?>" />
					</td>
				</tr>
				<tr>
					<th>
						<label>Mortgage calculator defaults</label>
					</th>
					<td>
						Price: <input type="text" id="mc-calc-price" name="mc-calc-price" value="<?= $saved_calc_price ?>" style="width: 70px; margin-right: 10px;" />
						Percent down: <input type="text" id="mc-calc-down" name="mc-calc-down" value="<?= $saved_calc_down ?>" style="width: 30px; margin-right: 10px;" />
						Zip: <input type="text" id="mc-calc-zip" name="mc-calc-zip" value="<?= $saved_calc_zip ?>" style="width: 50px;" />
					</td>
				</tr>
				<tr>
					<th>
						<label>Panels to display</label>
					</th>
					<td>
						<div>
							<input type="checkbox" id="mc-panels-rates" name="mc-panels-rates" <?= $saved_panels_to_display['rates'] ? 'checked="checked"' : '' ?> />
							<label for="mc-panels-rates">Rates</label>
						</div>
						<div>
							<input type="checkbox" id="mc-panels-calculator" name="mc-panels-calculator" <?= $saved_panels_to_display['calculator'] ? 'checked="checked"' : '' ?> />
							<label for="mc-panels-calculator">Calculator</label>
						</div>
						<div>
							<input type="checkbox" id="mc-panels-closing-costs" name="mc-panels-closing-costs" <?= $saved_panels_to_display['closing-costs'] ? 'checked="checked"' : '' ?> />
							<label for="mc-panels-closing-costs">Closing Costs</label>
						</div>
						<div>
							<input type="checkbox" id="mc-panels-articles" name="mc-panels-articles" <?= $saved_panels_to_display['articles'] ? 'checked="checked"' : '' ?> />
							<label for="mc-panels-articles">Articles</label>
						</div>
						<div>
							<input type="checkbox" id="mc-panels-news" name="mc-panels-news" <?= $saved_panels_to_display['news'] ? 'checked="checked"' : '' ?> />
							<label for="mc-panels-news">News</label>
						</div>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" name="Submit" value="Submit" />
			</p>
		</form>
	</div>
	<script>
		jQuery('#mc-state').val('<?= $saved_state ?>');
	</script>
<?
	}
}
?>