<?php
/**
 * Plugin Name:       RMU Workflow
 * Description:       แสดงรายการ Flowchart จากระบบ RMU Workflow พร้อมค้นหาและกรองด้วย Tag โดยใช้ Shortcode [rmu_workflow].
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Parich Suriya
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rmu-workflow
 *
 * @package RmuWorkflow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RMU_WORKFLOW_VERSION', '0.1.0' );
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
	include RMU_WORKFLOW_PLUGIN_DIR . 'src/awesome/render.php';
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
	</div>
	<?php
}
