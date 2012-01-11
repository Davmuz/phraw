<?
require_once('../phraw/phraw.php');

class TestValueException extends Exception {}

class PhrawMockFix_trailing_slash extends Phraw {
    static function redirect($url=null, $type=301) {
        throw new TestValueException($url);
    }
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
        unset($page_found);
        
        # No match
        $pages = array(
            '' => 'index.html',
            'about/' => 'about.html',
            'documentation/' => 'documentation/index.html'
        );
        $this->assertFalse($phraw->bulk_route($pages, $page_found, 'equal'));
        unset($page_found);
        
        # Simple match
        $pages = array(
            '' => 'index.html',
            'about/' => 'about.html',
            'contacts/' => 'contacts.html',
            'documentation/' => 'documentation/index.html'
        );
        $this->assertTrue($phraw->bulk_route($pages, $page_found, 'equal'));
        $this->assertEquals('contacts.html', $page_found);
        unset($page_found);
        
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
        unset($page_found);
    }

    public function testTree_route() {
        $_SERVER['REQUEST_URI'] = 'contacts/';
        $phraw = new Phraw();
        
        # Void
        $pages = array();
        $this->assertFalse($phraw->tree_route($pages, $values, 'equal'));
        unset($values);
        
        # No match
        $pages = array(
            '' => array(null, 'index.html'),
            'about/' => array(null, 'about.html'),
            'documentation/' => array(null, 'documentation/index.html')
        );
        $this->assertFalse($phraw->tree_route($pages, $values, 'equal'));
        unset($values);
        
        # Simple match
        $pages = array(
            '' => array(null, 'index.html'),
            'about/' => array(null, 'about.html'),
            'contacts/' => array(null, 'contacts.html'),
            'documentation/' => array(null, 'documentation/index.html')
        );
        $this->assertTrue($phraw->tree_route($pages, $values, 'equal'));
        $this->assertEquals('contacts.html', $values[0]);
        unset($values);
        
        # Match with extra parameters and one with a key
        $pages = array(
            '' => array(null, 'index.html', 'one'),
            'about/' => array(null, 'about.html', 'two', 'number' => 2),
            'contacts/' => array(null, 'contacts.html', 'three', 'number' => 3),
            'documentation/' => array(null, 'documentation/index.html', 'four')
        );
        $this->assertTrue($phraw->tree_route($pages, $values, 'equal'));
        $this->assertEquals('three', $values[1]);
        $this->assertEquals(3, $values['number']);
        unset($values);
        
        # External default values
        $values = array('extra', 'value' => 'foobar');
        $pages = array(
            '' => array(null, 'index.html', 'one'),
            'about/' => array(null, 'about.html', 'two', 'number' => 2),
            'contacts/' => array(null, 'contacts.html', 'three', 'number' => 3),
            'documentation/' => array(null, 'documentation/index.html', 'four')
        );
        $this->assertTrue($phraw->tree_route($pages, $values, 'equal'));
        $this->assertEquals('extra', $values[0]);
        $this->assertEquals('three', $values[2]);
        $this->assertEquals('foobar', $values['value']);
        $this->assertEquals(3, $values['number']);
        unset($values);
        
        # Hierarchy and mix of default values
        $values = array('extra', 'value' => 'foobar');
        $pages = array(
            '' => array(null, 'index.html', 'one'),
            'about/' => array(array(
                '' => array(null, 'about.html'),
                'my/' => array(array(
                    'own/' => array(array(
                        'contacts/' => array(null, 'contacts.html', 'three', 'leaf', 'number' => 3)
                    )),
                )),
            ), 'two', 'number' => 2),
            'documentation/' => array(null, 'documentation/index.html', 'four')
        );
        # A wrong path
        $this->assertFalse($phraw->tree_route($pages, $values, 'equal'));
        $_SERVER['REQUEST_URI'] = 'about/';
        $phraw = new Phraw();
        $this->assertTrue($phraw->tree_route($pages, $values, 'equal'));
        $this->assertEquals('extra', $values[0]);
        $this->assertEquals('two', $values[1]);
        $this->assertEquals('about.html', $values[2]);
        $this->assertEquals(2, $values['number']);
        unset($values);
        # A no ending path
        $_SERVER['REQUEST_URI'] = 'about/my/';
        $phraw = new Phraw();
        $this->assertFalse($phraw->tree_route($pages, $values, 'equal'));
        # An ending path
        $_SERVER['REQUEST_URI'] = 'about/my/own/contacts/';
        $phraw = new Phraw();
        $values = array('extra', 'value' => 'foobar');
        $this->assertTrue($phraw->tree_route($pages, $values, 'equal'));
        $this->assertEquals('extra', $values[0]);
        $this->assertEquals('two', $values[1]);
        $this->assertEquals('contacts.html', $values[2]);
        $this->assertEquals('three', $values[3]);
        $this->assertEquals('leaf', $values[4]);
        $this->assertEquals(3, $values['number']);
        unset($values);
    }

    public function testDetect_no_trailing_slash() {
        $_SERVER['REQUEST_URI'] = '';
        $phraw = new Phraw();
        $this->assertFalse($phraw->detect_no_trailing_slash());
        
        $_SERVER['REQUEST_URI'] = '/';
        $phraw = new Phraw();
        $this->assertFalse($phraw->detect_no_trailing_slash());
        
        $_SERVER['REQUEST_URI'] = 'foo/';
        $phraw = new Phraw();
        $this->assertFalse($phraw->detect_no_trailing_slash());
        
        $_SERVER['REQUEST_URI'] = 'foo/bar';
        $phraw = new Phraw();
        $this->assertTrue($phraw->detect_no_trailing_slash());
    }

    public function testFix_trailing_slash() {
        # Classic situation
        $_SERVER['REQUEST_URI'] = 'foo/bar';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = '80';
        $phraw_mock = new PhrawMockFix_trailing_slash();
        try {
            $phraw_mock->fix_trailing_slash();
        } catch (TestValueException $e) {
            $this->assertEquals('http://localhost/foo/bar/', $e->getMessage());
        }
        
        # Mixed situation
        $_SERVER['REQUEST_URI'] = 'foo/bar';
        $_SERVER['SERVER_NAME'] = 'www.localhost';
        $_SERVER['SERVER_PORT'] = '8080';
        $_SERVER['HTTPS'] = 'on';
        $phraw_mock = new PhrawMockFix_trailing_slash();
        try {
            $phraw_mock->fix_trailing_slash();
        } catch (TestValueException $e) {
            $this->assertEquals('https://www.localhost:8080/foo/bar/', $e->getMessage());
        }
    }
}
?>