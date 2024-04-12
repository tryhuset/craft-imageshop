# Imageshop plugin for Craft CMS 4

Integrate with an Imageshop account and use Imageshop assets in Craft.

![Screenshot](resources/img/plugin-logo.png)

## Please note

This is a copy of [maxwelvm/Craft-Imageshop](https://github.com/maxwelvm/Craft-Imageshop), which in turn is a fork of [vangenplotz/Craft-Imageshop](https://github.com/vangenplotz/Craft-Imageshop) which seems abandoned. We've copied the plugin to be able to maintain it and keep it updated. Credits for the original code goes to [Vangen & Plotz](https://vangenplotz.no/).

## Requirements

This plugin requires Craft CMS 4.

To use this plugin you need an account with [Imageshop](https://www.imageshop.org/).

## Installation

To install the plugin, follow these instructions.

1. Add the following object to your composer.json file in the `repositories` array

   ```json
   {
     "type": "vcs",
     "url": "https://git1.apt.no/open/craft-imageshop.git"
   }
   ```

2. Then tell Composer to load the plugin:

   ```ssh
   composer require "trydig/craft-imageshop" -w && php craft plugin/install craft-imageshop
   ```

3. ( In the Control Panel, go to Settings → Plugins and click the “Install” button for Imageshop if the plugin wasn't installed. )

# Development

To develop this plugin, include it as a package in a Craft installation by loading it from the file system.

In your Craft projects `composer.json`, add the following in the `repositories` array

```json
{
  "type": "path",
  "url": "/some/path/on/your/file-system/craft-imageshop"
}
```

If your Craft project already has a link to the repostitory on git1.apt.no, remove that and replace with the above code, then run `composer update` to use the version from your file system instead.

If not, then you need to at the package as a composer requirement, like described above in step 2.

You're now ready to do development on the plugin.

# Field Overview

![Imageshop field modal](resources/img/screen-modal.jpg)

The field has been made to mimic the normal Craft asset field. To the left the user can choose to filter the results
by the available categories.

There is a search field that lets the user search amongst the images. The results are loaded dynamically as the user scrolls
the list.

![Rearranging images](resources/img/screen-moving.jpg)

The user can rearrange the order of the images by dragging and dropping.

## Configuring Imageshop

### Plugin settings

Once installed you need to go to the plugin settings and provide a Imageshop token.

![Settings screen](resources/img/screen-settings.jpg)

Chose one of the available languages.

Press the "Refresh" button under "Interface name" to get available interfaces for the provided token. Select the interface name to be used as default.

Save the changes once you're finished.

Note that changing the token will make all the previously selected images inaccessible.

### Field settings

![Field settings screen](resources/img/screen-field-settings.jpg)

You can add the field type to a standalone field or to a matrix block.

Choose the text on the "Add image"-button and the "Maxiumum number of images" the user can choose. A value of `0` means the
user kan choose an infinite amount of images.

## Using the Imageshop image field

When you access the ImageshopImage field you get an array of ImageShop-models. These are the attributes and methods available on the model:

| Property/method | Description                                                                                                                                   |
| --------------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| alt             | Image alt text from Imageshop document Description                                                                                            |
| base64pixel     | Image placeholder base64 encoded transparent 1x1px gif                                                                                        |
| credits         | Image credits from Imageshop document Credits                                                                                                 |
| getUrl()        | Get a single image url for an image given a transform, the transform can be a width or a transform object                                     |
| originalHeight  | Full height of original                                                                                                                       |
| originalWidth   | Full width of original                                                                                                                        |
| imageData       | Raw image data from ImageShop                                                                                                                 |
| ratio()         |  Get the original image width to height ratio                                                                                                 |
| rights          | Image rights from Imageshop document Rights                                                                                                   |
| src()           | Get the image url from a transform with a given width.                                                                                        |
| srcset()        | Get a srcset string with all the images in a transform                                                                                        |
| transform()     | Create one or more transforms for an image. Can receive two parameters, a transform or array of transforms, and a default transform property. |
| transformed     | An array containing all the image transforms, each item has three attributes `url`, `width` and `height`                                      |
| title           | Image title from Imageshop Name                                                                                                               |
| url             | Url to the original image                                                                                                                     |
| value           | Raw field value, comma separated list of image strings `{interface}_{language}_{documentId}`                                                  |

### Image transforms

When you use the `getUrl` or the `transform` method you can pass an integer which will be used to set the image width, or a transform object with the attributes:

- `width`
- `height`
- `width` and `height`
- `width` and `ratio`
- `height` and `ratio`

If only the width or height is defined with no ratio, the image will keep it's original ratio. Width and height should be integers, ratio should be a float.

```
{% set transform1 = {
  width: 100,
  height: 50
} %}

{% set transform2 = {
  width: 100,
  ratio: 4/3
} %}
```

### Get single image url

We can use the `getUrl` method with an integer, or a transform object. Remember that the Imageshop field always returns an array, if you only want one image select it with `.one()`.

```twig
{% set image = entry.imageshopImage.one() %}

{# Get the full original image url #}
{{ image.url }}

{# Get image with width of 600 px #}
{{ image.getUrl(600) }}

{# Get image with height of 600 px #}
{{ image.getUrl({height: 600}) }}

{# Get image with width 600px and height of 400 px #}
{{ image.getUrl({width: 600, height: 400}) }}

{# Get image with width 600px and 16:9 aspect ratio #}
{{ image.getUrl({width: 600, ratio: 16/9}) }}
```

### Show one image

In this example we are creating transforms for image widths of 300 and 400 pixels, keeping the original image aspect ratio.

We access the url by using the `src` method and specifying one of the widths. We can also use the `srcset` method to get all the transforms in a valid srcset-format.

```twig
{% set image = entry.imageshopImage.one().transform([300, 400]) %}

<img src="{{ image.base64pixel }}" srcset="{{ image.srcset }}" width="400" height="{{ ( 400 / image.ratio )|round }}" alt="{{ image.alt }}">
```

### Show multiple images

In this example we create two transforms for each image; 300px and 400 px wide. The images keep the original image aspect ratio.:

```twig
{% set images = entry.imageshopImage.transform([300, 400]) %}

{% for image in images %}
  <img src="{{ image.base64pixel }}" srcset="{{ image.srcset }}" width="400" height="{{ ( 400 / image.ratio )|round }}" alt="{{ image.alt }}">
{% endfor %}
```

If we want to force an aspect ratio on the images we can pass an optional default parameter that is applied to all the transforms for all the images.

In this case all the images will get the same widescreen (16:9) aspect ratio.

```twig
{% set images = entry.imageshopImage.transform([300, 400], {ratio: 16/9}) %}

{% for image in images %}
  <img src="{{ image.base64pixel }}" srcset="{{ image.srcset }}" width="400" height="{{ ( 400 / ( 16 / 9 ) )|round }}" alt="{{ image.alt }}">
{% endfor %}
```

### Element API

You can access images from the Imageshop field similarly in PHP:

```php
if( $image = $entry->imageshopImage->one() )
{
  $imageUrl = $image->getUrl(['width' => 400, 'ratio' => 16/9]);
  $imageAlt = $image->alt;
}
```

## Caching images

The Imageshop API doesn't seem to cache the image url it creates, and because of this the methods used in this plugin can be fairly slow. Therefore we cache the Imageshop API-responses for a year so that repeat request are much quicker.

## Imageshop Roadmap

Some things to do, and ideas for potential features:

- Let the user upload images to Imageshop from the plugin

The transform syntax and logic used in this plugin was inspired by [Fred Carlsen's](https://github.com/sjelfull/) [Imgix plugin for Craft CMS](https://github.com/sjelfull/imgix).
