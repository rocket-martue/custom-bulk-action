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
		$bulk_actions['migrate_title']            = __( 'タイトルを移植', 'custom-bulk-action' );
		$bulk_actions['migrate_content']          = __( 'コンテンツを移植', 'custom-bulk-action' );
		$bulk_actions['migrate_thumbnail']        = __( 'アイキャッチ画像を移植', 'custom-bulk-action' );
		$bulk_actions['replace_slug_with_id']     = __( 'スラッグを投稿IDに置き換える', 'custom-bulk-action' );
		$bulk_actions['migrate_all']              = __( 'すべてのカスタムフィールドを移植', 'custom-bulk-action' );
		$bulk_actions['delete_custom_title']      = __( 'カスタムタイトルを削除', 'custom-bulk-action' );
		$bulk_actions['delete_custom_body']       = __( 'カスタム本文を削除', 'custom-bulk-action' );
		$bulk_actions['delete_custom_plain_text'] = __( 'カスタムプレーンテキストを削除', 'custom-bulk-action' );
		$bulk_actions['delete_custom_thumbnail']  = __( 'カスタムサムネイルを削除', 'custom-bulk-action' );
		return $bulk_actions;
	}

	/**
	 * バルクアクションを処理する
	 *
	 * @param string $redirect_to リダイレクト先URL
	 * @param string $doaction 実行するアクション
	 * @param array $post_ids 投稿IDの配列
	 * @return string
	 */
	public function handle_bulk_action( $redirect_to, $doaction, $post_ids ) {
		if ( empty( $post_ids ) ) {
			return $redirect_to;
		}

		$action = sanitize_text_field( $doaction );

		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'bulk-posts' ) ) {
			return $redirect_to;
		}

		switch ( $action ) {
			case 'migrate_title':
				BulkActionHandler::migrate_title( $post_ids );
				$redirect_to = add_query_arg( 'bulk_migrated_title', count( $post_ids ), $redirect_to );
				break;
			case 'migrate_content':
				BulkActionHandler::migrate_content( $post_ids );
				$redirect_to = add_query_arg( 'bulk_migrated_content', count( $post_ids ), $redirect_to );
				break;
			case 'migrate_thumbnail':
				BulkActionHandler::migrate_thumbnail( $post_ids );
				$redirect_to = add_query_arg( 'bulk_migrated_thumbnail', count( $post_ids ), $redirect_to );
				break;
			case 'replace_slug_with_id':
				BulkActionHandler::replace_slug_with_id( $post_ids );
				$redirect_to = add_query_arg( 'bulk_replaced_slug', count( $post_ids ), $redirect_to );
				break;
			case 'migrate_all':
				BulkActionHandler::migrate_all( $post_ids );
				$redirect_to = add_query_arg( 'bulk_migrated_all', count( $post_ids ), $redirect_to );
				break;
			case 'delete_custom_title':
				BulkActionHandler::delete_custom_title( $post_ids );
				$redirect_to = add_query_arg( 'bulk_deleted_custom_title', count( $post_ids ), $redirect_to );
				break;
			case 'delete_custom_body':
				BulkActionHandler::delete_custom_body( $post_ids );
				$redirect_to = add_query_arg( 'bulk_deleted_custom_body', count( $post_ids ), $redirect_to );
				break;
			case 'delete_custom_plain_text':
				BulkActionHandler::delete_custom_plain_text( $post_ids );
				$redirect_to = add_query_arg( 'bulk_deleted_custom_plain_text', count( $post_ids ), $redirect_to );
				break;
			case 'delete_custom_thumbnail':
				BulkActionHandler::delete_custom_thumbnail( $post_ids );
				$redirect_to = add_query_arg( 'bulk_deleted_custom_thumbnail', count( $post_ids ), $redirect_to );
				break;
		}

		return $redirect_to;
	}
}
