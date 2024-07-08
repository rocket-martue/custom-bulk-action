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
	 * カスタムフィールドのタイトルを移植
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
	 * カスタムフィールドのコンテンツを移植
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function migrate_content( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			$custom_content = get_post_meta( $post_id, 'body', true );
			$plain_text     = get_post_meta( $post_id, 'plain_text', true );
			if ( $custom_content ) {
				wp_update_post(
					array(
						'ID'           => $post_id,
						'post_content' => $custom_content . $plain_text,
					)
				);
			}
		}
	}

	/**
	 * カスタムフィールドのサムネイルを移植
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
	 * カスタムフィールド 'type' の値をタクソノミー 'type' に登録
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function assign_custom_type_terms( $post_ids ) {
		$valid_terms = array( 'furisode', 'kimono' );
		foreach ( $post_ids as $post_id ) {
			$custom_field_value = get_post_meta( $post_id, 'type', true );
			if ( in_array( $custom_field_value, $valid_terms, true ) ) {
				if ( ! term_exists( $custom_field_value, 'type' ) ) {
					wp_insert_term( $custom_field_value, 'type' );
				}
				wp_set_object_terms( $post_id, $custom_field_value, 'type' );
			}
		}
	}

	/**
	 * 全てのカスタムフィールドを移植
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function migrate_all( $post_ids ) {
		self::migrate_title( $post_ids );
		self::migrate_content( $post_ids );
		self::migrate_thumbnail( $post_ids );
		self::assign_custom_type_terms( $post_ids );
	}

	/**
	 * カスタムフィールド 'title2' を削除
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function delete_custom_title( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			delete_post_meta( $post_id, 'title2' );
		}
	}

	/**
	 * カスタムフィールド 'body' を削除
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function delete_custom_body( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			delete_post_meta( $post_id, 'body' );
		}
	}

	/**
	 * カスタムフィールド 'plain_text' を削除
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function delete_custom_plain_text( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			delete_post_meta( $post_id, 'plain_text' );
		}
	}

	/**
	 * カスタムフィールド 'thumbnail' を削除
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function delete_custom_thumbnail( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			delete_post_meta( $post_id, 'thumbnail' );
		}
	}

	/**
	 * カスタムフィールド 'type' を削除
	 *
	 * @param array $post_ids 投稿IDの配列
	 */
	public static function delete_custom_type( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			delete_post_meta( $post_id, 'type' );
		}
	}
}
