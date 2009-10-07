<?
add_action('admin_menu', 'MortgageCenter_Admin::AddMenu');

class MortgageCenter_Admin {
	static function AddMenu() {
		add_options_page('Mortgage Center Options', 'Mortgage Center', 'manage_options', 'mortgage-center-options', 'MortgageCenter_Admin::Initialize');
	}
	static function SavePostedData() {
		update_option('mortgage-center-state', $_POST['mc-state']);
		update_option('mortgage-center-url-slug', $_POST['mc-url-slug']);
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
		
		$state = get_option('mortgage-center-state');
		$url_slug = get_option('mortgage-center-url-slug');
?>
		<form method="post">
			<table class="form-table">
				<tr>
					<th>
						<label for="mc-url-slug">State to load data for</label>
					</th>
					<td>
						<select id="mc-state" name="mc-state">
							<option value="AL">Alabama</option>
							<option value="AK">Alaska</option>
							<option value="AZ">Arizona</option>
							<option value="AR">Arkansas</option>
							<option value="CA">California</option>
							<option value="CO">Colorado</option>
							<option value="CT">Connecticut</option>
							<option value="DE">Delaware</option>
							<option value="DC">Dist of Columbia</option>
							<option value="FL">Florida</option>
							<option value="GA">Georgia</option>
							<option value="HI">Hawaii</option>
							<option value="ID">Idaho</option>
							<option value="IL">Illinois</option>
							<option value="IN">Indiana</option>
							<option value="IA">Iowa</option>
							<option value="KS">Kansas</option>
							<option value="KY">Kentucky</option>
							<option value="LA">Louisiana</option>
							<option value="ME">Maine</option>
							<option value="MD">Maryland</option>
							<option value="MA">Massachusetts</option>
							<option value="MI">Michigan</option>
							<option value="MN">Minnesota</option>
							<option value="MS">Mississippi</option>
							<option value="MO">Missouri</option>
							<option value="MT">Montana</option>
							<option value="NE">Nebraska</option>
							<option value="NV">Nevada</option>
							<option value="NH">New Hampshire</option>
							<option value="NJ">New Jersey</option>
							<option value="NM">New Mexico</option>
							<option value="NY">New York</option>
							<option value="NC">North Carolina</option>
							<option value="ND">North Dakota</option>
							<option value="OH">Ohio</option>
							<option value="OK">Oklahoma</option>
							<option value="OR">Oregon</option>
							<option value="PA">Pennsylvania</option>
							<option value="RI">Rhode Island</option>
							<option value="SC">South Carolina</option>
							<option value="SD">South Dakota</option>
							<option value="TN">Tennessee</option>
							<option value="TX">Texas</option>
							<option value="UT">Utah</option>
							<option value="VT">Vermont</option>
							<option value="VA">Virginia</option>
							<option value="WA">Washington</option>
							<option value="WV">West Virginia</option>
							<option value="WI">Wisconsin</option>
							<option value="WY">Wyoming</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label for="mc-url-slug">Plugin URL</label>
					</th>
					<td>
						<?= get_bloginfo('wpurl') ?>/<input type="text" id="mc-url-slug" name="mc-url-slug" value="<?= $url_slug ?>" />
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" name="Submit" value="Submit" />
			</p>
		</form>
	</div>
	<script>
		jQuery('#mc-state').val('<?= $state ?>');
	</script>
<?
	}
}
?>