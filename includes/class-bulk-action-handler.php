<?php
/**
 * BulkActionHandlerクラス
 *
 * @package CustomBulkAction
 */

namespace CustomBulkAction;

/**
 * バルクアクションを処理するクラス
 */
class BulkActionHandler {
	/**
	 * タイトルを移植する
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function migrate_title( $post_ids ) {
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
	 * コンテンツを移植する
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function migrate_content( $post_ids ) {
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
	 * サムネイルを移植する
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function migrate_thumbnail( $post_ids ) {
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
	public static function replace_slug_with_id( $post_ids ) {
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
	 * すべてのカスタムフィールドを移植する
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function migrate_all( $post_ids ) {
		self::migrate_title( $post_ids );
		self::migrate_content( $post_ids );
		self::migrate_thumbnail( $post_ids );
	}

	/**
	 * カスタムタイトルを削除する
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function delete_custom_title( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			delete_post_meta( $post_id, 'title2' );
		}
	}

	/**
	 * カスタム本文を削除する
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function delete_custom_body( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			delete_post_meta( $post_id, 'body' );
		}
	}

	/**
	 * カスタムプレーンテキストを削除する
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function delete_custom_plain_text( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			delete_post_meta( $post_id, 'plain_text' );
		}
	}

	/**
	 * カスタムサムネイルを削除する
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function delete_custom_thumbnail( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			delete_post_meta( $post_id, 'thumbnail' );
		}
	}
}
