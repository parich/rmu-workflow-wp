<?php
/**
 * Render template สำหรับ shortcode [rmu_workflow dept_id="518"]
 * ตัวแปร $dept_id ถูกส่งมาจาก shortcode callback ใน awesome.php
 */
$dept_id = isset( $dept_id ) ? absint( $dept_id ) : 0;
if ( ! $dept_id ) {
	return;
}
?>
<div
	class="rmu-workflow-container"
	data-dept-id="<?php echo esc_attr( $dept_id ); ?>"
	aria-live="polite"
>
	<div class="rmu-workflow-controls">
		<input
			type="search"
			class="rmu-workflow-search"
			placeholder="ค้นหา flowchart..."
			aria-label="ค้นหา flowchart"
		/>
	</div>

	<div class="rmu-workflow-tags" aria-label="กรองตาม tag"></div>

	<div class="rmu-workflow-status" aria-live="assertive" style="display:none"></div>

	<ul class="rmu-workflow-list" role="list"></ul>
</div>
