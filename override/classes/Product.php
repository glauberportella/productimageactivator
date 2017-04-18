<?php

class Product extends ProductCore
{
	public function getImages($id_lang, Context $context = null)
    {
        if (!Module::isInstalled('productimageactivator') || !Module::isEnabled('productimageactivator')) {
            return parent::getImages($id_lang, $context);
        }
    	// override completelly the ProductCore::getImages()
    	// if Prestashop team change some logic on it you will need
    	// to review this method
        return Db::getInstance()->executeS('
			SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`, i.`pia_active`
			FROM `'._DB_PREFIX_.'image` i
			'.Shop::addSqlAssociation('image', 'i').'
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
			WHERE i.`id_product` = '.(int)$this->id.'
			AND i.`pia_active` = 1
			ORDER BY `position`'
        );
    }

    public static function getCover($id_product, Context $context = null)
    {
        if (!Module::isInstalled('productimageactivator') || !Module::isEnabled('productimageactivator')) {
            return parent::getCover($id_product, $context);
        }

        if (!$context) {
            $context = Context::getContext();
        }
        $cache_id = 'Product::getCover_'.(int)$id_product.'-'.(int)$context->shop->id;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT image_shop.`id_image`
					FROM `'._DB_PREFIX_.'image` i
					'.Shop::addSqlAssociation('image', 'i').'
					WHERE i.`id_product` = '.(int)$id_product.'
					AND image_shop.`cover` = 1
					AND i.`pia_active` = 1';
            $result = Db::getInstance()->getRow($sql);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }
}