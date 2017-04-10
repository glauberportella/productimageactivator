<?php

class Image extends ImageCore
{
	public $pia_active = 1;

	public function __construct($id = null, $id_lang = null) {
		self::$definition['fields']['pia_active'] = array(
			'type' => self::TYPE_BOOL,
			'allow_null' => true,
			'validate' => 'isBool',
		);
		parent::__construct($id, $id_lang);
	}

    public static function hasImages($id_lang, $id_product, $id_product_attribute = null)
    {
        $attribute_filter = ($id_product_attribute ? ' AND ai.`id_product_attribute` = '.(int)$id_product_attribute : '');
        $sql = 'SELECT 1
			FROM `'._DB_PREFIX_.'image` i
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image`)';

        if ($id_product_attribute) {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` ai ON (i.`id_image` = ai.`id_image`)';
        }

        $sql .= ' WHERE i.`id_product` = '.(int)$id_product.' AND il.`id_lang` = '.(int)$id_lang.$attribute_filter.' AND i.`pia_active` = 1';
        return (bool)Db::getInstance()->getValue($sql);
    }
}