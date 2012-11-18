<?

class ViewManager {
	
	private $_key;
	private $_toCache = array('index/index');
	private $_caching = false;
	
	public function __construct() {
		
	}


	public function beforeRender($event, $view){
		
		$app['controller'] = $view->getControllerName();
		$app['action'] = $view->getActionName();
		$view->setVar("app", $app);
		
		$view->setVar("message", $view->request->get('message'));
		
		if( isset($_COOKIE['answered']) )
			$view->setVar("answered", $_COOKIE['answered']);
		
		/*
		$this->_key = $view->getControllerName().'/'.$view->getActionName();
		if( in_array( $this->_key, $this->_toCache ) ) {

			$this->_caching = true;
			$cache = Phalcon\DI::getDefault()->getShared('cache');
			$got = 	$cache->get( $this->_key );	
			if ( $got !== null ) {
				echo $got;
				$view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
			}	
				
		}
		*/
    }
	
	
	public function afterRender($event, $view){	
	/*
		if( $this->_caching ) {
			
			$rendered = $view->getContent();
		
			$cache = Phalcon\DI::getDefault()->getShared('cache');
			$cache->save($this->_key, $rendered);
			
			$view->setContent($rendered);
			
		}
      */ 	
    }

}