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
	private $enabled_actions;

	/**
	 * コンストラクタ
	 */
	private function __construct() {
		// 全てのバルクアクションをデフォルトで有効にする
		$this->enabled_actions = array(
			'migrate_title'            => true,
			'migrate_content'          => true,
			'migrate_thumbnail'        => true,
			'replace_slug_with_id'     => true,
			'assign_custom_type_terms' => true,
			'migrate_all'              => true,
			'delete_custom_title'      => true,
			'delete_custom_body'       => true,
			'delete_custom_plain_text' => true,
			'delete_custom_thumbnail'  => true,
			'delete_custom_type'       => true,
		);

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * インスタンスを取得
	 *
	 * @return CustomBulkActionAdmin インスタンス
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 管理メニューを追加
	 */
	public function add_admin_menu() {
		add_options_page(
			'Custom Bulk Action Settings',
			'Custom Bulk Action',
			'manage_options',
			'custom-bulk-action',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * 管理ページを作成
	 */
	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Custom Bulk Action Settings', 'custom-bulk-action' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'custom_bulk_action_group' );
				do_settings_sections( 'custom-bulk-action' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * 設定を登録
	 */
	public function register_settings() {
		register_setting(
			'custom_bulk_action_group',
			'custom_bulk_action_settings',
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'custom_bulk_action_section',
			esc_html__( 'Enable/Disable Bulk Actions', 'custom-bulk-action' ),
			null,
			'custom-bulk-action'
		);

		foreach ( $this->enabled_actions as $action => $enabled ) {
			add_settings_field(
				$action,
				esc_html( ucwords( str_replace( '_', ' ', $action ) ) ),
				array( $this, 'create_checkbox_field' ),
				'custom-bulk-action',
				'custom_bulk_action_section',
				array( 'action' => $action )
			);
		}
	}

	/**
	 * チェックボックスフィールドを作成
	 *
	 * @param array $args フィールドの引数
	 */
	public function create_checkbox_field( $args ) {
		$options = get_option( 'custom_bulk_action_settings' );
		?>
		<input type="checkbox" id="<?php echo esc_attr( $args['action'] ); ?>" name="custom_bulk_action_settings[<?php echo esc_attr( $args['action'] ); ?>]" value="1" <?php checked( isset( $options[ $args['action'] ] ) ? $options[ $args['action'] ] : $this->enabled_actions[ $args['action'] ], 1 ); ?> />
		<?php
	}

	/**
	 * 設定値をサニタイズ
	 *
	 * @param array $input 入力値
	 * @return array サニタイズされた値
	 */
	public function sanitize_settings( $input ) {
		$new_input = array();

		foreach ( $this->enabled_actions as $action => $enabled ) {
			if ( isset( $input[ $action ] ) ) {
				$new_input[ $action ] = absint( $input[ $action ] );
			} else {
				$new_input[ $action ] = 0;
			}
		}

		return $new_input;
	}

	/**
	 * 有効なバルクアクションを取得
	 *
	 * @return array 有効なバルクアクション
	 */
	public function get_enabled_actions() {
		$options = get_option( 'custom_bulk_action_settings', $this->enabled_actions );
		return array_merge( $this->enabled_actions, $options );
	}
}
