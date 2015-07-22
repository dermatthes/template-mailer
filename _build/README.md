# Which binary version to download?

Here you have the choice how to download template-mailer.

## Single-file phar archive with autoloader

`template-mailer.recent.phar.php.gz`

Use this file to easy integrate template-mailer into your project. To include
template-mailer just put the file somewhere, extract it using `gunzip template-mailer.recent.phar.php.gz`
and include it in your project:

```
require "path/to/template-mailer.recent.phar.php";
```
That's it.

## All source-files in one tarball

`template-mailer.recent.src.tar.gz`

Includes all files from the /src directory. No autoloader is included. 
It is mainly for integration into frameworks working with an own classloader.