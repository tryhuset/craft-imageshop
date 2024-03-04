<?php

/**
 * Imageshop plugin for Craft CMS 3
 *
 * Integrate with an Imageshop account and use Imageshop resources in Craft
 *
 * @link      https://vangenplotz.no/
 * @copyright Copyright (c) 2018 Vangen & Plotz AS
 */

namespace trydig\craftimageshop\controllers;

use trydig\craftimageshop\CraftImageshop;

use Craft;
use craft\web\Controller;

/**
 * Search Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Vangen & Plotz AS
 * @package   Imageshop
 * @since     0.0.1
 */
class SearchController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Search images
     * actions/craft-imageshop/search
     *
     * @return json
     */
    public function actionIndex()
    {
        $interface = Craft::$app->getRequest()->getParam('interface');
        $language = Craft::$app->getRequest()->getParam('language');

        // Category ids, comma separated
        $categoryIds = Craft::$app->getRequest()->getParam('categoryIds');
        $searchString =  "cat:" . (empty($categoryIds) ? '' : $categoryIds);

        // Sub category ids, comma separated
        $subCategoryIds = Craft::$app->getRequest()->getParam('subCategoryIds');
        $searchString .= " sub:" . (empty($subCategoryIds) ? '' : $subCategoryIds);

        // Page number to view 0-indexed
        $page = Craft::$app->getRequest()->getParam('page');
        $searchString .= " page:" . (empty($page) ? 0 : $page);

        // Pagesize, number of documents per page
        $pageSize = Craft::$app->getRequest()->getParam('pagesize');
        $searchString .= " pagesize:" . (empty($pageSize) ? 50 : $pageSize);;

        // Author query
        $author = Craft::$app->getRequest()->getParam('author');
        $searchString .= " author:" . (empty($author) ? '' : $author);

        // Search query
        $query = Craft::$app->getRequest()->getParam('query');
        $searchString .= " " . (empty($query) ? '' : $query);


        return $this->asJson(CraftImageshop::$plugin->soap->search($searchString, $interface, $language));
    }

    /**
     * Show data for a single search result
     * actions/craft-imageshop/search/show
     * Send the documentId as a body param
     *
     * @return json
     */
    public function actionShow()
    {
        $documentId = Craft::$app->getRequest()->getParam('documentId');
        $language = Craft::$app->getRequest()->getParam('language');

        return $this->asJson(CraftImageshop::$plugin->soap->getImageData($documentId, $language));
    }
}
