<?php
require_once('DataTablesPaginatorComponent.php');
	
class DataTableComponent extends DataTablesPaginatorComponent {
	public function getResponse($controller = null, $model=null) {
		if(is_object($controller)) $this->Controller = $controller;
		return $this->paginate($model);
	}
}