# PhotoShow


## Overview

**PhotoShow**, *your* web gallery. **PhotoShow** is a *free* and *open source* web gallery, that you can very easily install on your web server. It doesn't even require a database !

## Installation

### Copy the repository

First, you need to copy the repository into whatever you like (here, toto)

`git clone git://github.com/thibaud-rohmer/PhotoShow-v2.git toto`

### Create three directories

Note : you may create those directories wherever you want, and give them the names you want. It is safer to have the Photos and Thumbs directories outside of your web path (this way, they are only reachable by the web server). The Feeds directory has to be in the web path, though.

* **Photos** : Where your photos will be stored.
* **Thumbs** : Where the thumbnails of your photos will be stored. 
* **Feeds** : Where the rss feeds will be generated.

***Important*** : Make sure that the web server has the rights to read and write in those directories.

### Edit your settings

Edit the file `conf.ini` that is inside your PhotoShow folder. It is advised to put absolute paths for each of the entries, although relative paths should work fine.

### Go to your website

Now, use your favorite web browser to go to your PhotoShow website. You should be asked to create the main account. This account will be the admin of the website.

> Your website is now ready.
