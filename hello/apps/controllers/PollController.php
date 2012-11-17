<?php

class PollController extends BaseController {
	
	public function initialize()
    {
        //Set the document title
        Phalcon\Tag::setTitle('Poll');
        parent::initialize();
    }


	public function answerAction(){
			
		if ($this->request->isPost() == true) {
			
			$poll = new Poll();
			$hash = $this->request->getClientAddress().$this->request->getUserAgent();
			$hash = md5( $hash );
			$bool = $poll->find("hash = '".$hash."'");

			if( $bool->count() ) {
			
				setcookie('answered', 1, time()+60*60*24*30, '/hello');
				return $this->dispatcher->forward( array('controller' => 'poll', "action" => "results") );
				
			} else {
				
				$post = $this->request;	
							
				$poll->gender = $post->getPost("gender", "int");
				$poll->age = $post->getPost("age", "int");
				$poll->read_modern = $post->getPost("read_modern", "int");
				
				$poll->authors = implode( ',', $post->getPost("authors") );
				$poll->genre = implode( ',', $post->getPost("genre") );
				$poll->method = implode( ',', $post->getPost("method") );
				
				$poll->hash = $hash;
				$poll->timestamp = time();				
				
				if ($poll->create() == false) {
					$this->response->redirect("poll/answer?message=error");
				} else {
					$this->response->redirect("poll/results");
				}
				/**/

			}
				
		}
		
		else {
			
			$bool = $_COOKIE['answered'];
			if( $bool )
				$this->response->redirect("poll/results");
				
		}
		
	}
	
	public function resultsAction(){
		Phalcon\Tag::appendTitle(' | Answer');
	}
	
	
	public function makeAction(){
		
		$gender = array( 1, 2 );
		$age = array( 1, 2, 3, 4, 5, 6 );
		$modern = array( 1, 2 );
		
		$authors =	array('local', 'abroad');
		$genre = array('drama','tradegy','comedy','detective','adventures','fy','fantasy','horror','cyber','ero','bio');
		$method = array('pc', 'tablet', 'mobile', 'ebook', 'book');
		
		for( $i=0; $i < 5000; $i++ ) {
			
			$poll = new Poll();
			$poll->gender = $this->rnd( $gender );
			$poll->age = $this->rnd( $age );
			$poll->read_modern = $this->rnd( $modern );
			
			$poll->authors = $this->rnd( $authors, true );
			$poll->genre = $this->rnd( $genre, true );
			$poll->method = $this->rnd( $method, true );
			
			$poll->hash = md5( time().rand() );
			$poll->timestamp = time();	
			
			$poll->create();
			//echo '<br>---<br>';
		}
	}
	
	public function rnd( $obj, $set = false ) {
		$l = count( $obj ) - 1;
		$tmp = array();
		if( $set ) {
			$num = rand(1, $l+1);
			for( $i=0; $i<$num; $i++ ) {
				$r = rand(0, $l);
				$tmp[ $obj[$r] ] = $obj[$r];
			}
			return implode(',', $tmp);
			//echo implode(',', $tmp).'<br>';
		} else {
			$r = rand(0, $l);
			return $obj[ $r ];
			//echo $obj[ $r ].'<br>';
		}
	}
}