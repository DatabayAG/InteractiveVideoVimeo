<#1>
<?php
/**
 * @var $ilDB ilDB
 */
if(!$ilDB->tableExists('rep_robj_xvid_vimeo'))
{
	$fields = array(
		'obj_id' => array(
			'type' => 'integer',
			'length' => '4',
			'notnull' => true
		),
		'vimeo_id' => array(
			'type' => 'text',
			'length' => '100',
			'notnull' => true
		)
	);
	$ilDB->createTable('rep_robj_xvid_vimeo', $fields);
	$ilDB->addPrimaryKey('rep_robj_xvid_vimeo', array('obj_id', 'vimeo_id'));
}
?>