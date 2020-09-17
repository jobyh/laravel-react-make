# laravel-react-make
[![Build Status](https://travis-ci.com/jobyh/laravel-react-make.svg?branch=main)](https://travis-ci.com/jobyh/laravel-react-make)

Artisan generator for React function and class components. Supports Laravel 7, 6 &amp; 5.8.

## Quick start

```
% cd /path/to/laravel/project
% composer require --dev jobyh/laravel-react-make
```

### Customise Stubs

Publishing stubs in Laravel 7 is a [great feature](https://laravel.com/docs/7.x/artisan#stub-customization).
Yes please.

```
% php artisan vendor:publish --tag react-stub
```

## Usage

Generate a React function component:

```
% php artisan make:react MyComponent
# -> resources/js/components/MyComponent.js
```

Generate under a subdirectory:

```
% php artisan make:react foo/bar/MyComponent
# -> resources/js/components/foo/bar/MyComponent.js
```

Use `.jsx` file extension (short version `-x` is also available)

```
% php artisan make:react --jsx MyComponent
# -> resources/js/components/MyComponent.jsx
```

Generate a class based component (short version `-c` is also available)
```
% php artisan make:react --class MyComponent
# -> resources/js/components/MyComponent.js
```
