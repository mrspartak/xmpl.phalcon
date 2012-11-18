<?

class ViewManager {
	
	private $_key;
	
	public function __construct() {
		
	}


	public function beforeRender($event, $view){
		
		$app['controller'] = $view->getControllerName();
		$app['action'] = $view->getActionName();
		$view->setVar("app", $app);
		
		$view->setVar("message", $view->request->get('message'));
		$view->setVar("answered", $_COOKIE['answered']);
		
		/*
		$this->_key = $view->getControllerName().'/'.$view->getActionName();
		
		$cache = Phalcon\DI::getDefault()->getShared('cache');	
		
		$got = 	$cache->get( $this->_key );	
		if ( $got !== null ) {
			echo $got;
			$view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
		}
		*/
    }
	
	
	public function afterRender($event, $view){	
	/*
       	$rendered = $view->getContent();
		
		$cache = Phalcon\DI::getDefault()->getShared('cache');
		$cache->save($this->_key, $rendered);
		
        $view->setContent($rendered);
	*/
    }

}