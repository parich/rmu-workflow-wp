import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';

export default function Edit() {
	return (
		<div { ...useBlockProps() }>
			<p style={ { padding: '0.75rem', background: '#f9fafb', borderRadius: '6px', margin: 0 } }>
				<strong>RMU Workflow</strong> —{ ' ' }
				{ __( 'ใช้ Shortcode', 'rmu-workflow' ) }{ ' ' }
				<code>[rmu_workflow dept_id="518"]</code>{ ' ' }
				{ __( 'ในหน้าหรือโพสต์ที่ต้องการ', 'rmu-workflow' ) }
			</p>
		</div>
	);
}
