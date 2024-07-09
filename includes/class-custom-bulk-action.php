<?php
/**
 * CustomBulkActionクラス
 *
 * @package CustomBulkAction
 */

namespace CustomBulkAction;

/**
 * カスタムバルクアクションのクラス
 */
class CustomBulkAction {
	/**
	 * インスタンスを保持する
	 *
	 * @var CustomBulkAction|null
	 */
	private static $instance = null;

	/**
	 * コンストラクタ
	 */
	private function __construct() {
		add_action( 'admin_init', array( $this, 'register_bulk_actions' ) );
	}

	/**
	 * インスタンスを取得する
	 *
	 * @return CustomBulkAction
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * バルクアクションを登録する
	 */
	public function register_bulk_actions() {
		$post_types = $this->get_custom_post_types();
		foreach ( $post_types as $post_type ) {
			add_filter( "bulk_actions-edit-{$post_type}", array( $this, 'add_bulk_actions' ) );
			add_filter( "handle_bulk_actions-edit-{$post_type}", array( $this, 'handle_bulk_action' ), 10, 3 );
		}
	}

	/**
	 * カスタム投稿タイプを取得する
	 *
	 * @return array
	 */
	private function get_custom_post_types() {
		$args = array(
			'public'   => true,
			'_builtin' => false,
		);
		return get_post_types( $args, 'names' );
	}

	/**
	 * バルクアクションを追加する
	 *
	 * @param array $bulk_actions バルクアクションのリスト
	 * @return array
	 */
	public function add_bulk_actions( $bulk_actions ) {
		$enabled_actions = get_option( 'custom_bulk_action_enabled', array() );

		if ( isset( $enabled_actions['migrate_title'] ) && $enabled_actions['migrate_title'] ) {
			$bulk_actions['migrate_title'] = esc_html__( 'タイトルを移植', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['migrate_content'] ) && $enabled_actions['migrate_content'] ) {
			$bulk_actions['migrate_content'] = esc_html__( 'コンテンツを移植', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['migrate_thumbnail'] ) && $enabled_actions['migrate_thumbnail'] ) {
			$bulk_actions['migrate_thumbnail'] = esc_html__( 'アイキャッチ画像を移植', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['replace_slug_with_id'] ) && $enabled_actions['replace_slug_with_id'] ) {
			$bulk_actions['replace_slug_with_id'] = esc_html__( 'スラッグを投稿IDに置き換える', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['assign_custom_type_terms'] ) && $enabled_actions['assign_custom_type_terms'] ) {
			$bulk_actions['assign_custom_type_terms'] = esc_html__( 'type の値をタクソノミーに登録', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['migrate_all'] ) && $enabled_actions['migrate_all'] ) {
			$bulk_actions['migrate_all'] = esc_html__( 'すべてのカスタムフィールドを移植', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['delete_custom_title'] ) && $enabled_actions['delete_custom_title'] ) {
			$bulk_actions['delete_custom_title'] = esc_html__( 'カスタムタイトルを削除', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['delete_custom_body'] ) && $enabled_actions['delete_custom_body'] ) {
			$bulk_actions['delete_custom_body'] = esc_html__( 'カスタム本文を削除', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['delete_custom_plain_text'] ) && $enabled_actions['delete_custom_plain_text'] ) {
			$bulk_actions['delete_custom_plain_text'] = esc_html__( 'カスタムプレーンテキストを削除', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['delete_custom_thumbnail'] ) && $enabled_actions['delete_custom_thumbnail'] ) {
			$bulk_actions['delete_custom_thumbnail'] = esc_html__( 'カスタムサムネイルを削除', 'custom-bulk-action' );
		}
		if ( isset( $enabled_actions['delete_custom_type'] ) && $enabled_actions['delete_custom_type'] ) {
			$bulk_actions['delete_custom_type'] = esc_html__( 'カスタムフィールド type を削除', 'custom-bulk-action' );
		}

		return $bulk_actions;
	}

	/**
	 * バルクアクションを処理する
	 *
	 * @param string $redirect_to リダイレクト先のURL
	 * @param string $doaction 実行するアクション
	 * @param array $post_ids 投稿IDの配列
	 * @return string リダイレクト先のURL
	 */
	public function handle_bulk_action( $redirect_to, $doaction, $post_ids ) {
		if ( ! check_admin_referer( 'bulk-posts' ) ) {
			return $redirect_to;
		}

		switch ( $doaction ) {
			case 'migrate_title':
				BulkActionHandler::migrate_title( $post_ids );
				break;
			case 'migrate_content':
				BulkActionHandler::migrate_content( $post_ids );
				break;
			case 'migrate_thumbnail':
				BulkActionHandler::migrate_thumbnail( $post_ids );
				break;
			case 'replace_slug_with_id':
				BulkActionHandler::replace_slug_with_id( $post_ids );
				break;
			case 'assign_custom_type_terms':
				BulkActionHandler::assign_custom_type_terms( $post_ids );
				break;
			case 'migrate_all':
				BulkActionHandler::migrate_all( $post_ids );
				break;
			case 'delete_custom_title':
				BulkActionHandler::delete_custom_title( $post_ids );
				break;
			case 'delete_custom_body':
				BulkActionHandler::delete_custom_body( $post_ids );
				break;
			case 'delete_custom_plain_text':
				BulkActionHandler::delete_custom_plain_text( $post_ids );
				break;
			case 'delete_custom_thumbnail':
				BulkActionHandler::delete_custom_thumbnail( $post_ids );
				break;
			case 'delete_custom_type':
				BulkActionHandler::delete_custom_type( $post_ids );
				break;
		}

		return $redirect_to;
	}
}
