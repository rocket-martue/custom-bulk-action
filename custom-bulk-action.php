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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * クラスファイルの読み込み
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-bulk-action.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-bulk-action-handler.php';

/**
 * 初期化関数の呼び出し
 */
function custom_bulk_action_init() {
	CustomBulkAction\CustomBulkAction::get_instance();
}
add_action( 'plugins_loaded', 'custom_bulk_action_init' );
