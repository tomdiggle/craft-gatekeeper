<?php
/**
 * Gatekeeper plugin for Craft CMS 3.x
 *
 * Protect your craftcms website with a simple password form.
 *
 * @link      http://tomdiggle.com
 * @copyright Copyright (c) 2018 Tom Diggle
 */

namespace tomdiggle\gatekeeper\models;

use tomdiggle\gatekeeper\Gatekeeper;

use Craft;
use craft\base\Model;

/**
 * Gatekeeper Settings Model
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Tom Diggle
 * @package   Gatekeeper
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $password = '';

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['password', 'string', 'min' => 8],
            ['password', 'default', 'value' => ''],
            [['password'], 'required'],
        ];
    }
}
