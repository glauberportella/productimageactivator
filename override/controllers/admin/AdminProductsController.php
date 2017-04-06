<?php

class AdminProductsController extends AdminProductsControllerCore
{
	public function ajaxProcessUpdateActive()
    {
        if (!Module::isEnabled('productimageactivator')) {
            return;
        }

        if ($this->tabAccess['edit'] === '0') {
            return die(Tools::jsonEncode(array('error' => $this->l('You do not have the right permission'))));
        }

        $img = new Image((int)Tools::getValue('id_image'));
        if (!isset($img->pia_active)) {
        	$this->jsonConfirmation($this->l('Sucesso!'));
        }

        $img->pia_active = (int)Tools::getValue('activate');

        if ($img->update()) {
            $msg = $img->pia_active == 1 ? $this->l('Imagem ativada com sucesso!') : $this->l('Imagem desativada com sucesso!');
            $this->jsonConfirmation($msg);
        } else {
            $this->jsonError(Tools::displayError('An error occurred while attempting to update the active state.'));
        }
    }
}