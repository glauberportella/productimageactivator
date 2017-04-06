<?php

if (!defined('_PS_VERSION_'))
	exit;

class ProductImageActivator extends Module
{
	const IMAGE_COLUMN_NAME = 'pia_active';

	protected $_alter_tables = array(
		'image' => array(
			'pia_active' 	=> 'BOOLEAN DEFAULT 1'
		)
	);

	public function __construct() {
		$this->name = 'productimageactivator';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Glauber Portella';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);
		$this->bootstrap = true;
		$this->controllers = array();

		parent::__construct();

		$this->displayName = $this->l('Product Image Activator');
		$this->description = $this->l('Allows activate/deactivate product images.');
		$this->ps_versions_compliancy = array('min' => '1.6.0.4', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		if (!parent::install()) {
			return false;
		}

		if (!$this->alterTables()) {
			$this->_errors[] = $this->l('Erro ao atualizar tabelas de Imagens.');
			return false;
		}

		if (!$this->copyTemplates()) {
			$this->_errors[] = $this->l('Erro ao copiar templates do Administrador.');
			return false;
		}

		return true;
	}

	public function uninstall() {
		if (!parent::uninstall()) {
			return false;
		}

		$this->restoreTables();

		$this->deleteTemplates();

		return true;
	}

	public function enable($force_all = false) {
		$this->copyTemplates();
		return parent::enable($force_all);
	}

    public function disable($force_all = false) {
    	$this->deleteTemplates();
    	return parent::disable($force_all);
    }

	protected function alterTables() {
		$schema_sql = 'SELECT COLUMN_NAME'
			.' FROM INFORMATION_SCHEMA.COLUMNS'
			.' WHERE table_name = :tbl_name'
			.' AND table_schema = :db_name'
			.' AND column_name = :column_name';

		foreach ($this->_alter_tables as $table_name => $fields) {
			foreach ($fields as $field_name => $field_definition) {
				$check_sql = sprintf('SHOW COLUMNS FROM `%s` LIKE "%s"', _DB_PREFIX_.$table_name, $field_name);
				$result = Db::getInstance()->executeS($check_sql);
				if (count($result) === 0) {
					// add column to db
					$alter_sql = sprintf('ALTER TABLE `%s` ADD COLUMN `%s` %s', _DB_PREFIX_.$table_name, $field_name, $field_definition);
					if (!Db::getInstance()->execute($alter_sql)) {
						return false;
					}
				}
			}
		}

		return true;
	}

	protected function restoreTables() {
		$schema_sql = 'SELECT COLUMN_NAME'
			.' FROM INFORMATION_SCHEMA.COLUMNS'
			.' WHERE table_name = :tbl_name'
			.' AND table_schema = :db_name'
			.' AND column_name = :column_name';

		foreach ($this->_alter_tables as $table_name => $fields) {
			$fields = array_keys($fields);
			foreach ($fields as $field_name) {
				$check_sql = sprintf('SHOW COLUMNS FROM `%s` LIKE "%s"', _DB_PREFIX_.$table_name, $field_name);
				$result = Db::getInstance()->executeS($check_sql);
				if (count($result) > 0) {
					// remove column from db
					$alter_sql = sprintf('ALTER TABLE `%s` DROP COLUMN `%s`', _DB_PREFIX_.$table_name, $field_name);
					if (!Db::getInstance()->execute($alter_sql)) {
						return false;
					}
				}
			}
		}

		return true;
	}

	protected function copyTemplates() {
		if (file_exists(_PS_OVERRIDE_DIR_.'controllers/admin/templates/products/images.tpl')) {
			return true;
		}

		if (!file_exists(_PS_OVERRIDE_DIR_.'controllers/admin/templates/products')) {
			mkdir(_PS_OVERRIDE_DIR_.'controllers/admin/templates/products', '0777', true);
		}

		// copy files
		return copy(
			_PS_MODULE_DIR_.$this->name.'/override/controllers/admin/templates/products/images.tpl',
			_PS_OVERRIDE_DIR_.'controllers/admin/templates/products/images.tpl'
		);
	}

	protected function deleteTemplates() {
		if (!file_exists(_PS_OVERRIDE_DIR_.'controllers/admin/templates/products/images.tpl')) {
			return true;
		}

		return unlink(_PS_OVERRIDE_DIR_.'controllers/admin/templates/products/images.tpl');
	}
}