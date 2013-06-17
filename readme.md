json-settings
=============

This package aims to make a json setting file usable and easy maintainable.

The json file is build like this:

```JSON
{
	"foo":{
		"bar":"foo",
		"bars":"foos",
	}
}
```
Where `foo` is a group and `bar` a key value pair.

It is possible to append groups and add new groups.

## usage examples

### Getting settings

Getting a setting is straight forward.

```PHP
//gets the value of bar in the foo group
$value = $s->get('foo.bar'); //foo
//gets the value of bars in the foo group
$values = $s->get('foo.bars'); //foos
```

### Filling the settings file

This can be done by giving `$settings` as a parameter when creating a new Settings object

`$settings` can either be a json string or an array
```PHP
$settings = array('foo'=>array('bar'=>'foo', 'bars'=>'foos'));
$s = new Settings($settings); 
print_r($s->getSettingsAsArray()); 
/**
 * Array
 *(
 *   [foo] => Array
 *       (
 *           [bar] => foo
 *           [bars] => foos
 *       )
 *
 *)
 *
 */
```
Or by calling the fill method

```PHP
$settings = array('foo'=>array('bar'=>'foo', 'bars'=>'foos'));
$s = new Settings(); 
$s->fill($settings);
```

### Appending groups

To add new settings to a existing group goes as follows

```PHP
//we assume the settings file is already filled
$s->appendGroup('foo','cackes');
```
The group foo has now a new key value pair with the key `'cackes'`. The value defaults to `''`
If you want to add a default value use a third parameter like this
```PHP
$s->appendGroup('foo','cackes','cheesecake');
```

### Add a new group

Adding a new group to the settings file, and give it some default settings.

```PHP
$s->addGroup('files')->appendGroup('files', 'logfile', '/location/to/file.log');
```

### Todo:

- [x] Make it a separate package

- [x] Add functionality to extend the json settings

- [x] Make the settings file dynamic 

- [ ] Allow greater settings depth

- [ ] Allow arrays when adding a new group
