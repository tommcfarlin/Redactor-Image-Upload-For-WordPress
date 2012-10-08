# Redactor Image Uploads For WordPress

Easily integrate image uploads with the [Redactor.js](http://redactorjs.com/) WYSIWYG editor into any WordPress based project.

## About

[Redactor.js](http://redactorjs.com/) is a powerful jQuery plugin that makes it easy to introduce front-end editing to any web site or web application.

If you're looking to incorporate Redactor and its image uploading functionality into WordPress, then you can have a bit of a challenge especially when it comes with trying to make sure all uploads reside in the `wp-content/uploads` directory.

This script works to solve that problem:

 * It aims to use WordPress conventions by placing it in the `uploads/YEAR/MONTH` directories
 * It will create said directories if they don't already exist.
 * The script will also look to rename the uploaded file if another one exists on the server by the same name.

## Requirements

Redactor Image Uploads For WordPress aims to be as easy as possible to integrate into your existing theme or plugin. You need to have the following:

* WordPress, of course
* Enough knowledge of JavaScript to integrate a library
* PHP 5.3 or greater

## Installation


To install the script…

1. Place the file in a directory in your theme or plugin. I'm a fan of placing it within a `lib` directory in the root of, say, your theme.
2. Register and Enqueue the Redactor library with your theme
3. Register and Enqueue your own theme's JavaScript
4. Reference this file in the `imageUpload` parameter of the Redactor initialization function

## How To Use It

Assuming that you've placed `redactor-image-upload-for-wordpress.php` into a `lib` directory in the root of your theme, enqueue the Redactor library:

## 1. Register and Enqueue Redactor

```php
// Stylesheets
wp_register_style( 'redactor-style', get_template_directory_uri() . '/css/redactor.css' );
wp_enqueue_style( 'redactor-style' );

// JavaScript
wp_register_script( 'redactor-js', get_template_directory_uri() . '/js/redactor.min.js' );
wp_enqueue_script( 'redactor-js' );
```

### 2. Setup Redactor in `theme.js`

In your own `theme.js` file, setup Redactor and having the `ImageUploads` parameter reference this script.

First, setup a hidden field on your page that contains the directory to your theme. This is so that we can properly import the script using JavaScript. 

```html
<input type="hidden" id="theme-directory" value="<?php echo get_template_directory_uri(); ?>" />
```

Next, setup your theme's JavaScript code by reading the directory path and providing the location to this script for the `imageUpload` parameter.

```javascript
(function ($) {
	"use strict";
	$(function () {

			var sThemeDirectory;
			sThemeDirectory = $('#theme-directory').val();
			$('#story_content').redactor({
				
				focus:			false,
				autorsize:		false,
				buttons:		[ 'bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', '|', 'image', 'video', '|', 'link' ],
				tabindex:		3,
				minHeight:		200,
				imageUpload:	sThemeDirectory + '/lib/redactor-image-uploads-for-wordpress.php'
				
			});
		
		} // end if

	});
}(jQuery));
```

Finally, register and enqueue the above JavaScript source.

```php
wp_register_script( 'theme-js', get_template_directory_uri() . '/js/theme.js' );
wp_enqueue_script( 'theme-js' );
```

## Known Issues

1. This script only works if the WordPress installation is located in the root (read: `/public_html/`) directory of a web server.
2. This script expects that the user's default uploads directory is in `wp-content/uploads` rather than reading the value from the API.


## Contact

* Twitter: [@tommcfarlin](http://twitter.com/tommcfarlin)
* Website: [http://tommcfarlin.com](http://tommcfarlin.com)
* Contact: [Contact Form](http://tommcfarlin.com/contact/)
* Email:   [tom@tommcfarlin.com](mailto:tom@tommcfarlin.com)

## Changelog

### 0.2 (October 8th, 2012)

* Adding *Known Issues* to the README

### 0.1 (October 6th, 2012)

* Initial Release