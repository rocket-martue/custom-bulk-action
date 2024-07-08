<?php
/**
 * Plugin Name: Custom Bulk Action
 * Description: 各カスタム投稿タイプにカスタムバルクアクションを追加します。
 * Version: 1.1.1
 * Author: Rocket Martue
 * Text Domain: custom-bulk-action
 * @package CustomBulkAction
 */

// 必要なファイルを読み込む
require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-bulk-action.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-bulk-action-admin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-bulk-action-handler.php';

// プラグインを初期化する
add_action(
	'plugins_loaded',
	function () {
		CustomBulkAction\CustomBulkAction::get_instance();
		CustomBulkAction\CustomBulkActionAdmin::get_instance();
	}
);
