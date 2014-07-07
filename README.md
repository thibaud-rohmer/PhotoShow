# PhotoShow 3.0

## Website

[PhotoShow-Gallery](http://www.photoshow-gallery.com)

## Overview

**PhotoShow**, *your* web gallery. **PhotoShow** is a *free* and *open source* web gallery, that you can very easily install on your web server. It doesn't even require a database !

## What's new ?

Wow, well, loads. Here is a quick list.
* "Material design" : applied most of the guidelines from Google's new design paradigm. And if you don't like it....
* ... Themes ! You can add your own themes in the user/themes/ folder, and select them in the Settings.
* Responsive Layout : you can now access and manage your PhotoShow from your phone !
* PhotoSpheres support : put your photosheres in a folder named "PhotoSpheres" and... enjoy
* RSS Feed : automatically adds items when generating a thumb for them (meaning they're new), only if they are publicly accessible. Also, if you change the access rights later on, they will apply to the feed.
* Administration : removed most of the drag'n'drop stuff (clunky and unusable from a phone) : now, most of your modifications can be done from the Information panel (right menu)
* Settings overhaul
* Comments deleting : Now, you can. Sorry this took so long.
* Thumbnails size can be edited in the admin section.
* Loads of code removed for a faster, more reliable PhotoShow. Hopefully.
* Uploaders have lost a load of rights. Basically, all they can do is upload. No file removing anymore.
* Rights management SHOULD be the same as before. But, just in case, please check after updating

## Installation

### Copy the repository

First, you need to copy the repository into whatever you like (here, toto)

`git clone https://github.com/thibaud-rohmer/PhotoShow.git toto`

### Create two directories

Note : you may create those directories wherever you want, and give them the names you want. It is safer to have the Photos and Thumbs directories outside of your web path (this way, access can be restricted using the authentication & authorization mechanisms provided by PhotoShow).

* **Photos** : Where your photos will be stored.
* **Generated** : Where the thumbnails of your photos will be stored. 

***Important*** : Make sure that the web server has the rights to read and write in those directories.

### Edit your settings

Edit the file `config.php` that is inside your PhotoShow folder. It is advised to put absolute paths for each of the entries, although relative paths should work fine.

### Go to your website

Now, use your favorite web browser to go to your PhotoShow website. You should be asked to create the main account. This account will be the admin of the website.

> Your website is now ready.
