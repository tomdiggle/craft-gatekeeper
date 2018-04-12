<?php
/**
 * Gatekeeper plugin for Craft CMS 3.x
 *
 * Protect your Craft CMS website from access with a universal password.
 *
 * @link      http://tomdiggle.com
 * @copyright Copyright (c) 2018 Tom Diggle
 */

namespace tomdiggle\gatekeeper\controllers;

use tomdiggle\gatekeeper\Gatekeeper;
use tomdiggle\gatekeeper\assetbundles\gatekeeper\GatekeeperAsset;

use Craft;
use craft\web\View;
use craft\web\Session;
use craft\web\Controller;

use yii\web\Cookie;
use yii\web\Response;

/**
 * Gatekeeper Controller
 * 
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Tom Diggle
 * @package   Gatekeeper
 * @since     0.1.0
 */
class GatekeeperController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'login'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/gatekeeper/gatekeeper
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Gatekeeper::$plugin->isAuthenticated()) {
            return $this->redirect('/');
        }
        
        $oldMode = $this->view->getTemplateMode();
        $this->view->setTemplateMode(View::TEMPLATE_MODE_CP);

        $rendered = $this->view->renderTemplate('gatekeeper/_frontend/gatekeeper');
        
        $this->view->setTemplateMode($oldMode);

        return $rendered;
    }

    /**
     * Handle a request going to our plugin's actionLogin URL,
     * e.g.: actions/gatekeeper/gatekeeper/login
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $password = Craft::$app->getRequest()->post('password');
        $gatekeeperPassword = Gatekeeper::$settings->password;
        
        if ($password === $gatekeeperPassword) {
            $cookie = new Cookie(['name' => 'gatekeeper']);
            $cookie->value = 'loggedin';
            $cookie->expire = time() + 3600;
            Craft::$app->getResponse()->getCookies()->add($cookie);

            return $this->redirect('/');
        }

        return $this->actionIndex();
    }
}
