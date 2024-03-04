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

/**
 * Soap Service
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
class Soap extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Return available categories for the interface
     * https://webservices.imageshop.no/V4.asmx?op=GetCategoriesTree
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->soap->categories()
     *
     * @return null | Array
     */
    public function categories()
    {

        $settings = CraftImageshop::$plugin->settings;
        $token = $settings->token;
        $interfaceName = $settings->interfaceName;
        $language = $settings->language;

        // If no token is sent or set in settings, return null
        if (empty($token)) {
            return null;
        }

        $action = 'http://imageshop.no/V4/GetCategoriesTree';

        $xml  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n";
        $xml .= "  <soap:Body>\n";
        $xml .= "    <GetCategoriesTree xmlns=\"http://imageshop.no/V4\">\n";
        $xml .= "      <token>" . $token . "</token>\n";
        $xml .= "      <interfacename>" . $interfaceName . "</interfacename>\n";
        $xml .= "      <language>" . $language . "</language>\n";
        $xml .= "    </GetCategoriesTree>\n";
        $xml .= "  </soap:Body>\n";
        $xml .= "</soap:Envelope>";

        return $this->_request($action, $xml);
    }


    /**
     * Return available interfaces for the token
     * https://webservices.imageshop.no/V4.asmx?op=GetInterfaces
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->soap->interfaces()
     *
     * @return null | Array
     */
    public function interfaces($token = '')
    {
        if (empty($token)) {
            $settings = CraftImageshop::$plugin->settings;
            $token = $settings->token;
        }

        // If no token is sent or set in settings, return null
        if (empty($token)) {
            return null;
        }

        $action = 'http://imageshop.no/V4/GetInterfaces';

        $xml  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n";
        $xml .= "  <soap:Body>\n";
        $xml .= "    <GetInterfaces xmlns=\"http://imageshop.no/V4\">\n";
        $xml .= "      <token>" . $token . "</token>\n";
        $xml .= "    </GetInterfaces>\n";
        $xml .= "  </soap:Body>\n";
        $xml .= "</soap:Envelope>";

        return $this->_request($action, $xml);
    }

    /**
     * Get permalink for an image
     * https://webservices.imageshop.no/V4.asmx?op=CreatePermaLinkFromDocumentId
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->soap->getImage()
     *
     * @return Array
     */
    public function getImage($documentId)
    {
        $settings = CraftImageshop::$plugin->settings;
        $token = $settings->token;
        $action = 'http://imageshop.no/V4/CreatePermaLinkFromDocumentId';

        $width = 100;
        $height = 100;

        $xml  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n";
        $xml .= "  <soap:Body>\n";
        $xml .= "    <CreatePermaLinkFromDocumentId xmlns=\"http://imageshop.no/V4\">\n";
        $xml .= "      <token>" . $token . "</token>\n";
        $xml .= "      <documentid>" . $documentId . "</documentid>\n";
        $xml .= "      <width>" . $width . "</width>\n";
        $xml .= "      <height>" . $height . "</height>\n";
        $xml .= "    </CreatePermaLinkFromDocumentId>\n";
        $xml .= "  </soap:Body>\n";
        $xml .= "</soap:Envelope>";

        // Cache image transform for 31536000s (365 days * 24 hours * 60 minutes * 60 seconds)
        // This request is quite slow, so we want it cached for a long time once made
        return $this->_request($action, $xml, 31536000);
    }

    /**
     * Return the image data for a document ID
     * https://webservices.imageshop.no/V4.asmx?op=GetDocumentById
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->soap->getImageData($documentId, $language)
     *
     * @return Array
     */
    public function getImageData($documentId, $language = null)
    {
        $settings = CraftImageshop::$plugin->settings;
        $token = $settings->token;
        $language = $language ?: $settings->language;
        $action = 'http://imageshop.no/V4/GetDocumentById';

        $xml  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n";
        $xml .= "  <soap:Body>\n";
        $xml .= "    <GetDocumentById xmlns=\"http://imageshop.no/V4\">\n";
        $xml .= "      <token>" . $token . "</token>\n";
        $xml .= "      <language>" . $language . "</language>\n";
        $xml .= "      <DocumentID>" . $documentId . "</DocumentID>\n";
        $xml .= "    </GetDocumentById>\n";
        $xml .= "  </soap:Body>\n";
        $xml .= "</soap:Envelope>";

        return $this->_request($action, $xml);
    }

    /**
     * Return the image data for a document ID
     * https://webservices.imageshop.no/V4.asmx?op=CreatePermaLinkFromDocumentId
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->soap->getImagePermalink($documentId, $width, $height)
     *
     * @return Array
     */
    public function getImagePermalink($documentId, $width, $height)
    {
        $settings = CraftImageshop::$plugin->settings;
        $token = $settings->token;
        $action = 'http://imageshop.no/V4/CreatePermaLinkFromDocumentId';

        $xml  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n";
        $xml .= "  <soap:Body>\n";
        $xml .= "    <CreatePermaLinkFromDocumentId xmlns=\"http://imageshop.no/V4\">\n";
        $xml .= "      <token>" . $token . "</token>\n";
        $xml .= "      <documentid>" . $documentId . "</documentid>\n";
        $xml .= "      <width>" . $width . "</width>\n";
        $xml .= "      <height>" . $height . "</height>\n";
        $xml .= "    </CreatePermaLinkFromDocumentId>\n";
        $xml .= "  </soap:Body>\n";
        $xml .= "</soap:Envelope>";

        // Cache image transform for 31536000s (365 days * 24 hours * 60 minutes * 60 seconds)
        // This request is quite slow, so we want it cached for a long time once made
        return $this->_request($action, $xml, 31536000);
    }

    /**
     * Search the interface for images or other document types
     * https://webservices.imageshop.no/V4.asmx?op=Search
     *
     * From any other plugin file, call it like this:
     *
     *     CraftImageshop::$plugin->soap->search()
     *
     * @return Array
     */
    public function search($query = '', $interfaceName = null, $language = null,  $documentType = 'IMAGE')
    {
        $settings = CraftImageshop::$plugin->settings;
        $token = $settings->token;
        $interfaceName = $interfaceName ?: $settings->interfaceName;
        $language = $language ?: $settings->language;

        $action = 'http://imageshop.no/V4/Search';

        $xml  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n";
        $xml .= "  <soap:Body>\n";
        $xml .= "    <Search xmlns=\"http://imageshop.no/V4\">\n";
        $xml .= "      <token>" . $token . "</token>\n";
        $xml .= "      <interfacename>" . $interfaceName . "</interfacename>\n";
        $xml .= "      <language>" . $language . "</language>\n";
        $xml .= "      <querystring>" . $query . "</querystring>\n";
        $xml .= "      <documentType>" . $documentType . "</documentType>\n";
        $xml .= "    </Search>\n";
        $xml .= "  </soap:Body>\n";
        $xml .= "</soap:Envelope>";

        return $this->_request($action, $xml);
    }


    // Private Methods
    // =========================================================================

    /**
     * @prop $cacheDuration int seconds (86400 = 24h)
     * 
     * @return SimpleXML
     */
    private function _request($action, $xml, $cacheDuration = 86400)
    {
        $url = 'https://webservices.imageshop.no/V4.asmx';

        $headers = [
            'POST /V4.asmx HTTP/1.1',
            'Host: webservices.imageshop.no',
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($xml),
            'SOAPAction: ' . $action
        ];

        $cacheKey = md5($url . implode(', ', $headers) . $xml);


        if (($cached = Craft::$app->getCache()->get($cacheKey)) !== false) {
            return $cached;
        }

        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($curl);
            curl_close($curl);

            $response1 = str_replace("<soap:Body>", "", $response);
            $response2 = str_replace("</soap:Body>", "", $response1);

            $result = json_decode(json_encode(simplexml_load_string($response2)));
        } catch (\Throwable $e) {
            Craft::warning("Couldn't get SOAP response: {$e->getMessage()}", __METHOD__);

            $result = null;

            // Set shorter cache duraction
            $cacheDuration = 300; // 5 minutes
        }

        Craft::$app->getCache()->set($cacheKey, $result, $cacheDuration);

        return $result;
    }
}
