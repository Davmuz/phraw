<?
require_once('../phraw/phraw.php');
function __autoload($class) {
    require $class . '.php';
}
class PhrawTest extends PHPUnit_Framework_TestCase {
    public function testGet_uri() {
        $phraw = new Phraw();
        
        # Via SERVER mode
        $_SERVER['REQUEST_URI'] = '';
        $this->assertEquals('', $phraw->get_uri());
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertEquals('', $phraw->get_uri());
        $_SERVER['REQUEST_URI'] = '/foo/bar';
        $this->assertEquals('foo/bar', $phraw->get_uri());
        $_SERVER['REQUEST_URI'] = '/foo/bar/';
        $this->assertEquals('foo/bar/', $phraw->get_uri());
        
        # Via GET mode
        $_GET['u'] = '';
        $this->assertEquals('', $phraw->get_uri('u'));
        $_GET['u'] = '/';
        $this->assertEquals('', $phraw->get_uri('u'));
        $_GET['u'] = '/foo/bar';
        $this->assertEquals('foo/bar', $phraw->get_uri('u'));
        $_GET['u'] = '/foo/bar/';
        $this->assertEquals('foo/bar/', $phraw->get_uri('u'));
    }
}
?>