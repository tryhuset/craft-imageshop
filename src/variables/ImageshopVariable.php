<?php

/**
 * Imageshop plugin for Craft CMS 3
 *
 * Integrate with an Imageshop account and use Imageshop resources in Craft
 *
 * @link      https://vangenplotz.no/
 * @copyright Copyright (c) 2018 Vangen & Plotz AS
 */

namespace trydig\craftimageshop\variables;

use trydig\craftimageshop\CraftImageshop;

use Craft;

/**
 * Imageshop Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.imageshop }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Vangen & Plotz AS
 * @package   Imageshop
 * @since     0.0.1
 */
class ImageshopVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Get an array with all the images from a field converted to Imageshop models
     *
     *     {{ craft.imageshop.all(documentsString) }}
     *
     * @param string $documentsString value from Imageshop field
     * @return array of Imagshop models
     */
    public function all($documentsString, $transforms = [], $defaultOptions = [])
    {
        $documents = explode(',', $documentsString);
        $imageArray = [];

        foreach ($documents as $document) {
            $imageArray[] = CraftImageshop::$plugin->image->transformImage($document, $transforms, $defaultOptions);
        }

        return $imageArray;
    }

    /**
     * Get a single image from an Imageshop field converted to an Imageshop model
     *
     *     {{ craft.imageshop.one(documentsString) }}
     *
     * @param string $documentsString value from Imageshop field
     * @return string
     */
    public function one($documentsString, $transforms = [], $defaultOptions = [])
    {
        $document = explode(',', $documentsString);

        return CraftImageshop::$plugin->image->transformImage($document[0], $transforms, $defaultOptions);
    }

    public function getValueString($ImageshopModelArray)
    {
        return CraftImageshop::$plugin->image->serialize($ImageshopModelArray);
    }
}
