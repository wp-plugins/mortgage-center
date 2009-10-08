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
		
		global $MortgageCenter_States;
		$saved_state = get_option('mortgage-center-state');
		$saved_url_slug = get_option('mortgage-center-url-slug');
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