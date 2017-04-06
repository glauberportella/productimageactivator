<?php

class Image extends ImageCore
{
	public $pia_active;

	public function __construct($id = null, $id_lang = null) {
		self::$definition['fields']['pia_active'] = array(
			'type' => self::TYPE_BOOL,
			'allow_null' => true,
			'validate' => 'isBool',
		);
		parent::__construct($id, $id_lang);
	}
}