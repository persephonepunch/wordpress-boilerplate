# Wordpress Boilerplate

A boilerplate for my particular flavor of Wordpress.  I like to customize things, so this is Wordpress customized.

## Technologies Used
- HTML ( 5 ), CSS ( 3 ), JS ( [jQuery](http://jquery.com/download/) )
- [Wordpress (3.6)](http://wordpress.org/latest.zip)
- [Humans.txt](http://humanstxt.org/)
- [Bower](http://bower.io/)
- [Grunt](http://gruntjs.com/)

## Installation

1. `git clone git@github.com:apermanentwreck/wordpress-boilerplate.git .`
2. `cd wordpress-boilerplate` or whatever you've named it.
3. `git submodule update --init`
4. Set up [MAMP Pro](http://www.mamp.info/en/downloads/index.html) for `dev.{{ domain }}` as virtual host
5. Create dB in [Sequel Pro](https://code.google.com/p/sequel-pro/downloads/list) (settings are in config/db-settings.dev.php)
6. Hit `dev.{{ domain }}/wordpress/wp-admin/` to install Wordpress
7. `git checkout develop`
8. Create feature branches off of develop in the form of _feature/{{ name of feature }}_
9. `grunt dev` to start the grunt/build process

## Release Process

- Git flow process goes here
- `grunt release`

## Credits
- David Winter's [Install and manage WordPress with Git](http://davidwinter.me/articles/2012/04/09/install-and-manage-wordpress-with-git/)
