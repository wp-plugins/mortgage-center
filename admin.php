<?
add_action('admin_menu', 'MortgageCenter_Admin::AddMenu');

class MortgageCenter_Admin {
	function AddMenu() {
		add_options_page('Mortgage Center Options', 'Mortgage Center', 'manage_options', 'mortgage-center-options', 'MortgageCenter_Admin::Initialize');
	}
	function SavePostedData() {
		update_option('mortgage-center-state', $_POST['mc-state']);
		update_option('mortgage-center-url-slug', $_POST['mc-url-slug']);
	}
	function Initialize() {
		if (isset($_POST['Submit']))
			SavePostedData();
		
		$state = get_option('mortgage-center-state');
		$url_slug = get_option('mortgage-center-url-slug');
		
?>
	<div class="wrap">
		<h2>Mortgage Center Options</h2>
		
		<form method="post">
			<ul>
				<li>
					<label for="mc-url-slug">State to load data for</label>
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
				</li>
			</ul>
			<ul>
				<li>
					<label for="mc-url-slug">URL to load plugin</label>
					<?= get_bloginfo('wpurl') ?><input type="text" id="mc-url-slug" name="url-slug" value="{$url_slug}" />
				</li>
			</ul>
			<input type="primary-button" name="Submit" value="Submit" />
		</form>
	</div>
	<script>
		$('#mc-state').val('{$state}');
	</script>
<?
	}
}
?>