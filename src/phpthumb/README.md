phpThumb
========

phpThumb() - The PHP thumbnail generator

phpThumb() uses the GD library and/or ImageMagick to create thumbnails from images (GIF, PNG or JPEG) on the fly.
The output size is configurable (can be larger or smaller than the source), and the source may be the entire
image or only a portion of the original image. True color and resampling is used if GD v2.0+ is available,
otherwise low-color and simple resizing is used. Source image can be a physical file on the server or can be
retrieved from a database. GIFs are supported on all versions of GD even if GD does not have native GIF support
thanks to the GIFutil class by Fabien Ezber. AntiHotlinking feature prevents other people from using your server
to resize their thumbnails, or link to your images from another server. The cache feature reduces server load.
