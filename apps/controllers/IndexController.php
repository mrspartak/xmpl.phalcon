<?php

class IndexController extends BaseController {
	
	public function initialize()
    {
        //Set the document title
        Phalcon\Tag::setTitle('Index');
        parent::initialize();
    }


	public function indexAction(){
		 
	}
	
}