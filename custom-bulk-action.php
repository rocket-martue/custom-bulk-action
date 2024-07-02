<?php
/**
 * Plugin Name: Custom Bulk Action
 * Description: 各カスタム投稿タイプにカスタムバルクアクションを追加します。
 * Version: 1.0.0
 * Author: Rocket Martue
 * Text Domain: custom-bulk-action
 *
 * @package CustomBulkAction
 */

// セキュリティ対策として直接アクセスを禁止
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CustomBulkAction class
 */
class CustomBulkAction {
	/**
	 * コンストラクタ
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_bulk_actions' ) );
		add_action( 'admin_action_migrate_title', array( $this, 'handle_migrate_title' ) );
		add_action( 'admin_action_migrate_content', array( $this, 'handle_migrate_content' ) );
		add_action( 'admin_action_migrate_thumbnail', array( $this, 'handle_migrate_thumbnail' ) );
		add_action( 'admin_action_replace_slug_with_id', array( $this, 'handle_replace_slug_with_id' ) );
		add_action( 'admin_action_migrate_all', array( $this, 'handle_migrate_all' ) );
	}

	/**
	 * カスタム投稿タイプの取得
	 *
	 * @return array カスタム投稿タイプの配列
	 */
	private function get_custom_post_types() {
		$args = array(
			'public'   => true,
			'_builtin' => false,
		);
		return get_post_types( $args, 'names' );
	}

	/**
	 * バルクアクションを登録
	 */
	public function register_bulk_actions() {
		$post_types = $this->get_custom_post_types();
		foreach ( $post_types as $post_type ) {
			add_filter( "bulk_actions-edit-{$post_type}", array( $this, 'add_bulk_actions' ) );
			add_filter( "handle_bulk_actions-edit-{$post_type}", array( $this, 'handle_bulk_action' ), 10, 3 );
		}
	}

	/**
	 * バルクアクションを追加
	 *
	 * @param array $bulk_actions 既存のバルクアクション
	 * @return array 更新されたバルクアクション
	 */
	public function add_bulk_actions( $bulk_actions ) {
		$bulk_actions['migrate_title']        = __( 'タイトルを移植', 'custom-bulk-action' );
		$bulk_actions['migrate_content']      = __( 'コンテンツを移植', 'custom-bulk-action' );
		$bulk_actions['migrate_thumbnail']    = __( 'アイキャッチ画像を移植', 'custom-bulk-action' );
		$bulk_actions['replace_slug_with_id'] = __( 'スラッグを投稿IDに置き換える', 'custom-bulk-action' );
		$bulk_actions['migrate_all']          = __( 'すべてのカスタムフィールドを移植', 'custom-bulk-action' );
		return $bulk_actions;
	}

	/**
	 * バルクアクションを処理
	 *
	 * @param string $redirect_to リダイレクト先のURL
	 * @param string $doaction 実行するアクション
	 * @param array  $post_ids 投稿IDの配列
	 * @return string リダイレクト先のURL
	 */
	public function handle_bulk_action( $redirect_to, $doaction, $post_ids ) {
		if ( ! $post_ids ) {
			return $redirect_to;
		}

		$action = sanitize_text_field( $doaction );

		switch ( $action ) {
			case 'migrate_title':
				$this->migrate_title( $post_ids );
				$redirect_to = add_query_arg( 'bulk_migrated_title', count( $post_ids ), $redirect_to );
				break;
			case 'migrate_content':
				$this->migrate_content( $post_ids );
				$redirect_to = add_query_arg( 'bulk_migrated_content', count( $post_ids ), $redirect_to );
				break;
			case 'migrate_thumbnail':
				$this->migrate_thumbnail( $post_ids );
				$redirect_to = add_query_arg( 'bulk_migrated_thumbnail', count( $post_ids ), $redirect_to );
				break;
			case 'replace_slug_with_id':
				$this->replace_slug_with_id( $post_ids );
				$redirect_to = add_query_arg( 'bulk_replaced_slug', count( $post_ids ), $redirect_to );
				break;
			case 'migrate_all':
				$this->migrate_all( $post_ids );
				$redirect_to = add_query_arg( 'bulk_migrated_all', count( $post_ids ), $redirect_to );
				break;
		}

		return $redirect_to;
	}

	/**
	 * タイトルを移植
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	private function migrate_title( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			$custom_title = get_post_meta( $post_id, 'title2', true );

			if ( $custom_title ) {
				wp_update_post(
					array(
						'ID'         => $post_id,
						'post_title' => $custom_title,
					)
				);
			}
		}
	}

	/**
	 * コンテンツを移植
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	private function migrate_content( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			$_body      = get_post_meta( $post_id, 'body', true );
			$plain_text = get_post_meta( $post_id, 'plain_text', true );

			if ( $plain_text ) {
				$custom_content = $_body . $plain_text;
			} else {
				$custom_content = $_body;
			}

			if ( $custom_content ) {
				wp_update_post(
					array(
						'ID'           => $post_id,
						'post_content' => $custom_content,
					)
				);
			}
		}
	}

	/**
	 * アイキャッチ画像を移植
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	private function migrate_thumbnail( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			$custom_thumbnail = get_post_meta( $post_id, 'thumbnail', true );

			if ( $custom_thumbnail ) {
				set_post_thumbnail( $post_id, $custom_thumbnail );
			}
		}
	}

	/**
	 * スラッグを投稿IDに置き換える
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	private function replace_slug_with_id( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			wp_update_post(
				array(
					'ID'        => $post_id,
					'post_name' => $post_id,
				)
			);
		}
	}

	/**
	 * すべてのカスタムフィールドを移植
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	private function migrate_all( $post_ids ) {
		$this->migrate_title( $post_ids );
		$this->migrate_content( $post_ids );
		$this->migrate_thumbnail( $post_ids );
	}

	/**
	 * バルクアクションのタイトル移植処理
	 */
	public function handle_migrate_title() {
		if ( isset( $_REQUEST['post'] ) ) {
			$post_ids = array_map( 'intval', $_REQUEST['post'] );
			$this->migrate_title( $post_ids );
		}
	}

	/**
	 * バルクアクションのコンテンツ移植処理
	 */
	public function handle_migrate_content() {
		if ( isset( $_REQUEST['post'] ) ) {
			$post_ids = array_map( 'intval', $_REQUEST['post'] );
			$this->migrate_content( $post_ids );
		}
	}

	/**
	 * バルクアクションのサムネイル移植処理
	 */
	public function handle_migrate_thumbnail() {
		if ( isset( $_REQUEST['post'] ) ) {
			$post_ids = array_map( 'intval', $_REQUEST['post'] );
			$this->migrate_thumbnail( $post_ids );
		}
	}

	/**
	 * バルクアクションのスラッグ置き換え処理
	 */
	public function handle_replace_slug_with_id() {
		if ( isset( $_REQUEST['post'] ) ) {
			$post_ids = array_map( 'intval', $_REQUEST['post'] );
			$this->replace_slug_with_id( $post_ids );
		}
	}

	/**
	 * バルクアクションのすべてのカスタムフィールド移植処理
	 */
	public function handle_migrate_all() {
		if ( isset( $_REQUEST['post'] ) ) {
			$post_ids = array_map( 'intval', $_REQUEST['post'] );
			$this->migrate_all( $post_ids );
		}
	}
}

// プラグインの初期化
new CustomBulkAction();
