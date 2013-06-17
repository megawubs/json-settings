<?php namespace Wubs\Settings;

class Settings{

	/**
	 * the location of the settings file
	 * @var string
	 */
	private $file;

	/**
	 * The settings mapped to a object
	 * @var stdObject
	 */
	private $settings;
	/**
	 * The json string for the settings
	 * @var string
	 */
	private $baseSettingString = '{"trakt":{"username":"","api":"","password":" ","email":""}}';

	private $filePath = '/../../../settings/settings.json';

	private $settingsDir = '/../../../settings/';

	/**
	 * Sets the file, and loads the settings from the file
	 */
	public function __construct($settings = false){
		$base = dirname(__FILE__);
		if(file_exists($base.$this->filePath)){
			$this->file = $base.$this->filePath;
		}
		else{
			if(!is_dir($base.$this->settingsDir)){
				mkdir($base.$this->settingsDir);
			}
			else{
				touch($base.$this->filePath);
				$this->file = $base.$this->filePath;
			}
		}
		if($settings){
			$this->create($settings);
		}
		$this->loadSettings();
	}

	/**
	 * fills the settings file with the given settings string
	 * @param  sting|array $settings a json string or a array representing the
	 * structure of the settings file
	 * @throws \Exception If provided json string is invalid
	 */
	public function create($settings){
		if(is_array($settings)){
			$settings = json_encode($settings);
		}
		else{
			if(!$this->isJson($settings)){
				throw new \Exception("Provided string is no json", 1);
			}
		}
		$this->reset($settings);
		$this->baseSettingString = $settings;
		return $this;
	}
	/**
	 * Get the given setting from the settings file
	 * @param  string $name the name of the setting separated with dots
	 * @return string       the value of the setting
	 * @throws \Exception If requested setting doesn't exist
	 */
	public function get($name){
		$error = "Setting with name: $name not present in the settings";
		if(strstr($name, '.')){
			$settings = $this->parseSettingName($name);
			if($this->settingsExsists($settings)){
				return $this->prettify($this->settings->$settings[0]->$settings[1]);
			}
			else{
				throw new \Exception($error);
			}
		}
		else{
			if(property_exists($this->settings, $name)){
				return $this->settings->$name;
			}
			else{
				throw new \Exception($error);
			}
			
		}
	}

	/**
	 * Sets the given setting if it exists.
	 * @param string $name the name of the setting separated by dots
	 * @param mixed $val  the value for the setting
	 */
	public function set($name, $val){
		if(strstr($name, '.')){
			$settings = $this->parseSettingName($name);
			$this->settings->$settings[0]->$settings[1] = $val;
			return $this->write();
		}
		else{
			return false;
		}
	}

	/**
	 * Writes the settings object back to the file
	 * @return void
	 */
	private function write(){
		try{
			$handle = fopen($this->file, 'w+');
			$string = json_encode($this->settings);
			fwrite($handle, $string);
			$this->loadSettings();
			fclose($handle);
			return true;
		}
		catch(Exeption $e){
			throw new Exception("Error while writing settings");
		}
		
	}

	/**
	 * Resets the setting file
	 * @return void
	 */
	public function reset($settings = false){
		$this->settings = (!$settings) ? json_decode($this->baseSettingString) : json_decode($settings);
		return $this->write();
	}

	/**
	 * Adds a new key value pair to the provided group name
	 * @param  string $group the name of the group to append to
	 * @param  string $key   the name of the key
	 * @param  mixed $value the value of the key, defaults to ''
	 * @throws \Exception If the group doesn't exists
	 */
	public function appendGroup($group, $key, $value = ''){
		if(array_key_exists($group, $this->settings)){
			$this->settings->$group->$key = $value;
			$this->baseSettingString = json_encode($this->settings);
			$this->write();
			return $this;
		}
		else{
			throw new \Exception("Can't add the key '$key' to the group '$group' because the group '$group' doesn't exists");
			
		}
	}

	public function addGroup($group){
		$this->settings->$group = new \stdClass();
		$this->baseSettingString = json_encode($this->settings);
		$this->write();
		return $this;
	}
	/**
	 * parses the name of the setting
	 * @param  string $name setting name seperated by dots
	 * @return array       root and sub setting name
	 */
	private function parseSettingName($name){
		$rootSetting = explode('.', $name)[0];
		$subSetting  = explode('.', $name)[1];
		return array($rootSetting, $subSetting);
	}

	/**
	 * checks if the provided setting exists
	 * @param  string $settings dotted string of setting to check
	 * @return bool           true when the setting can be set, false if not
	 */
	private function settingsExsists($settings){
		return (property_exists($this->settings, $settings[0]) && property_exists($this->settings->$settings[0],$settings[1]));
	}

	/**
	 * gets the content from $this->file and maps it 
	 * to an object
	 * @return void
	 */
	private function loadSettings(){
		if(is_readable($this->file)){
			$string = file_get_contents($this->file);
			$this->settings = json_decode($string);
		}
		else{
			$this->reset();
		}
	}

	/**
	 * prettify the object or string it's given
	 * @param  mixed $var the variable to prettify
	 * @return string      prettified string
	 */
	private function prettify($var){
		$str = '';
		if(is_object($var)){
			foreach ($var as $key => $value) {
				if(!is_object($value)){
					$str .= $key.' = '.$value;
					$str .="\n";
				}
				else{
					$str .= $key."\n";
					$str .= $this->prettify($value);
				}
			}
		}
		else{
			$str = $var;
		}
		return $str;
	}

	/**
	 * shows the complete settings file, prettified
	 * @return string $this->settings prettified as a string
	 */
	public function show(){
		echo $this->prettify($this->settings);
	}

	/**
	 * Checks if given string is json
	 * @param  string  $string the json string
	 * @return boolean         indicator if string is json
	 */
	private function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	public function getSettingsAsArray(){
		$json = json_encode($this->settings);
		return json_decode($json, true);
	}
}