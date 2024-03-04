<?php

/**
 * Imageshop plugin for Craft CMS 3
 *
 * Integrate with an Imageshop account and use Imageshop resources in Craft
 *
 * @link      https://vangenplotz.no/
 * @copyright Copyright (c) 2018 Vangen & Plotz AS
 */

namespace trydig\craftimageshop\services;

use trydig\craftimageshop\CraftImageshop;

use Craft;
use craft\base\Component;
use trydig\craftimageshop\models\ImageModel;

/**
 * Image Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Vangen & Plotz AS
 * @package   Imageshop
 * @since     0.0.1
 */
class Image extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Transform the image, and return an image model
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->image->transformImage($documentId, $transforms)
     *
     * @return ImageModel
     */
    public function transformImage($document = null, $transforms = [], $defaultOptions = [])
    {
        if (!$document) {
            return null;
        }

        $documentComponents = explode('_', $document);

        if (count($documentComponents) < 3) {
            return null;
        }

        $documentInterface = $documentComponents[0];
        $documentLanguage = $documentComponents[1];
        $documentId = $documentComponents[2];


        return new ImageModel($documentId, $documentInterface, $documentLanguage, $transforms, $defaultOptions);
    }

    /**
     * Get the image data for the document Id
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->image->getImageData($documentId)
     *
     * @return simpleXML
     */
    public function getImageData($documentId = null)
    {
        if (!$documentId) {
            return null;
        }

        $imageData = CraftImageshop::$plugin->soap->getImageData($documentId);

        if (!$imageData || property_exists($imageData, 'GetDocumentByIdResponse') == false) {
            return null;
        }

        return $imageData->GetDocumentByIdResponse->GetDocumentByIdResult ?? null;
    }

    /**
     * Get image transform permalink with document id, width and height
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->image->getImageTransform($documentId, $width, $height)
     *
     * @return simpleXML
     */
    public function getImageTransform($documentId = null, $width = null, $height = null)
    {

        if (!$documentId || !$width || !$height) {
            return null;
        }

        return CraftImageshop::$plugin->soap->getImagePermalink($documentId, $width, $height);
    }

    /**
     * Serialize ImageshopModelArray
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->image->serialize($ImageshopModelArray)
     *
     * @return string
     */
    public function serialize($ImageshopModelArray)
    {
        $stringArray = [];

        foreach ($ImageshopModelArray->all() as $ImageshopModel) {
            $stringArray[] = (string)$ImageshopModel['value'];
        }

        return implode(',', $stringArray);
    }
}
