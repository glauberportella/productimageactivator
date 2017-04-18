<?php

class ProductController extends ProductControllerCore
{
	protected function assignImages()
    {
        $images = $this->product->getImages((int)$this->context->cookie->id_lang);
        $product_images = array();

        if (isset($images[0])) {
            $this->context->smarty->assign('mainImage', $images[0]);
        }
        foreach ($images as $k => $image) {
        	$image_active = true;
        	if (Module::isEnabled('productimageactivator')) {
        		$image_active = $image['pia_active'] == 1;
        	}
            if ($image['cover'] && $image_active) {
                $this->context->smarty->assign('mainImage', $image);
                $cover = $image;
                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$image['id_image']) : $image['id_image']);
                $cover['id_image_only'] = (int)$image['id_image'];
            }
            $product_images[(int)$image['id_image']] = $image;
        }

        if (!isset($cover)) {
            if (isset($images[0])) {
                $cover = $images[0];
                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$images[0]['id_image']) : $images[0]['id_image']);
                $cover['id_image_only'] = (int)$images[0]['id_image'];
            } else {
                $cover = array(
                    'id_image' => $this->context->language->iso_code.'-default',
                    'legend' => 'No picture',
                    'title' => 'No picture'
                );
            }
        }
        $size = Image::getSize(ImageType::getFormatedName('large'));
        $this->context->smarty->assign(array(
            'have_image' => (isset($cover['id_image']) && (int)$cover['id_image'])? array((int)$cover['id_image']) : Product::getCover((int)Tools::getValue('id_product')),
            'cover' => $cover,
            'imgWidth' => (int)$size['width'],
            'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
            'largeSize' => Image::getSize(ImageType::getFormatedName('large')),
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'cartSize' => Image::getSize(ImageType::getFormatedName('cart')),
            'col_img_dir' => _PS_COL_IMG_DIR_));
        if (count($product_images)) {
            $this->context->smarty->assign('images', $product_images);
        }
    }
}