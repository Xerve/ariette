# Ariette
[![Build Status](https://travis-ci.org/rrdelaney/ariette.svg?branch=master)](https://travis-ci.org/rrdelaney/ariette)
[![Documentation Status](https://readthedocs.org/projects/ariette/badge/?version=latest)](https://readthedocs.org/projects/ariette/?badge=latest)
[![Codacy Badge](https://www.codacy.com/project/badge/4030bdab7e4941c09969d4284e8a761a)](https://www.codacy.com/app/rrdelaney/ariette)
[![Code Climate](https://codeclimate.com/github/rrdelaney/ariette/badges/gpa.svg)](https://codeclimate.com/github/rrdelaney/ariette)

Ariette is a small framework designed to make you not hate your life as much because
you're using PHP. It is designed around making API's and focuses on your backend logic, 
but doesn't make any desicions for you, making it easy to integrate into new projects.

## Installing
```
composer require rrdelaney/ariette
```

## Set up environment

Run

```
composer install
composer dump-autoload
php scipts/test.php
```

## Project Layout

### `/core`
The standard library for Ariette lives here.

### `/docs`
Documentation to be built with mkdocs is here.

### `/it`
It is the testing framework that comes bundled with Ariette

### `/aritette`
The source code for the Ariette framework

### `/scripts`
Build scripts and other fun stuff.

### `/tests`
Tests build with It live here.
