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
 * Interface Controller
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
class InterfaceController extends Controller
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
     * Return interface options for token
     * e.g.: actions/craft-imageshop/interface
     *
     * @return json
     */
    public function actionIndex()
    {
        $token = Craft::$app->getRequest()->getParam('token');

        if (!$token) {
            $settings = CraftImageshop::$plugin->settings;
            $token = $settings->token;
        }

        if (!$token) {
            return null;
        }

        return $this->asJson(CraftImageshop::$plugin->soap->interfaces($token));
    }
}
