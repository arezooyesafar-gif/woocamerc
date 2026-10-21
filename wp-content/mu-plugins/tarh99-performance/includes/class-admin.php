<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Tarh99_Perf_Admin {

	const SLUG = 'tarh99-performance';

	private $plugin;

	public function __construct( Tarh99_Perf_Plugin $plugin ) {
		$this->plugin = $plugin;
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_post_tarh99_perf_save', array( $this, 'save' ) );
		add_action( 'admin_post_tarh99_perf_tool', array( $this, 'tool' ) );
	}

	public function menu() {
		add_menu_page( 'سرعت طرح۹۹', 'سرعت طرح۹۹', 'manage_options', self::SLUG, array( $this, 'render' ), 'dashicons-performance', 81 );
	}

	private function groups() {
		return array(
			'front'     => 'فرانت‌اند (PageSpeed و LCP)',
			'server'    => 'سمت سرور',
			'admin'     => 'پیشخوان',
			'stability' => 'پایداری و نگهداری',
		);
	}

	private function url( $tab = 'modules', $args = array() ) {
		return add_query_arg( array_merge( array( 'page' => self::SLUG, 'tab' => $tab ), $args ), admin_url( 'admin.php' ) );
	}

	public function save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'دسترسی ندارید.' );
		}
		check_admin_referer( 'tarh99_perf_save' );
		$posted_modules = isset( $_POST['tarh99_modules'] ) && is_array( $_POST['tarh99_modules'] ) ? wp_unslash( $_POST['tarh99_modules'] ) : array();
		$posted_values  = isset( $_POST['tarh99_values'] ) && is_array( $_POST['tarh99_values'] ) ? wp_unslash( $_POST['tarh99_values'] ) : array();
		$modules        = array();
		$values         = array();
		foreach ( $this->plugin->modules() as $id => $module ) {
			$modules[ $id ] = empty( $posted_modules[ $id ] ) ? 0 : 1;
			$values[ $id ]  = array();
			$raw            = isset( $posted_values[ $id ] ) && is_array( $posted_values[ $id ] ) ? $posted_values[ $id ] : array();
			foreach ( $module->fields() as $field => $def ) {
				$values[ $id ][ $field ] = $module->sanitize_field( $field, isset( $raw[ $field ] ) ? $raw[ $field ] : null );
			}
		}
		Tarh99_Perf_Settings::save( $modules, $values );
		if ( ! Tarh99_Perf_Settings::module_enabled( 'cleanup', true ) && class_exists( 'Tarh99_Perf_Module_Cleanup' ) ) {
			Tarh99_Perf_Module_Cleanup::unschedule();
		}
		Tarh99_Perf_Cache_Store::bump();
		$this->purge_page_cache();
		wp_safe_redirect( $this->url( 'modules', array( 'tarh99_msg' => 'saved' ) ) );
		exit;
	}

	private function purge_page_cache() {
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
	}

	public function tool() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'دسترسی ندارید.' );
		}
		check_admin_referer( 'tarh99_perf_tool' );
		$tool = isset( $_POST['tarh99_tool'] ) ? sanitize_key( wp_unslash( $_POST['tarh99_tool'] ) ) : '';
		$msg  = 'done';
		switch ( $tool ) {
			case 'run_cleanup':
				$cleanup = $this->plugin->module( 'cleanup' );
				if ( $cleanup && $cleanup->enabled() ) {
					$cleanup->run( true );
				} else {
					$msg = 'cleanup_off';
				}
				break;
			case 'flush_cache':
				Tarh99_Perf_Cache_Store::bump();
				Tarh99_Perf_Cache_Store::purge_files( 0 );
				break;
			case 'refresh_metrics':
				$monitor = $this->plugin->module( 'monitor' );
				if ( $monitor ) {
					$monitor->metrics( true );
				}
				break;
			case 'clear_http_log':
				delete_option( 'tarh99_perf_http_log' );
				break;
			case 'clear_scan':
				delete_transient( 'tarh99_perf_scan_' . get_current_user_id() );
				break;
			case 'reset_settings':
				Tarh99_Perf_Settings::reset();
				Tarh99_Perf_Cache_Store::bump();
				$this->purge_page_cache();
				break;
			default:
				$msg = '';
		}
		$tab = isset( $_POST['tarh99_tab'] ) ? sanitize_key( wp_unslash( $_POST['tarh99_tab'] ) ) : 'tools';
		wp_safe_redirect( $this->url( $tab, array( 'tarh99_msg' => $msg ) ) );
		exit;
	}

	private function tool_button( $tool, $label, $tab, $class = 'button', $confirm = '' ) {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline-block;margin:0 0 6px 6px">';
		wp_nonce_field( 'tarh99_perf_tool' );
		echo '<input type="hidden" name="action" value="tarh99_perf_tool">';
		echo '<input type="hidden" name="tarh99_tool" value="' . esc_attr( $tool ) . '">';
		echo '<input type="hidden" name="tarh99_tab" value="' . esc_attr( $tab ) . '">';
		$onclick = '' !== $confirm ? ' onclick="return confirm(\'' . esc_js( $confirm ) . '\');"' : '';
		echo '<button type="submit" class="' . esc_attr( $class ) . '"' . $onclick . '>' . esc_html( $label ) . '</button>';
		echo '</form>';
	}

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'modules';
		$tabs = array(
			'modules' => 'ماژول‌ها',
			'monitor' => 'مانیتورینگ',
			'tools'   => 'ابزارها و لاگ‌ها',
		);
		if ( ! isset( $tabs[ $tab ] ) ) {
			$tab = 'modules';
		}
		echo '<div class="wrap tarh99-perf" dir="rtl">';
		$this->styles();
		echo '<h1>سرعت طرح۹۹ <small>نسخه ' . esc_html( TARH99_PERF_VERSION ) . '</small></h1>';
		$this->messages();
		$this->status_bar();
		echo '<nav class="nav-tab-wrapper">';
		foreach ( $tabs as $key => $label ) {
			echo '<a class="nav-tab' . ( $key === $tab ? ' nav-tab-active' : '' ) . '" href="' . esc_url( $this->url( $key ) ) . '">' . esc_html( $label ) . '</a>';
		}
		echo '</nav>';
		if ( 'monitor' === $tab ) {
			$this->render_monitor();
		} elseif ( 'tools' === $tab ) {
			$this->render_tools();
		} else {
			$this->render_modules();
		}
		echo '</div>';
	}

	private function styles() {
		echo '<style>
.tarh99-perf h1 small{font-size:12px;color:#646970;font-weight:400}
.tarh99-perf .t99-group{margin:24px 0 8px;font-size:16px}
.tarh99-perf .t99-module{background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:12px 16px;margin:0 0 12px}
.tarh99-perf .t99-module.off{opacity:.75;background:#f6f7f7}
.tarh99-perf .t99-head{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.tarh99-perf .t99-head label{font-weight:600;font-size:14px}
.tarh99-perf .t99-badge{font-size:11px;border-radius:10px;padding:1px 8px;background:#f0f0f1;color:#50575e}
.tarh99-perf .t99-badge.medium{background:#fcf0e3;color:#8a4b00}
.tarh99-perf .t99-badge.const{background:#e7f5fe;color:#0a4b78}
.tarh99-perf .t99-desc{color:#50575e;margin:6px 0 8px;line-height:1.9}
.tarh99-perf .t99-note{color:#8a4b00;margin:0 0 8px}
.tarh99-perf .t99-fields{display:grid;gap:8px;padding-top:6px;border-top:1px dashed #dcdcde}
.tarh99-perf .t99-field textarea{width:100%;max-width:820px;min-height:90px;direction:ltr;text-align:left;font-family:monospace}
.tarh99-perf .t99-field input[type=text]{width:100%;max-width:520px;direction:ltr;text-align:left}
.tarh99-perf .t99-field .t99-help{color:#646970;font-size:12px;margin-top:2px}
.tarh99-perf .t99-status{display:flex;flex-wrap:wrap;gap:8px;margin:12px 0}
.tarh99-perf .t99-pill{background:#fff;border:1px solid #dcdcde;border-radius:16px;padding:4px 12px}
.tarh99-perf .t99-pill.bad{border-color:#d63638;color:#8a2424}
.tarh99-perf .t99-pill.good{border-color:#00a32a;color:#00650f}
.tarh99-perf table.t99-table{border-collapse:collapse;background:#fff;margin:8px 0 16px;max-width:100%}
.tarh99-perf table.t99-table td,.tarh99-perf table.t99-table th{border:1px solid #dcdcde;padding:6px 10px;text-align:right;vertical-align:top}
.tarh99-perf table.t99-table td.ltr{direction:ltr;text-align:left;font-family:monospace;font-size:12px;word-break:break-all}
.tarh99-perf .t99-box{background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:12px 16px;margin:12px 0}
</style>';
	}

	private function messages() {
		$msg = isset( $_GET['tarh99_msg'] ) ? sanitize_key( wp_unslash( $_GET['tarh99_msg'] ) ) : '';
		$map = array(
			'saved'       => array( 'success', 'تنظیمات ذخیره شد. کش WP Rocket و کش ایجکس این افزونه پاک شد؛ سایت را در حالت مهمان بررسی کنید.' ),
			'done'        => array( 'success', 'انجام شد.' ),
			'cleanup_off' => array( 'warning', 'ماژول پاکسازی خاموش است.' ),
		);
		if ( isset( $map[ $msg ] ) ) {
			echo '<div class="notice notice-' . esc_attr( $map[ $msg ][0] ) . ' is-dismissible"><p>' . esc_html( $map[ $msg ][1] ) . '</p></div>';
		}
		if ( Tarh99_Perf_Settings::safe_mode() ) {
			echo '<div class="notice notice-error"><p>حالت ایمن (TARH99_PERF_SAFE_MODE) فعال است؛ همه ماژول‌هایی که با ثابت روشن نشده‌اند خاموش‌اند.</p></div>';
		}
		if ( Tarh99_Perf_Env::tps_active() ) {
			echo '<div class="notice notice-info"><p>افزونه قدیمی <strong>Tarh99 Performance Suite</strong> هنوز فعال است. بخش‌هایی که آن افزونه انجام می‌دهد در این افزونه خودکار کنار می‌روند. بعد از بررسی، آن افزونه را غیرفعال کنید تا همه کارها از اینجا انجام شود (راهنما: docs/deploy.md).</p></div>';
		}
	}

	private function status_bar() {
		$items = array();
		$items[] = array( Tarh99_Perf_Env::rocket_active() ? 'good' : 'bad', 'WP Rocket: ' . ( Tarh99_Perf_Env::rocket_active() ? WP_ROCKET_VERSION : 'غیرفعال' ) );
		$items[] = array( wp_using_ext_object_cache() ? 'good' : 'bad', 'Object Cache: ' . ( wp_using_ext_object_cache() ? 'فعال' : 'ندارد' ) );
		$items[] = array( version_compare( PHP_VERSION, '8.2', '>=' ) ? 'good' : 'bad', 'PHP ' . PHP_VERSION );
		if ( Tarh99_Perf_Env::rocket_active() ) {
			$items[] = array( '', 'Delay JS راکت: ' . ( Tarh99_Perf_Env::rocket_option( 'delay_js' ) ? 'روشن' : 'خاموش' ) );
			$items[] = array( '', 'Remove Unused CSS: ' . ( Tarh99_Perf_Env::rocket_option( 'remove_unused_css' ) ? 'روشن' : 'خاموش' ) );
		}
		$items[] = array( '', 'غیرفعال‌سازی موقت برای تست: افزودن ?tarh99_perf=off به آدرس هر صفحه (فقط برای مدیر)' );
		echo '<div class="t99-status">';
		foreach ( $items as $item ) {
			echo '<span class="t99-pill ' . esc_attr( $item[0] ) . '">' . esc_html( $item[1] ) . '</span>';
		}
		echo '</div>';
	}

	private function render_modules() {
		$by_group = array();
		foreach ( $this->plugin->modules() as $id => $module ) {
			$by_group[ $module->group() ][ $id ] = $module;
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'tarh99_perf_save' );
		echo '<input type="hidden" name="action" value="tarh99_perf_save">';
		foreach ( $this->groups() as $group => $label ) {
			if ( empty( $by_group[ $group ] ) ) {
				continue;
			}
			echo '<h2 class="t99-group">' . esc_html( $label ) . '</h2>';
			foreach ( $by_group[ $group ] as $id => $module ) {
				$this->render_module( $id, $module );
			}
		}
		submit_button( 'ذخیره تنظیمات و پاک‌کردن کش' );
		echo '</form>';
	}

	private function render_module( $id, Tarh99_Perf_Module $module ) {
		$enabled  = $module->enabled();
		$constant = Tarh99_Perf_Settings::constant_override( $id );
		$stored   = Tarh99_Perf_Settings::stored_flag( $id );
		$checked  = null === $stored ? $module->default_enabled() : $stored;
		echo '<div class="t99-module' . ( $enabled ? '' : ' off' ) . '">';
		echo '<div class="t99-head">';
		echo '<input type="checkbox" id="t99-m-' . esc_attr( $id ) . '" name="tarh99_modules[' . esc_attr( $id ) . ']" value="1"' . checked( $checked, true, false ) . '>';
		echo '<label for="t99-m-' . esc_attr( $id ) . '">' . esc_html( $module->title() ) . '</label>';
		echo '<span class="t99-badge ' . esc_attr( $module->risk() ) . '">' . ( 'medium' === $module->risk() ? 'ریسک متوسط — بعد از روشن کردن تست کنید' : 'ریسک کم' ) . '</span>';
		echo '<span class="t99-badge">' . ( $enabled ? 'فعال' : 'غیرفعال' ) . '</span>';
		if ( null !== $constant ) {
			echo '<span class="t99-badge const">کنترل با ثابت TARH99_PERF_MODULE_' . esc_html( strtoupper( $id ) ) . '</span>';
		}
		echo '<code style="direction:ltr">' . esc_html( $id ) . '</code>';
		echo '</div>';
		echo '<p class="t99-desc">' . esc_html( $module->description() ) . '</p>';
		if ( '' !== $module->notes() ) {
			echo '<p class="t99-note">' . esc_html( $module->notes() ) . '</p>';
		}
		$fields = $module->fields();
		if ( ! empty( $fields ) ) {
			echo '<div class="t99-fields">';
			foreach ( $fields as $field => $def ) {
				$this->render_field( $id, $field, $def, $module->opt( $field ) );
			}
			echo '</div>';
		}
		echo '</div>';
	}

	private function render_field( $module_id, $field, $def, $value ) {
		$name = 'tarh99_values[' . $module_id . '][' . $field . ']';
		$fid  = 't99-' . $module_id . '-' . $field;
		echo '<div class="t99-field">';
		switch ( $def['type'] ) {
			case 'checkbox':
				echo '<label><input type="checkbox" name="' . esc_attr( $name ) . '" value="1"' . checked( ! empty( $value ), true, false ) . '> ' . esc_html( $def['label'] ) . '</label>';
				break;
			case 'number':
				$step = isset( $def['step'] ) ? $def['step'] : 1;
				echo '<label for="' . esc_attr( $fid ) . '">' . esc_html( $def['label'] ) . '</label> ';
				echo '<input type="number" id="' . esc_attr( $fid ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" step="' . esc_attr( $step ) . '"' . ( isset( $def['min'] ) ? ' min="' . esc_attr( $def['min'] ) . '"' : '' ) . ( isset( $def['max'] ) ? ' max="' . esc_attr( $def['max'] ) . '"' : '' ) . ' style="width:100px">';
				break;
			case 'select':
				echo '<label for="' . esc_attr( $fid ) . '">' . esc_html( $def['label'] ) . '</label> <select id="' . esc_attr( $fid ) . '" name="' . esc_attr( $name ) . '">';
				foreach ( $def['options'] as $key => $label ) {
					echo '<option value="' . esc_attr( $key ) . '"' . selected( (string) $value, (string) $key, false ) . '>' . esc_html( $label ) . '</option>';
				}
				echo '</select>';
				break;
			case 'multicheck':
				$value = is_array( $value ) ? $value : array();
				echo '<div><strong>' . esc_html( $def['label'] ) . '</strong></div>';
				echo '<input type="hidden" name="' . esc_attr( $name ) . '[]" value="">';
				foreach ( $def['options'] as $key => $label ) {
					echo '<label style="display:inline-block;margin-left:16px"><input type="checkbox" name="' . esc_attr( $name ) . '[]" value="' . esc_attr( $key ) . '"' . checked( in_array( (string) $key, array_map( 'strval', $value ), true ), true, false ) . '> ' . esc_html( $label ) . '</label>';
				}
				break;
			case 'textarea':
				echo '<label for="' . esc_attr( $fid ) . '">' . esc_html( $def['label'] ) . '</label>';
				echo '<textarea id="' . esc_attr( $fid ) . '" name="' . esc_attr( $name ) . '" rows="5">' . esc_textarea( (string) $value ) . '</textarea>';
				break;
			default:
				echo '<label for="' . esc_attr( $fid ) . '">' . esc_html( $def['label'] ) . '</label> ';
				echo '<input type="text" id="' . esc_attr( $fid ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '">';
		}
		if ( ! empty( $def['help'] ) ) {
			echo '<div class="t99-help">' . esc_html( $def['help'] ) . '</div>';
		}
		echo '</div>';
	}

	private function kv_table( array $rows ) {
		echo '<table class="t99-table"><tbody>';
		foreach ( $rows as $label => $value ) {
			echo '<tr><th>' . esc_html( $label ) . '</th><td>' . esc_html( (string) $value ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}

	private function render_monitor() {
		$monitor = $this->plugin->module( 'monitor' );
		if ( ! $monitor ) {
			return;
		}
		$m   = $monitor->metrics( false );
		$env = $monitor->environment();
		echo '<div class="t99-box">';
		$this->tool_button( 'refresh_metrics', 'اندازه‌گیری دوباره', 'monitor', 'button button-primary' );
		echo ' <span>آخرین اندازه‌گیری: ' . esc_html( isset( $m['time'] ) ? $m['time'] : '-' ) . '</span>';
		echo '</div>';

		$warnings = $monitor->warnings( $m );
		if ( ! empty( $warnings ) ) {
			echo '<div class="notice notice-warning inline"><ul style="list-style:disc;padding-right:20px">';
			foreach ( $warnings as $warning ) {
				echo '<li>' . esc_html( $warning ) . '</li>';
			}
			echo '</ul></div>';
		}

		echo '<h2>دیتابیس</h2>';
		$as = array();
		if ( ! empty( $m['as_status'] ) ) {
			foreach ( $m['as_status'] as $row ) {
				$as[] = $row['status'] . ': ' . number_format_i18n( (int) $row['c'] );
			}
		}
		$this->kv_table(
			array(
				'حجم کل دیتابیس'                 => ( isset( $m['db_mb'] ) ? $m['db_mb'] : '-' ) . ' MB',
				'autoload'                       => ( isset( $m['autoload_kb'] ) ? $m['autoload_kb'] : '-' ) . ' KB در ' . ( isset( $m['autoload_count'] ) ? number_format_i18n( $m['autoload_count'] ) : '-' ) . ' گزینه',
				'ردیف‌های ترنزینت'               => isset( $m['transients'] ) ? number_format_i18n( $m['transients'] ) . ' (منقضی: ' . number_format_i18n( $m['transients_expired'] ) . ')' : '-',
				'سشن‌های ووکامرس'                => isset( $m['sessions'] ) ? number_format_i18n( $m['sessions'] ) . ' (منقضی: ' . number_format_i18n( $m['sessions_expired'] ) . ') — ' . $m['sessions_mb'] . ' MB' : '-',
				'Action Scheduler'               => empty( $as ) ? '-' : implode( ' | ', $as ),
				'اکشن‌های عقب‌افتاده'            => isset( $m['as_pastdue'] ) ? number_format_i18n( $m['as_pastdue'] ) : '-',
				'revisionها'                     => isset( $m['revisions'] ) ? number_format_i18n( $m['revisions'] ) : '-',
				'رویدادهای کران (عقب‌افتاده)'   => isset( $m['cron_events'] ) ? number_format_i18n( $m['cron_events'] ) . ' (' . number_format_i18n( $m['cron_overdue'] ) . ')' : '-',
			)
		);

		if ( ! empty( $m['autoload_top'] ) ) {
			echo '<h3>۱۰ گزینه بزرگ autoload</h3><table class="t99-table"><tbody>';
			foreach ( $m['autoload_top'] as $row ) {
				echo '<tr><td class="ltr">' . esc_html( $row['option_name'] ) . '</td><td>' . esc_html( $row['kb'] ) . ' KB</td></tr>';
			}
			echo '</tbody></table>';
		}
		if ( ! empty( $m['big_tables'] ) ) {
			echo '<h3>بزرگ‌ترین جداول</h3><table class="t99-table"><tbody>';
			foreach ( $m['big_tables'] as $row ) {
				echo '<tr><td class="ltr">' . esc_html( $row['t'] ) . '</td><td>' . esc_html( $row['mb'] ) . ' MB</td><td>' . esc_html( number_format_i18n( (int) $row['r'] ) ) . ' ردیف</td></tr>';
			}
			echo '</tbody></table>';
		}

		echo '<h2>سرور</h2>';
		$op         = $env['opcache'];
		$cache_info = 'در Object Cache';
		if ( ! Tarh99_Perf_Cache_Store::external() ) {
			$stats      = Tarh99_Perf_Cache_Store::stats();
			$cache_info = number_format_i18n( $stats['files'] ) . ' فایل، ' . size_format( $stats['bytes'] );
		}
		$this->kv_table(
			array(
				'PHP'                => $env['php'] . ( version_compare( $env['php'], '8.2', '<' ) ? ' — پیشنهاد: 8.3' : '' ),
				'وردپرس / ووکامرس'   => $env['wp'] . ' / ' . $env['wc'],
				'MySQL'              => $env['mysql'],
				'memory_limit'       => $env['memory_limit'] . ' (WP_MEMORY_LIMIT: ' . $env['wp_memory_limit'] . ')',
				'max_execution_time' => $env['max_execution'],
				'OPcache'            => $op['enabled'] ? 'فعال' . ( isset( $op['used_mb'] ) ? ' — مصرف ' . $op['used_mb'] . 'MB، آزاد ' . $op['free_mb'] . 'MB' : '' ) . ( isset( $op['hit_rate'] ) ? ' — hit rate ' . $op['hit_rate'] . '%' : '' ) : ( $op['loaded'] ? 'نصب ولی غیرفعال' : 'نامشخص/غیرفعال' ),
				'Object Cache'       => $env['object_cache'] ? 'فعال' : ( 'غیرفعال — اکستنشن Redis: ' . ( $env['redis_ext'] ? 'موجود' : 'ندارد' ) . '، Memcached: ' . ( $env['memcached_ext'] ? 'موجود' : 'ندارد' ) . ( $env['dropin'] ? '، فایل object-cache.php وجود دارد' : '' ) ),
				'ionCube Loader'     => $env['ioncube'],
				'WP-Cron'            => $env['wp_cron'],
				'WP Rocket'          => $env['rocket'],
				'HPOS سفارش‌ها'      => $env['hpos'] ? 'فعال' : 'غیرفعال',
				'WP_POST_REVISIONS'  => $env['post_revisions'],
				'WP_DEBUG'           => $env['debug'] ? 'روشن (روی سایت اصلی خاموش شود)' : 'خاموش',
				'کش فایل این افزونه' => $cache_info,
			)
		);
	}

	private function render_tools() {
		echo '<div class="t99-box"><h2>عملیات</h2>';
		$this->tool_button( 'run_cleanup', 'اجرای پاکسازی همین حالا', 'tools', 'button button-primary', 'پاکسازی دیتابیس اجرا شود؟ (قبل از اولین اجرا بکاپ بگیرید)' );
		$this->tool_button( 'flush_cache', 'پاک‌کردن کش ایجکس این افزونه', 'tools' );
		$this->tool_button( 'clear_http_log', 'پاک‌کردن لاگ درخواست‌های کند', 'tools' );
		$this->tool_button( 'reset_settings', 'بازگشت همه تنظیمات به پیش‌فرض', 'tools', 'button', 'همه تنظیمات این افزونه به پیش‌فرض برگردد؟' );
		$next = wp_next_scheduled( 'tarh99_perf_daily_cleanup' );
		echo '<p>اجرای بعدی پاکسازی خودکار: ' . esc_html( $next ? wp_date( 'Y-m-d H:i', $next ) : 'زمان‌بندی نشده' ) . '</p>';
		echo '</div>';

		$log = get_option( 'tarh99_perf_cleanup_log', array() );
		echo '<h2>آخرین اجراهای پاکسازی</h2>';
		if ( empty( $log ) || ! is_array( $log ) ) {
			echo '<p>هنوز اجرا نشده است.</p>';
		} else {
			echo '<table class="t99-table"><thead><tr><th>زمان</th><th>نتیجه</th></tr></thead><tbody>';
			foreach ( $log as $row ) {
				$time = isset( $row['time'] ) ? $row['time'] : '';
				unset( $row['time'] );
				echo '<tr><td>' . esc_html( $time ) . '</td><td class="ltr">' . esc_html( wp_json_encode( $row ) ) . '</td></tr>';
			}
			echo '</tbody></table>';
		}

		$http = get_option( 'tarh99_perf_http_log', array() );
		echo '<h2>درخواست‌های بیرونی کند</h2>';
		if ( empty( $http ) || ! is_array( $http ) ) {
			echo '<p>موردی ثبت نشده است.</p>';
		} else {
			echo '<table class="t99-table"><thead><tr><th>زمان</th><th>دامنه</th><th>مسیر</th><th>ثانیه</th><th>محل</th><th>صفحه</th><th>نتیجه</th></tr></thead><tbody>';
			foreach ( $http as $row ) {
				echo '<tr><td>' . esc_html( $row['time'] ) . '</td><td class="ltr">' . esc_html( $row['host'] ) . '</td><td class="ltr">' . esc_html( $row['path'] ) . '</td><td>' . esc_html( $row['seconds'] ) . '</td><td>' . esc_html( $row['context'] ) . '</td><td class="ltr">' . esc_html( $row['page'] ) . '</td><td class="ltr">' . esc_html( $row['result'] ) . '</td></tr>';
			}
			echo '</tbody></table>';
		}

		$scan = get_transient( 'tarh99_perf_scan_' . get_current_user_id() );
		echo '<h2>آخرین اسکن منابع</h2>';
		if ( ! is_array( $scan ) ) {
			echo '<p>از نوار مدیریت در فرانت‌اند روی «سرعت طرح۹۹: اسکن منابع این صفحه» کلیک کنید. (فایل‌های نوار مدیریت فقط برای مدیر لود می‌شوند.)</p>';
			return;
		}
		echo '<p>صفحه: <code>' . esc_html( $scan['page'] ) . '</code> — ' . esc_html( $scan['date'] ) . '</p>';
		$this->tool_button( 'clear_scan', 'پاک‌کردن نتیجه اسکن', 'tools' );
		if ( ! empty( $scan['removed'] ) ) {
			echo '<p>حذف‌شده توسط این افزونه در این صفحه: <code>' . esc_html( implode( ', ', $scan['removed'] ) ) . '</code></p>';
		}
		foreach ( array( 'styles' => 'CSS', 'scripts' => 'JS' ) as $key => $label ) {
			if ( empty( $scan[ $key ] ) ) {
				continue;
			}
			echo '<h3>' . esc_html( $label ) . ' (' . count( $scan[ $key ] ) . ')</h3><table class="t99-table"><thead><tr><th>handle</th><th>حجم</th><th>وابستگی‌ها</th><th>آدرس</th><th>نمونه قانون</th></tr></thead><tbody>';
			foreach ( $scan[ $key ] as $handle => $row ) {
				$type = 'styles' === $key ? 'css' : 'js';
				echo '<tr><td class="ltr">' . esc_html( $handle ) . ( ! empty( $row['strategy'] ) ? ' [' . esc_html( $row['strategy'] ) . ']' : '' ) . ( ! empty( $row['footer'] ) ? ' [footer]' : '' ) . '</td><td>' . esc_html( $row['size'] ? size_format( $row['size'] ) : '-' ) . '</td><td class="ltr">' . esc_html( $row['deps'] ) . '</td><td class="ltr">' . esc_html( $row['src'] ) . '</td><td class="ltr">' . esc_html( ( Tarh99_Perf_Assets::is_critical( $handle ) ? '(حیاتی)' : 'all | ' . $type . ' | ' . $handle ) ) . '</td></tr>';
			}
			echo '</tbody></table>';
		}
	}
}
