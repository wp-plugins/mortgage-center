<?
add_action('admin_menu', 'MortgageCenter_Admin::AddMenu');

class MortgageCenter_Admin {
	function AddMenu() {
		add_options_page('Mortgage Center Options', 'Mortgage Center', 'manage_options', 'mortgage-center-options', 'MortgageCenter_Admin::ShowOptions');
	}
	function ShowOptions() {
?>
	<div class="wrap">
		<h2>Mortgage Center Options</h2>
		
		<form method="post">
			<ul>
				<li>
					<label for="mc-url-slug">URL to load plugin</label>
					<?= get_bloginfo('wpurl') ?><input type="text" id="mc-url-slug" name="url-slug" />
				</li>
			</ul>
		</form>
	</div>
<?
	}
}
?>