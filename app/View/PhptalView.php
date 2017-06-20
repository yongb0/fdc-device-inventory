<?php

require_once(APP . 'Vendor/phptal/classes/PHPTAL.php');
App::uses('View', 'View');
App::uses('CakeLog', 'Log');

class PhptalView extends View {

	public $sharedPath = null;

	private $phptal;

	private $layoutDir = null;

	public function __construct(Controller $controller = null) {
		parent::__construct($controller);
		$this->phptal = new PHPTAL();
		$this->phptal->setTemplateRepository(APP . 'View/');
		if ($controller !== null) {
			$this->layoutDir= $controller->layoutDir;
		}
	}

	public function render($view = null, $layout = null) {
		if ($this->hasRendered) {
			return;
		}

		if ($view === null) {
			$view = $this->view;
		}
		if ($layout === null) {
			$layout = $this->layout;
		}
		$viewFileName = $this->_getViewFileName($view);
		if ($layout === false) {
			$this->phptal->setTemplate($viewFileName);
		} else {
			$layoutFileName = $this->_getLayoutFileName($layout);
			$this->phptal->setTemplate($layoutFileName);
			$this->phptal->set('tmpl_contents', $viewFileName);
		}

		$dataForView = $this->viewVars;
		foreach ($dataForView as $key => $value) {
			$this->phptal->set($key, $value);
		}

		$this->phptal->set('view', $this);
		$this->phptal->set('session', $this->Session);

		
		$this->phptal->set('tmpl_paginator', $this->_getSharedFileName('paginate'));

		return $this->phptal->execute();
	}

	protected function _getSharedFileName($name = null) {
		$subDir = null;

		if ($this->sharedPath !== null) {
			$subDir = $this->sharedPath . DS;
		}
		list($plugin, $name) = $this->pluginSplit($name);
		$paths = $this->_paths($plugin);
		$file = 'Global' . DS . $subDir . $name;

		$exts = $this->_getExtensions();
		foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $file . $ext)) {
					return $path . $file . $ext;
				}
			}
		}
		throw new MissingLayoutException(array('file' => $file . $this->ext));
	}

	
	public static function getCacheDisabledPath($filename) {
		$full_path = APP . 'webroot' . $filename;
		if (file_exists($full_path)) {
			$filename .= '?date=' .date('YmdHis', filemtime($full_path));
		}
		return $filename;
	}

	protected function _getViewFileName($name = null) {
		$subDir = null;

		if ($this->subDir !== null) {
			$subDir = $this->subDir . DS;
		}

		if ($name === null) {
			$name = $this->view;
		}
		$name = str_replace('/', DS, $name);
		list($plugin, $name) = $this->pluginSplit($name);

		if (strpos($name, DS) === false && $name[0] !== '.') {
			$name = $this->viewPath . DS . $subDir . Inflector::underscore($name);
		} elseif (strpos($name, DS) !== false) {
			if ($name[0] === DS || $name[1] === ':') {
				$name = trim($name, DS);
			} elseif ($name[0] === '.') {
				$name = substr($name, 3);
			} elseif (!$plugin || $this->viewPath !== $this->name) {
				$name = $this->viewPath . DS . $subDir . $name;
			}
		}
		$paths = $this->_paths($plugin);
		$exts = $this->_getExtensions();
		foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $name . $ext)) {
					return $path . $name . $ext;
				}
			}
		}
		throw new MissingViewException(array('file' => $name . $this->ext));
	}

	protected function _getLayoutFileName($name = null) {
		if ($name === null) {
			$name = $this->layout;
		}

		$layoutDir = 'Layouts';
		if ($this->layoutDir !== null) {
			$layoutDir = $this->layoutDir;
		}

		$subDir = null;
		if ($this->layoutPath !== null) {
			$subDir = $this->layoutPath . DS;
		}
		list($plugin, $name) = $this->pluginSplit($name);
		$paths = $this->_paths($plugin);
		$file = $layoutDir . DS . $subDir . $name;
		$exts = $this->_getExtensions();
		foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $file . $ext)) {
					return $path . $file . $ext;
				}
			}
		}
		throw new MissingLayoutException(array('file' => $file . $this->ext));
	}

	public function getGlobalUrl($filename){
		$full_path = APP . 'View/Global/' . $filename;
		// return $full_path;
	}

}
