<?
require_once('../phraw/phraw.php');

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

    public function testRoute() {
        # Regular expressions (default)
        $_SERVER['REQUEST_URI'] = '/';
        $phraw = new Phraw();
        $this->assertTrue($phraw->route('^$'));
        $_SERVER['REQUEST_URI'] = '/foo/bar';
        $phraw = new Phraw();
        $this->assertTrue($phraw->route('^foo\/(?P<name>\w+)\/?$'));
        $this->assertEquals('bar', $phraw->uri_values['name']);

        # Regular expressions only on parentheses
        $_SERVER['REQUEST_URI'] = '/';
        $phraw = new Phraw();
        $this->assertTrue($phraw->route('^$', 'prexp'));
        $_SERVER['REQUEST_URI'] = '/foo/bar';
        $phraw = new Phraw();
        $this->assertTrue($phraw->route('^foo/(?P<name>\w+)(\/?)$', 'prexp'));
        $this->assertEquals('bar', $phraw->uri_values['name']);
    
        # Simple equal strings match
        $_SERVER['REQUEST_URI'] = '/';
        $phraw = new Phraw();
        $this->assertTrue($phraw->route('', 'equal'));
        $_SERVER['REQUEST_URI'] = '/foo/bar';
        $phraw = new Phraw();
        $this->assertTrue($phraw->route('foo/bar', 'equal'));
    
        # Function or method call
        function custom_route(&$uri, &$phraw_uri, &$phraw_uri_values) {
            $phraw_uri_values['name'] = 'bar';
            return $uri === $phraw_uri;
        }
        $_SERVER['REQUEST_URI'] = '/foo/bar';
        $phraw = new Phraw();
        $this->assertFalse($phraw->route('foo', 'custom_route'));
        $this->assertTrue($phraw->route('foo/bar', 'custom_route'));
        $this->assertEquals('bar', $phraw->uri_values['name']);
    }
}
?>