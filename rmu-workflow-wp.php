<?php
/**
 * Plugin Name:       RMU Workflow
 * Plugin URI:        https://github.com/parich/rmu-workflow-wp
 * Description:       แสดงรายการ Flowchart จากระบบ RMU Workflow พร้อมค้นหาและกรองด้วย Tag โดยใช้ Shortcode [rmu_workflow].
 * Version:           0.1.3
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Parich Suriya
 * Author URI:        https://github.com/parich
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rmu-workflow
 *
 * @package RmuWorkflow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RMU_WORKFLOW_VERSION', '0.1.3' );
define( 'RMU_WORKFLOW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RMU_WORKFLOW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// ---------------------------------------------------------------------------
// Block registration
// ---------------------------------------------------------------------------
function create_block_awesome_block_init() {
	wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
}
add_action( 'init', 'create_block_awesome_block_init' );

// ---------------------------------------------------------------------------
// Register frontend assets (enqueue ตอน shortcode render)
// ---------------------------------------------------------------------------
function rmu_workflow_register_frontend_assets() {
	wp_register_style(
		'rmu-workflow-style',
		RMU_WORKFLOW_PLUGIN_URL . 'build/awesome/style-index.css',
		array(),
		RMU_WORKFLOW_VERSION
	);

	wp_register_script(
		'rmu-workflow-view',
		RMU_WORKFLOW_PLUGIN_URL . 'build/awesome/view.js',
		array(),
		RMU_WORKFLOW_VERSION,
		true
	);

	wp_localize_script(
		'rmu-workflow-view',
		'rmuWorkflowSettings',
		array(
			'apiUrl'  => get_option( 'rmu_workflow_api_url', 'https://workflow.rmu.ac.th/api/embed/flowcharts' ),
			'baseUrl' => get_option( 'rmu_workflow_base_url', 'https://workflow.rmu.ac.th' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'rmu_workflow_register_frontend_assets' );

// ---------------------------------------------------------------------------
// Shortcode: [rmu_workflow dept_id="518"]
// ---------------------------------------------------------------------------
function rmu_workflow_shortcode( $atts ) {
	$atts = shortcode_atts(
		array( 'dept_id' => '' ),
		$atts,
		'rmu_workflow'
	);

	$dept_id = absint( $atts['dept_id'] );
	if ( ! $dept_id ) {
		return '';
	}

	wp_enqueue_style( 'rmu-workflow-style' );
	wp_enqueue_script( 'rmu-workflow-view' );

	ob_start();
	include RMU_WORKFLOW_PLUGIN_DIR . 'build/awesome/render.php';
	return ob_get_clean();
}
add_shortcode( 'rmu_workflow', 'rmu_workflow_shortcode' );

// ---------------------------------------------------------------------------
// Admin settings page
// ---------------------------------------------------------------------------
function rmu_workflow_admin_menu() {
	add_options_page(
		__( 'RMU Workflow Settings', 'rmu-workflow' ),
		__( 'RMU Workflow', 'rmu-workflow' ),
		'manage_options',
		'rmu-workflow',
		'rmu_workflow_settings_page'
	);
}
add_action( 'admin_menu', 'rmu_workflow_admin_menu' );

function rmu_workflow_settings_init() {
	register_setting( 'rmu_workflow_settings', 'rmu_workflow_api_url', array(
		'type'              => 'string',
		'sanitize_callback' => 'esc_url_raw',
		'default'           => 'https://workflow.rmu.ac.th/api/embed/flowcharts',
	) );
	register_setting( 'rmu_workflow_settings', 'rmu_workflow_base_url', array(
		'type'              => 'string',
		'sanitize_callback' => 'esc_url_raw',
		'default'           => 'https://workflow.rmu.ac.th',
	) );

	add_settings_section( 'rmu_workflow_section_api', __( 'การตั้งค่า API', 'rmu-workflow' ), null, 'rmu-workflow' );

	add_settings_field( 'rmu_workflow_api_url', __( 'Endpoint URL', 'rmu-workflow' ), 'rmu_workflow_field_api_url', 'rmu-workflow', 'rmu_workflow_section_api' );
	add_settings_field( 'rmu_workflow_base_url', __( 'Base URL (ลิงก์ไปยังหน้า flowchart)', 'rmu-workflow' ), 'rmu_workflow_field_base_url', 'rmu-workflow', 'rmu_workflow_section_api' );
}
add_action( 'admin_init', 'rmu_workflow_settings_init' );

function rmu_workflow_field_api_url() {
	$value = get_option( 'rmu_workflow_api_url', 'https://workflow.rmu.ac.th/api/embed/flowcharts' );
	echo '<input type="url" name="rmu_workflow_api_url" value="' . esc_attr( $value ) . '" class="regular-text" />';
	echo '<p class="description">ตัวอย่าง: https://workflow.rmu.ac.th/api/embed/flowcharts</p>';
}

function rmu_workflow_field_base_url() {
	$value = get_option( 'rmu_workflow_base_url', 'https://workflow.rmu.ac.th' );
	echo '<input type="url" name="rmu_workflow_base_url" value="' . esc_attr( $value ) . '" class="regular-text" />';
	echo '<p class="description">ตัวอย่าง: https://workflow.rmu.ac.th</p>';
}

function rmu_workflow_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'rmu_workflow_settings' );
			do_settings_sections( 'rmu-workflow' );
			submit_button( __( 'บันทึกการตั้งค่า', 'rmu-workflow' ) );
			?>
		</form>
		<hr />
		<h2><?php esc_html_e( 'วิธีใช้งาน', 'rmu-workflow' ); ?></h2>
		<p>ใส่ shortcode ต่อไปนี้ในหน้าหรือโพสต์ที่ต้องการ:</p>
		<code>[rmu_workflow dept_id="518"]</code>
		<p>ดูรายการ <code>dept_id</code> ทั้งหมดได้ที่ <a href="https://github.com/parich/rmu-workflow-wp/" target="_blank" rel="noopener noreferrer">https://github.com/parich/rmu-workflow-wp/</a></p>
	</div>
	<?php
}

// ---------------------------------------------------------------------------
// GitHub Update Checker
// ---------------------------------------------------------------------------
class RMU_Workflow_GitHub_Updater {

	private $slug            = 'rmu-workflow-wp';
	private $plugin_file;
	private $plugin_basename;
	private $github_owner    = 'parich';
	private $github_repo     = 'rmu-workflow-wp';
	private $current_version;
	private $github_response;
	private $cache_key       = 'rmu_workflow_github_update';
	private $cache_expiry    = 21600; // 6 hours

	public function __construct( $plugin_file ) {
		$this->plugin_file     = $plugin_file;
		$this->plugin_basename = plugin_basename( $plugin_file );

		$plugin_data           = get_file_data( $plugin_file, array( 'Version' => 'Version' ) );
		$this->current_version = $plugin_data['Version'];

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 20, 3 );
	}

	private function get_github_release() {
		if ( $this->github_response !== null ) {
			return $this->github_response;
		}

		$cached = get_transient( $this->cache_key );
		if ( $cached !== false ) {
			$this->github_response = $cached;
			return $cached;
		}

		$url      = "https://api.github.com/repos/{$this->github_owner}/{$this->github_repo}/releases/latest";
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'Accept'     => 'application/vnd.github.v3+json',
				'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ),
			),
		) );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			$this->github_response = false;
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		if ( empty( $body ) || ! isset( $body->tag_name ) ) {
			$this->github_response = false;
			return false;
		}

		$this->github_response = $body;
		set_transient( $this->cache_key, $body, $this->cache_expiry );

		return $body;
	}

	public function check_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$release = $this->get_github_release();
		if ( ! $release ) {
			return $transient;
		}

		$remote_version = ltrim( $release->tag_name, 'v' );

		if ( version_compare( $remote_version, $this->current_version, '>' ) ) {
			$download_url = $release->zipball_url;

			if ( ! empty( $release->assets ) ) {
				foreach ( $release->assets as $asset ) {
					if ( substr( $asset->name, -4 ) === '.zip' ) {
						$download_url = $asset->browser_download_url;
						break;
					}
				}
			}

			$transient->response[ $this->plugin_basename ] = (object) array(
				'slug'        => $this->slug,
				'plugin'      => $this->plugin_basename,
				'new_version' => $remote_version,
				'url'         => $release->html_url,
				'package'     => $download_url,
			);
		}

		return $transient;
	}

	public function plugin_info( $result, $action, $args ) {
		if ( $action !== 'plugin_information' || $args->slug !== $this->slug ) {
			return $result;
		}

		$release = $this->get_github_release();
		if ( ! $release ) {
			return $result;
		}

		$remote_version = ltrim( $release->tag_name, 'v' );
		$download_url   = $release->zipball_url;

		if ( ! empty( $release->assets ) ) {
			foreach ( $release->assets as $asset ) {
				if ( substr( $asset->name, -4 ) === '.zip' ) {
					$download_url = $asset->browser_download_url;
					break;
				}
			}
		}

		return (object) array(
			'name'          => 'RMU Workflow',
			'slug'          => $this->slug,
			'version'       => $remote_version,
			'author'        => '<a href="https://github.com/parich">Parich Suriya</a>',
			'homepage'      => "https://github.com/{$this->github_owner}/{$this->github_repo}",
			'requires'      => '6.7',
			'requires_php'  => '7.4',
			'sections'      => array(
				'description' => 'แสดงรายการ Flowchart จากระบบ RMU Workflow พร้อมค้นหาและกรองด้วย Tag',
				'changelog'   => nl2br( esc_html( $release->body ?? '' ) ),
			),
			'download_link' => $download_url,
		);
	}

}

new RMU_Workflow_GitHub_Updater( __FILE__ );
