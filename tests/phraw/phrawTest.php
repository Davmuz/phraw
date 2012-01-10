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
    
    public function testBulk_route() {
        $_SERVER['REQUEST_URI'] = 'contacts/';
        $phraw = new Phraw();
        
        # Void
        $pages = array();
        $this->assertFalse($phraw->bulk_route($pages, $page_found, 'equal'));
        $pages = array(
            '' => 'index.html',
            'about/' => 'about.html',
            'documentation/' => 'documentation/index.html'
        );
        $this->assertFalse($phraw->bulk_route($pages, $page_found, 'equal'));
        
        # Simple match
        $pages = array(
            '' => 'index.html',
            'about/' => 'about.html',
            'contacts/' => 'contacts.html',
            'documentation/' => 'documentation/index.html'
        );
        $this->assertTrue($phraw->bulk_route($pages, $page_found, 'equal'));
        $this->assertEquals('contacts.html', $page_found);
        
        # Match with an extra parameter
        $pages = array(
            '' => array('index.html', 'one'),
            'about/' => array('about.html', 'two'),
            'contacts/' => array('contacts.html', 'three'),
            'documentation/' => array('documentation/index.html', 'four')
        );
        $this->assertTrue($phraw->bulk_route($pages, $page_found, 'equal'));
        $this->assertEquals('three', $page_found[1]);
        
        # Match with extra parameters and one with a key
        $pages = array(
            '' => array('index.html', 'one'),
            'about/' => array('about.html', 'two', 'number' => 2),
            'contacts/' => array('contacts.html', 'three', 'number' => 3),
            'documentation/' => array('documentation/index.html', 'four')
        );
        $this->assertTrue($phraw->bulk_route($pages, $page_found, 'equal'));
        $this->assertEquals('three', $page_found[1]);
        $this->assertEquals(3, $page_found['number']);
    }
}
?>