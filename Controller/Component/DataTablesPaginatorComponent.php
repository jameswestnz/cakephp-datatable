<?php
App::uses('Component', 'PaginatorComponent');
	
class DataTablesPaginatorComponent extends PaginatorComponent {
	public function paginate($object = null, $scope = array(), $whitelist = array()) {
		$results = parent::paginate($object, $scope, $whitelist);
		
		$object = $this->_getObject($object);
		
        $response = array(
            'sEcho' => isset($this->Controller->request->query['sEcho']) ? intval($this->Controller->request->query['sEcho']) : 1,
            'iTotalRecords' => $this->Controller->request->paging[$object->name]['count'],
            'iTotalDisplayRecords' => $this->Controller->request->paging[$object->name]['count'],
            'aaData' => $results
        );
        
		return $response;
	}
	
	public function mergeOptions($alias) {
		$options = parent::mergeOptions($alias);
		
		if(!isset($options['conditions'])) $options['conditions'] = array();
		
		$options['conditions'] = array_merge($options['conditions'], $this->getWhereConditions());
		
		if(isset($this->Controller->request->query['iDisplayLength'])) {
			$options['limit'] = $this->Controller->request->query['iDisplayLength'];
		}
		
		if(isset($this->Controller->request->query['iDisplayStart'])) {
			$options['page'] = round($this->Controller->request->query['iDisplayStart'] / $options['limit']) + 1;
		}
		
		return $options;
	}

	/**
	 * returns sql conditions array after converting dataTables GET request into Cake style conditions
	 * will only search on fields with bSearchable set to true (which is the default value for bSearchable)
	 * @param void
	 * @return array
	 */
    private function getWhereConditions(){
        $fields = array();

		if(isset($this->Controller->request->query['iColumns'])) {
	        for($i=0;$i<$this->Controller->request->query['iColumns'];$i++){
	            if(!isset($this->Controller->request->query['bSearchable_'.$i]) || $this->Controller->request->query['bSearchable_'.$i] == true){
	                $fields[] = $this->Controller->request->query['mDataProp_'.$i];
	            }
	        }
        }
        
        $sSearch = array();

        foreach($fields as $x => $column){
            // only create conditions on bSearchable fields
            if( $this->Controller->request->query['bSearchable_'.$x] == 'true' ){
	            
	            // process sSearch
	            if(!empty($this->Controller->request->query['sSearch'])) {
	                $sSearch['OR'][] = array(
	                    $this->Controller->request->query['mDataProp_'.$x].' LIKE' => '%'.$this->Controller->request->query['sSearch'].'%'
	                ); 
	            }
                
                //then sSearch_x
	            if(!empty($this->Controller->request->query['sSearch_'.$x])) {
	                $sSearch[$this->Controller->request->query['mDataProp_'.$x].' LIKE'] = '%'.$this->Controller->request->query['sSearch_'.$x].'%'; 
	            }
            }
        }
        
        $conditions = array('AND' => $sSearch);
        
        return $conditions;
    }
}