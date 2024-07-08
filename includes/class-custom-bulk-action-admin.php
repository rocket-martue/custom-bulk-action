<?php
/**
 * CustomBulkActionAdminクラス
 *
 * @package CustomBulkAction
 */

namespace CustomBulkAction;

/**
 * プラグインの管理画面を作成するクラス
 */
class CustomBulkActionAdmin {
	/**
	 * インスタンスを保持する
	 *
	 * @var CustomBulkActionAdmin|null
	 */
	private static $instance = null;

	/**
	 * 有効なバルクアクション
	 *
	 * @var array
	 */
	private $bulk_actions = array(
		'migrate_title'            => 'タイトルを移植',
		'migrate_content'          => 'コンテンツを移植',
		'migrate_thumbnail'        => 'アイキャッチ画像を移植',
		'replace_slug_with_id'     => 'スラッグを投稿IDに置き換える',
		'assign_custom_type_terms' => 'type の値をタクソノミーに登録',
		'migrate_all'              => 'すべてのカスタムフィールドを移植',
		'delete_custom_title'      => 'カスタムタイトルを削除',
		'delete_custom_body'       => 'カスタム本文を削除',
		'delete_custom_plain_text' => 'カスタムプレーンテキストを削除',
		'delete_custom_thumbnail'  => 'カスタムサムネイルを削除',
		'delete_custom_type'       => 'カスタムフィールド type を削除',
	);

	/**
	 * コンストラクタ
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * インスタンスを取得する
	 *
	 * @return CustomBulkActionAdmin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 管理メニューを追加する
	 */
	public function add_admin_menu() {
		add_options_page(
			esc_html__( 'Custom Bulk Action Settings', 'custom-bulk-action' ),
			esc_html__( 'Custom Bulk Action', 'custom-bulk-action' ),
			'manage_options',
			'custom-bulk-action',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * 設定を登録する
	 */
	public function register_settings() {
		register_setting( 'custom_bulk_action_settings', 'custom_bulk_action_enabled' );
	}

	/**
	 * 管理画面を作成する
	 */
	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Custom Bulk Action Settings', 'custom-bulk-action' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'custom_bulk_action_settings' ); ?>
				<?php do_settings_sections( 'custom_bulk_action_settings' ); ?>
				<table class="form-table">
					<?php foreach ( $this->bulk_actions as $action => $label ) : ?>
						<tr valign="top">
							<th scope="row"><?php echo esc_html( $label ); ?></th>
							<td>
								<input type="checkbox" name="custom_bulk_action_enabled[<?php echo esc_attr( $action ); ?>]" value="1" <?php checked( 1, get_option( 'custom_bulk_action_enabled' )[ $action ], true ); ?> />
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
