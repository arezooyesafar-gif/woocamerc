<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Search extends Tarh99_Perf_Module {

	public function id() {
		return 'search';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'بهینه‌سازی جستجوی محصولات';
	}

	public function description() {
		return 'جستجوی محصولات (جستجوی زنده هدر و صفحه نتایج) فقط در ستون‌های انتخاب‌شده انجام می‌شود و SKU دقیق از جدول ایندکس‌دار wc_product_meta_lookup خوانده می‌شود (به‌جای کوئری کند postmeta قالب). فقط روی کوئری‌هایی اعمال می‌شود که نوع پستشان فقط product است. نتایج جستجوی زنده توسط ماژول «کش پاسخ‌های ایجکس» کش می‌شوند.';
	}

	public function risk() {
		return 'medium';
	}

	public function notes() {
		return 'پیش‌فرض: هر سه ستون مثل قبل جستجو می‌شوند تا نتایج هیچ تغییری نکنند. برداشتن تیک «توضیحات کامل» جستجو را چند برابر سریع‌تر می‌کند ولی محصولاتی که کلمه فقط در متن توضیحاتشان آمده دیگر پیدا نمی‌شوند (تست شد: برای کلمه‌ای که فقط در توضیحات ۸ محصول بود، نتیجه از ۸ به ۰ رسید).';
	}

	public function fields() {
		return array(
			'columns' => array(
				'type'    => 'multicheck',
				'label'   => 'ستون‌های جستجو',
				'options' => array(
					'title'   => 'عنوان محصول',
					'excerpt' => 'توضیحات کوتاه',
					'content' => 'توضیحات کامل (کندترین بخش)',
				),
				'default' => array( 'title', 'excerpt', 'content' ),
			),
			'sku'     => array(
				'type'    => 'checkbox',
				'label'   => 'جستجوی SKU دقیق (محصول و والد متغیرها) از جدول lookup',
				'default' => 1,
			),
			'front'   => array(
				'type'    => 'checkbox',
				'label'   => 'اعمال روی صفحه نتایج جستجو',
				'default' => 1,
			),
			'ajax'    => array(
				'type'    => 'checkbox',
				'label'   => 'اعمال روی جستجوی زنده (admin-ajax)',
				'default' => 1,
			),
		);
	}

	public function boot() {
		add_filter( 'posts_search', array( $this, 'posts_search' ), 50, 2 );
	}

	private function product_only( $query ) {
		$type = $query->get( 'post_type' );
		if ( is_array( $type ) ) {
			$type = array_values( array_unique( array_filter( $type ) ) );
			return 1 === count( $type ) && 'product' === $type[0];
		}
		return 'product' === $type;
	}

	public function posts_search( $search, $query ) {
		if ( '' === $search || ! ( $query instanceof WP_Query ) || ! $query->is_search() ) {
			return $search;
		}
		if ( Tarh99_Perf_Env::bypass() || Tarh99_Perf_Env::is_rest() || wp_doing_cron() ) {
			return $search;
		}
		if ( wp_doing_ajax() ) {
			if ( ! $this->opt( 'ajax' ) ) {
				return $search;
			}
		} elseif ( is_admin() || ! $this->opt( 'front' ) ) {
			return $search;
		}
		if ( ! $this->product_only( $query ) ) {
			return $search;
		}
		$terms = $query->get( 'search_terms' );
		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return $search;
		}
		$columns = $this->opt_list( 'columns' );
		if ( empty( $columns ) ) {
			return $search;
		}

		global $wpdb;
		$map = array(
			'title'   => "{$wpdb->posts}.post_title",
			'excerpt' => "{$wpdb->posts}.post_excerpt",
			'content' => "{$wpdb->posts}.post_content",
		);
		$cols = array();
		foreach ( $columns as $column ) {
			if ( isset( $map[ $column ] ) ) {
				$cols[] = $map[ $column ];
			}
		}
		if ( empty( $cols ) ) {
			return $search;
		}

		$exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );
		$n                = $query->get( 'exact' ) ? '' : '%';
		$clauses          = array();
		foreach ( $terms as $term ) {
			$term    = (string) $term;
			$exclude = $exclusion_prefix && '' !== $exclusion_prefix && 0 === strpos( $term, $exclusion_prefix );
			if ( $exclude ) {
				$term = substr( $term, strlen( $exclusion_prefix ) );
			}
			if ( '' === $term ) {
				continue;
			}
			$like  = $n . $wpdb->esc_like( $term ) . $n;
			$parts = array();
			foreach ( $cols as $col ) {
				$parts[] = $wpdb->prepare( $col . ( $exclude ? ' NOT LIKE %s' : ' LIKE %s' ), $like );
			}
			$clauses[] = '(' . implode( $exclude ? ' AND ' : ' OR ', $parts ) . ')';
		}
		if ( empty( $clauses ) ) {
			return $search;
		}
		$where = '(' . implode( ' AND ', $clauses ) . ')';

		$raw = trim( (string) $query->get( 's' ) );
		if ( $this->opt( 'sku' ) && '' !== $raw && strlen( $raw ) <= 100 && ! empty( $wpdb->wc_product_meta_lookup ) ) {
			$lookup = $wpdb->wc_product_meta_lookup;
			$where  = '(' . $where
				. $wpdb->prepare( " OR {$wpdb->posts}.ID IN (SELECT product_id FROM {$lookup} WHERE sku = %s)", $raw )
				. $wpdb->prepare( " OR {$wpdb->posts}.ID IN (SELECT tarh99_v.post_parent FROM {$wpdb->posts} AS tarh99_v INNER JOIN {$lookup} AS tarh99_l ON tarh99_l.product_id = tarh99_v.ID WHERE tarh99_l.sku = %s AND tarh99_v.post_type = 'product_variation')", $raw )
				. ')';
		}

		$search = " AND {$where} ";
		if ( ! is_user_logged_in() ) {
			$search .= " AND ({$wpdb->posts}.post_password = '') ";
		}
		return $search;
	}
}
