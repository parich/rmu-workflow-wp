<?php
// This file is generated. Do not modify it manually.
return array(
	'awesome' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'create-block/awesome',
		'version' => '0.1.0',
		'title' => 'RMU Workflow',
		'category' => 'widgets',
		'icon' => 'networking',
		'description' => 'แสดงรายการ Flowchart จากระบบ RMU Workflow',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'rmu-workflow',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScript' => 'file:./view.js'
	)
);
