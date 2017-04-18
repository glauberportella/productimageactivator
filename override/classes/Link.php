<?php

class Link extends LinkCore
{
	public function getImageLink($name, $ids, $type = null)
    {
    	if (!Module::isInstalled('productimageactivator') || !Module::isEnabled('productimageactivator')) {
    		return parent::getImageLink($name, $ids, $type);
    	}
    	
        $not_default = false;

        // Check if module is installed, enabled, customer is logged in and watermark logged option is on
        if (($type != '') && Configuration::get('WATERMARK_LOGGED') && (Module::isInstalled('watermark') && Module::isEnabled('watermark')) && isset(Context::getContext()->customer->id)) {
            $type .= '-'.Configuration::get('WATERMARK_HASH');
        }

        // legacy mode or default image
        $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
        if ((Configuration::get('PS_LEGACY_IMAGES')
            && (file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg')))
            || ($not_default = strpos($ids, 'default') !== false)) {
            if ($this->allow == 1 && !$not_default) {
                $uri_path = __PS_BASE_URI__.$ids.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg';
            }
        } else {
            // if ids if of the form id_product-id_image, we want to extract the id_image part
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            $image = new Image($id_image);
            if (!$image || $image->pia_active == 0) {
                $id_product = $split_ids[0];
                $product = new Product($id_product);
                if ($product) {
                    $context = Context::getContext();
                    $product_images = $product->getImages($context->language->id, $context);
                    if (!count($product_images)) {
                        $id_image = sprintf('%s-default', strtolower($context->language->iso_code));
                    } else {
                        $id_image = $product_images[0]['id_image'];
                    }
                }
            }

            if (($not_default = strpos($id_image, 'default')) !== false) {
                $theme = (
                    (Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$id_image.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) 
                    ? '-'.Context::getContext()->shop->id_theme 
                    : ''
                );

                if ($this->allow == 1 && !$not_default) {
                    $uri_path = __PS_BASE_URI__.$id_image.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
                } else {
                    $uri_path = _THEME_PROD_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').$theme.'.jpg';
                }
            } else {
                $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');

                if ($this->allow == 1) {
                    $uri_path = __PS_BASE_URI__.$id_image.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
                } else {
                    $uri_path = _THEME_PROD_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').$theme.'.jpg';
                }
            }
        }

        return $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
    }
}