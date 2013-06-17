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

- [x] Make it a separate package

- [ ] Add functionality to extend the json settings

- [ ] Make the settings file dynamic 
