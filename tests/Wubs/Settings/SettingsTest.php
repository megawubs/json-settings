<?php namespace Wubs\Settings;

class SettingsTest extends \PHPUnit_Framework_TestCase{
	protected static $file;

	protected static $content;

	public static function setUpBeforeClass(){
		self::$file    = dirname(__FILE__).'/../../../settings/settings.json';
		self::$content = file_get_contents(self::$file);
	}
	public function setUp(){
		$this->s = new Settings();
	}

	public function tearDown(){
		unset($this->s);
	}

	public function testFirstInitateWithoutFile(){
		unlink(self::$file);
		unset($this->s);
		$this->s = new Settings();
		$this->assertInternalType('object', $this->s);
		$this->assertInstanceOf('Wubs\Settings\Settings', $this->s);
		$this->assertFileExists(self::$file);
	}

	public function testSettingSettingsWithString(){
		$settings = '{"trakt":{"username":"","api":"","password":"","email": ""}}';
		$this->assertInstanceOf('Wubs\Settings\Settings', $this->s->fill($settings));
		$this->assertTrue($this->s->set('trakt.username', 'megawubs'));
		$this->assertEquals('megawubs', $this->s->get('trakt.username'));
	}

	/**
	 * @expectedException \Exception
	 */
	public function testSettingsSettingsWithInvalidJson(){
		$settings = '"trakt":{"username":"","api":"","password":"","email" ""}}';
		$this->s->fill($settings);
	}

	public function testSettingSettingsWithArray(){
		$settings = array('trakt'=>array('username'=>'megawubs', 'api'=>'', 'password'=>'', 'email'=>''));
		$this->assertEquals('megawubs', $this->s->fill($settings)->get('trakt.username'));
	}

	public function testAppendingExsistingSettingGroupWithNewKeyValuePair(){
		$key = 'validated';
		$value = '';
		$this->assertInstanceOf('Wubs\Settings\Settings',$this->s->appendGroup('trakt', $key, $value));
		$this->assertEquals('', $this->s->get('trakt.validated'));
		$this->assertTrue($this->s->set('trakt.validated', true));
		$this->assertEquals(true, $this->s->get('trakt.validated'));
	}

	/**
	 * @expectedException \Exception
	 */
	public function testAppendingValueToNonExsistingGroup(){
		$key = 'file';
		$value = 'foo.txt';
		$this->assertInstanceOf('Wubs\Settings\Settings',$this->s->appendGroup('bar', $key, $value));
	}

	public function testAddingNewGroup(){
		$this->assertInstanceOf('Wubs\Settings\Settings', $this->s->addGroup('files'));
		$value = $this->s->addGroup('foo')->appendGroup('foo', 'file', 'bar.txt')->get('foo.file');
		$this->assertEquals('bar.txt', $value);
		$this->assertArrayHasKey('foo', $this->s->getSettingsAsArray());
	}

	public function testGetSettingWithoudPrettify(){
		$trakt = $this->s->get('trakt', false);
		$this->assertInternalType('array', $trakt);
	}

	public function testGetSettingsWithPrettify(){
		$trakt = $this->s->get('trakt.username');
		$this->assertInternalType('string', $trakt);
	}

	public function testCreatingSettingsFileOnInitationWithJsonString(){
		unlink(self::$file);
		unset($this->s);
		$settings = array('foo'=>array('bar'=>'foo', 'bars'=>'foos'));
		$this->s = new Settings($settings);
		$this->assertArrayHasKey('foo', $this->s->getSettingsAsArray());
	}

	public function testGetFileLocation(){
		$this->assertFileExists($this->s->getFileLocation());
	}

	public static function tearDownAfterClass(){
		$s = new Settings();
		$s->reset(self::$content);
	}
}