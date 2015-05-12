# Ariette

The static Ariette class is the main instance of the application that will keep
track of modules you've declared and in libraries you've included.

#### `Ariette::module(string $name, array<string> $deps, [function $callback])`

Creates a new Module named `$name`.

Any dependencies declared in `$deps` will be checked for existence. If they
don't exist, an error will be thrown. All dependencies declared will be passed
to the callback function. It is possible to declare module-wide middleware and
endware in the module declaration.

`$callback` will be executed when the Module is first initialized. Arguments
passed in will be components declared in `$deps`.

Examples:

```php
Ariette::module('Test', []);
// Most basic module definition
```

```php
Ariette::module('Test', ['Ariette::redirect']);
// Declaring Module Test relies on Ariette::redirect
```

```php
Ariette::module('Test', ['Ariette::redirect'], function($redirect) {
    // $redirect is the Ariette::redirect Component
});
```

```php
Ariette::module('Test', ['*Ariette::redirect'], function() {
    // Ariette::redirect will be applied as middleware
    // for all Components in the Module
});
```

```php
Ariette::module('Test', ['Ariette::redirect*', 'Ariette::something'], function($something) {
    // Ariette::redirect is endware for all Components in the Test Module
    // $something is the Pt::something Component
});
```

#### `Ariette::module(string $name)`

If you have already created a module with the name `$name`, it will return the
appropriate module.

Example:

```php
Ariette::module('Test', []);

$m = Ariette::module('Test');
// $m is now the test Module
```
