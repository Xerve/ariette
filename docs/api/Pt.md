# Pt

The static Pt class is the main instance of the application that will keep track
of modules you've declared and in libraries you've included.

#### `Pt::module(string $name, array<string> $deps, [function $callback])`

Creates a new Module named `$name`.

Any dependencies declared in `$deps` will be checked for existence. If they
don't exist, an error will be thrown. All dependencies declared will be passed
to the callback function. It is possible to declare module-wide middleware and
endware in the module declaration.

`$callback` will be executed when the Module is first initialized. Arguments
passed in will be components declared in `$deps`.

Examples:

```php
Pt::module('Test', []);
// Most basic module definition
```

```php
Pt::module('Test', ['Pt::redirect']);
// Declaring Module Test relies on Pt::redirect
```

```php
Pt::module('Test', ['Pt::redirect'], function($redirect) {
    // $redirect is the Pt::redirect Component
});
```

```php
Pt::module('Test', ['*Pt::redirect'], function() {
    // Pt::redirect will be applied as middleware
    // for all Components in the Module
});
```

```php
Pt::module('Test', ['Pt::redirect*', 'Pt::something'], function($something) {
    // Pt::redirect is endware for all Components in the Test Module
    // $something is the Pt::something Component
});
```

#### `Pt::module(string $name)`

If you have already created a module with the name `$name`, it will return the
appropriate module.

Example:

```php
Pt::module('Test', []);

$m = Pt::module('Test');
// $m is now the test Module
```
