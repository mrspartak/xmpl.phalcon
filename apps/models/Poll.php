<?

class Poll extends Phalcon\Mvc\Model
{
    public $id;

    public $gender;

    public $age;

    public $read_modern;

    public $authors;

    public $genre;
	
	public $method;
	
	public $timestamp;

   /**
    * This model is mapped to the table answers
    */
    public function getSource()
    {
        return 'answers';
    }
	
	public function initialize()
    {
        $this->setConnectionService('db');
    }
}