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
			$hash = md5( $hash . rand() );
			/*
			$bool = $poll->find("hash = '".$hash."'");

			if( $bool->count() ) {
			
				setcookie('answered', 1, time()+60*60*24*30, '/');
				return $this->response->redirect("poll/results?message=twice");
				
			} else {
			*/
				$post = $this->request;	
							
				$poll->gender = $post->getPost("gender", "int");
				$poll->age = $post->getPost("age", "int");
				$poll->read_modern = $post->getPost("read_modern", "int");
				
				if( $poll->read_modern == 3 ) {
					$poll->authors = '';
					$poll->genre = '';
					$poll->method = '';
				} else {
					$poll->authors = implode( ',', $post->getPost("authors") );
					$poll->genre = implode( ',', $post->getPost("genre") );
					$poll->method = implode( ',', $post->getPost("method") );
				}
				
				
				$poll->hash = $hash;
				$poll->timestamp = time();				
				
				if ($poll->create() == false) {
					$this->response->redirect("poll/answer?message=error");
				} else {
					setcookie('answered', 1, time()+60*60*24*30, '/');
					$this->response->redirect("poll/results?message=answered");
				}
			/*
			}
			*/
				
		}
		
		else {
			
			$bool = $_COOKIE['answered'];
			if( $bool )
				$this->response->redirect("poll/results");
				
		}
		
	}
	
	public function resultsAction(){
		Phalcon\Tag::appendTitle(' | Result');
	}
	
	public function ajaxAction(){
		//if(!$this->request->isAjax())
		//	exit();
		
		$this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
		
		$type = $this->request->get('type');
		$key = 'db-'.$type;
		$cache = $this->cache->get( $key );
		
		
		if( !$cache ) {
			
			$poll = new Poll();
			$count = Poll::count();
			
			$labelsGender = array( '', 'до 18 лет', 'от 18 до 25', 'от 25 до 35 лет', 'от 35 до 45 лет', 'от 45 до 60 лет', 'от 60 лет' );
			$labelsModern = array( '', 'читаю', 'не читаю', 'не люблю читать' );
			$labelsGenre = array( 'drama' => 'драма', 'tradegy' => 'трагедия', 'comedy' => 'комедия', 'detective' => 'детектив', 'adventures' => 'приключения', 'fy' => 'фантастика', 'fantasy' => 'фэнтези', 'horror' => 'ужасы', 'cyber' => 'киберпанк', 'ero' => 'эротика', 'bio' => 'биография' );
			
			switch( $type ) {
			
				case 'genderAge' :
				$query = $this->modelsManager->executeQuery("SELECT COUNT(id) AS count, gender, age FROM poll GROUP BY gender, age");
				foreach ($query as $row) {
					$tmp[ $row->age ][ $row->gender ] = $row->count/$count;
				}
				foreach( $tmp as $k => $row ) {
					$tmps[$k]['row'] = array_values( $row );
					$tmps[$k]['label'] = $labelsGender[$k];
				}
				$tmp = null;
				$chart = 1;
				$opt = 0;
				break;
				
				case 'modern' :
				$query = $this->modelsManager->executeQuery("SELECT COUNT(id) AS count, read_modern FROM poll GROUP BY read_modern");
				foreach ($query as $row) {
					$tmp[ $row->read_modern ] = $row->count/$count;
				}
				foreach( $tmp as $k => $row ) {
					$tmps[$k]['row'] = $row;
					$tmps[$k]['label'] = $labelsModern[$k];
				}
				$tmp = null;
				$chart = 2;
				$opt = 1;
				break;
				
				case 'genres' :
				$genres = array('drama','tradegy','comedy','detective','adventures','fy','fantasy','horror','cyber','ero','bio');
				foreach( $genres as $k => $genre ) {
					$query = $this->modelsManager->executeQuery("SELECT COUNT(id) AS count FROM poll WHERE FIND_IN_SET( '$genre', genre)");
					foreach ($query as $row) {
						$tmp[ $genre ] = $row->count/$count;
					}
				}
				foreach( $tmp as $k => $row ) {
					$t['row'] = $row;
					$t['label'] = $labelsGenre[$k];
					$tmps[] = $t;
				}
				$tmp = null;
				$chart = 3;
				$opt = 1;
				break;
					
			}
			
			$result = array(
				'timestamp' => time(),
				'chart' => $chart,
				'opt' => $opt,
				'data'	=> $tmps
			);
			$result = json_encode( $result );
			$this->cache->save( $key, $result );
		} else {
			$result = $cache;
		}
		
		echo $result;
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