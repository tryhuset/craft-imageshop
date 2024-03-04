<?php

/**
 * Imageshop plugin for Craft CMS 4
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
 * Categories Controller
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
class CategoriesController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected array|bool|int
        $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Get all available categories
     * actions/craft-imageshop/categories
     *
     * @return json
     */
    public function actionIndex()
    {
        return $this->asJson(CraftImageshop::$plugin->soap->categories());
    }
}
