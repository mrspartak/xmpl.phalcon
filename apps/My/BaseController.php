<?

class BaseController extends \Phalcon\Mvc\Controller
{
	public function initialize()
    {
        Phalcon\Tag::prependTitle('Example | ');
    }
}