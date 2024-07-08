<?php
/**
 * CustomBulkActionクラス
 *
 * @package CustomBulkAction
 */

namespace CustomBulkAction;

/**
 * プラグインのメインクラス
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
		add_filter( 'bulk_actions-edit-announcement', array( $this, 'register_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-announcement', array( $this, 'handle_bulk_action' ), 10, 3 );
	}

	/**
	 * インスタンスを取得
	 *
	 * @return CustomBulkAction インスタンス
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * バルクアクションを登録
	 *
	 * @param array $bulk_actions 現在のバルクアクション
	 * @return array 変更後のバルクアクション
	 */
	public function register_bulk_actions( $bulk_actions ) {
		$enabled_actions = CustomBulkActionAdmin::get_instance()->get_enabled_actions();

		if ( true === isset( $enabled_actions['migrate_title'] ) && $enabled_actions['migrate_title'] ) {
			$bulk_actions['migrate_title'] = esc_html__( 'タイトルを移植', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['migrate_content'] ) && $enabled_actions['migrate_content'] ) {
			$bulk_actions['migrate_content'] = esc_html__( 'コンテンツを移植', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['migrate_thumbnail'] ) && $enabled_actions['migrate_thumbnail'] ) {
			$bulk_actions['migrate_thumbnail'] = esc_html__( 'アイキャッチ画像を移植', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['replace_slug_with_id'] ) && $enabled_actions['replace_slug_with_id'] ) {
			$bulk_actions['replace_slug_with_id'] = esc_html__( 'スラッグを投稿IDに置き換える', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['assign_custom_type_terms'] ) && $enabled_actions['assign_custom_type_terms'] ) {
			$bulk_actions['assign_custom_type_terms'] = esc_html__( 'type の値をタクソノミーに登録', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['migrate_all'] ) && $enabled_actions['migrate_all'] ) {
			$bulk_actions['migrate_all'] = esc_html__( 'すべてのカスタムフィールドを移植', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['delete_custom_title'] ) && $enabled_actions['delete_custom_title'] ) {
			$bulk_actions['delete_custom_title'] = esc_html__( 'カスタムタイトルを削除', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['delete_custom_body'] ) && $enabled_actions['delete_custom_body'] ) {
			$bulk_actions['delete_custom_body'] = esc_html__( 'カスタム本文を削除', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['delete_custom_plain_text'] ) && $enabled_actions['delete_custom_plain_text'] ) {
			$bulk_actions['delete_custom_plain_text'] = esc_html__( 'カスタムプレーンテキストを削除', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['delete_custom_thumbnail'] ) && $enabled_actions['delete_custom_thumbnail'] ) {
			$bulk_actions['delete_custom_thumbnail'] = esc_html__( 'カスタムサムネイルを削除', 'custom-bulk-action' );
		}
		if ( true === isset( $enabled_actions['delete_custom_type'] ) && $enabled_actions['delete_custom_type'] ) {
			$bulk_actions['delete_custom_type'] = esc_html__( 'カスタムフィールド type を削除', 'custom-bulk-action' );
		}

		return $bulk_actions;
	}

	/**
	 * バルクアクションを処理
	 *
	 * @param string $redirect_to リダイレクト先のURL
	 * @param string $doaction 実行するアクション
	 * @param array $post_ids 投稿IDの配列
	 * @return string リダイレクト先のURL
	 */
	public function handle_bulk_action( $redirect_to, $doaction, $post_ids ) {
		$enabled_actions = CustomBulkActionAdmin::get_instance()->get_enabled_actions();

		if ( true === isset( $enabled_actions[ $doaction ] ) && $enabled_actions[ $doaction ] ) {
			check_admin_referer( 'bulk-posts' );
			call_user_func( array( 'CustomBulkAction\BulkActionHandler', $doaction ), $post_ids );
			$redirect_to = add_query_arg( 'bulk_action_success', count( $post_ids ), $redirect_to );
		}

		return $redirect_to;
	}
}
