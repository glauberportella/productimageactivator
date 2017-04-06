<?php

class Product extends ProductCore
{
	public function getImages($id_lang, Context $context = null)
    {
    	// override completelly the ProductCore::getImages()
    	// if Prestashop team change some logic on it you will need
    	// to review this method
        return Db::getInstance()->executeS('
			SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
			FROM `'._DB_PREFIX_.'image` i
			'.Shop::addSqlAssociation('image', 'i').'
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
			WHERE i.`id_product` = '.(int)$this->id.'
			AND i.pia_active = 1
			ORDER BY `position`'
        );
    }
}