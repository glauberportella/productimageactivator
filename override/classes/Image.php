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
    	if (!Module::isInstalled('productimageactivator') || !Module::isEnabled('productimageactivator')) {
    		return parent::hasImages($id_lang, $id_product, $id_product_attribute);
    	}

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

    public static function getCover($id_product)
    {
    	if (!Module::isInstalled('productimageactivator') || !Module::isEnabled('productimageactivator')) {
    		return parent::getCover($id_product);
    	}

        return Db::getInstance()->getRow('
			SELECT * FROM `'._DB_PREFIX_.'image_shop` image_shop
			INNER JOIN `'._DB_PREFIX_.'image` i ON i.`id_image` = image_shop.`id_image`
			WHERE image_shop.`id_product` = '.(int)$id_product.'
			AND image_shop.`cover`= 1
			AND i.`pia_active` = 1');
    }

    public static function getGlobalCover($id_product)
    {
    	if (!Module::isInstalled('productimageactivator') || !Module::isEnabled('productimageactivator')) {
    		return parent::getGlobalCover($id_product);
    	}

        return Db::getInstance()->getRow('
			SELECT * FROM `'._DB_PREFIX_.'image` i
			WHERE i.`id_product` = '.(int)$id_product.'
			AND i.`cover`= 1
			AND i.`pia_active` = 1');
    }
}