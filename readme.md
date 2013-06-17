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

## usage example:

```PHP
$s = new Setting();
//gets the value of bar in the foo settings
$value = $s->get('foo.bar'); //foo
$values = $s->get('foo.bars'); //foos
```

### Todo:
[X] Make it a separate package
[X] Add functionality to extend the json settings
[X] Make the settings file dynamic 
