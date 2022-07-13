<?php
/**
 * Wrapper  for jquery.inputmask 3.x
 * Inspired by Yii 2.0 Input Mask
 * 
 *  Porting : jquery.inputmask
 *  Version : 3.1.34
 *   
 *  Date    : 2014-10-26
 *  Author  : Robin Herbots
 *            http://github.com/RobinHerbots/jquery.inputmask
 *            Copyright (c) 2010 - 2014 Robin Herbots
 *            Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 * @author Vilochane <vilochane@gmail.com>
 * @link GitHub https://github.com/Vilochane
 * @link yii http://www.yiiframework.com/forum/index.php/user/223499-vilo/
 * 
 */
class MaskedInput extends CInputWidget {

    /**
     * The user defined id of the masked input
     */
    public $selector = null;

    /**
     * The name of the jQuery plugin to use for this widget.
     */
    const PLUGIN_NAME = 'inputmask';

    /**
     * @var string|array|JsExpression the input mask (e.g. '99/99/9999' for date input). The following characters
     * can be used in the mask and are predefined:
     *
     * - `a`: represents an alpha character (A-Z, a-z)
     * - `9`: represents a numeric character (0-9)
     * - `*`: represents an alphanumeric character (A-Z, a-z, 0-9)
     * - `[` and `]`: anything entered between the square brackets is considered optional user input. This is
     * based on the `optionalmarker` setting in [[clientOptions]].
     *
     * Additional definitions can be set through the [[definitions]] property.
     */
    public $mask;
    
    /**
     * This variable used to override the default settings
     */
    public $defaults;
    
    /**
     * @var array custom mask definitions to use. Should be configured as `maskSymbol => settings`, where
     *
     * - `maskSymbol` is a string, containing a character to identify your mask definition and
     * - `settings` is an array, consisting of the following entries:
     * - `validator`: string, a JS regular expression or a JS function.
     * - `cardinality`: int, specifies how many characters are represented and validated for the definition.
     * - `prevalidator`: array, validate the characters before the definition cardinality is reached.
     * - `definitionSymbol`: string, allows shifting values from other definitions, with this `definitionSymbol`.
     */
    public $definitions;

    /**
     * @var array custom aliases to use. Should be configured as `maskAlias => settings`, where
     *
     * - `maskAlias` is a string containing a text to identify your mask alias definition (e.g. 'phone') and
     * - `settings` is an array containing settings for the mask symbol, exactly similar to parameters as passed in [[clientOptions]].
     */
    public $aliases;

    /**
     * @var array the JQuery plugin options for the input mask plugin.
     * @see https://github.com/RobinHerbots/jquery.inputmask
     */
    public $clientOptions = array();

    /**
     * @var array the HTML attributes for the input tag.
     */
    public $options = array('class' => 'form-control');

    /**
     * @var string the hashed variable to store the pluginOptions
     */
    protected $_hashVar;

    /**
     * Javascript expression for hashed varaible
     */
    private $hashVariableJs;

    /**
     * Javascript expression for maked input
     */
    private $maskedInputJs;

    public function init() {
        if (empty($this->mask) && empty($this->clientOptions['alias'])) {
            throw new InvalidConfigException("Either the 'mask' property or the 'clientOptions[\"alias\"]' property must be set.");
        }
    }

    public function run() {
        $this->publishAssets();
        $this->registerClientScript();
        if ($this->hasModel()) {
            echo CHtml::activeTextField($this->model, $this->attribute, $this->options);
        } else {
            echo CHtml::textField($this->name, $this->value, $this->options);
        }
    }

    public function publishAssets() {
        $assets = dirname(__FILE__) . "/assets";
        $baseUrl = Yii::app()->assetManager->publish($assets);
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        if (is_dir($assets)) {
            $cs->registerScriptFile($baseUrl . '/dist/jquery.inputmask.bundle.js', CClientScript::POS_HEAD);
        } else {
            throw new Exception("Jquery input mask - couldn't publish assets");
        }
    }

    /**
     * Generates a hashed variable to store the plugin `clientOptions`. Helps in reusing the variable for similar
     * options passed for other widgets on the same page. The following special data attributes will also be
     * setup for the input widget, that can be accessed through javascript:
     *
     * - 'data-plugin-options' will store the hashed variable storing the plugin options.
     * - 'data-plugin-name' the name of the plugin
     *
     * @param View $view the view instance
     * @author [Thiago Talma](https://github.com/thiagotalma)
     */
    protected function hashPluginOptions() {
        $encOptions = empty($this->clientOptions) ? '{}' : CJSON::encode($this->clientOptions);
        $this->_hashVar = self::PLUGIN_NAME . '_' . hash('crc32', $encOptions). uniqid();
        $this->hashVariableJs = "var {$this->_hashVar} = {$encOptions};\n";
        $this->options['data-plugin-name'] = self::PLUGIN_NAME;
        $this->options['data-plugin-options'] = $this->_hashVar;
    }

    /**
     * Initializes client options
     */
    protected function initClientOptions() {
        $options = $this->clientOptions;
        foreach ($options as $key => $value) {
            if (in_array($key, ['oncomplete', 'onincomplete', 'oncleared', 'onKeyUp', 'onKeyDown', 'onBeforeMask',
                        'onBeforePaste', 'onUnMask', 'isComplete', 'determineActiveMasksetIndex']) && !$value instanceof CJavaScriptExpression) {
                $options[$key] = $value;
            }
        }
        $this->clientOptions = $options;
    }

    /**
     * Registers the needed client script and options.
     */
    public function registerClientScript() {
        $this->initClientOptions();
        $cs = Yii::app()->clientScript;
        $this->maskedInputJs = '';
        if (!empty($this->mask)) {
            $this->clientOptions['mask'] = $this->mask;
        }
        if (is_array($this->defaults) && !empty($this->defaults)) {
            $this->maskedInputJs .= '$.extend($.' . self::PLUGIN_NAME . '.defaults, ' . CJSON::encode($this->defaults) . ");\n";
        }
        
        if (is_array($this->definitions) && !empty($this->definitions)) {
            $this->maskedInputJs .= '$.extend($.' . self::PLUGIN_NAME . '.defaults.definitions, ' . CJSON::encode($this->definitions) . ");\n";
        }

        if (is_array($this->aliases) && !empty($this->aliases)) {
            $this->maskedInputJs .= '$.extend($.' . self::PLUGIN_NAME . '.defaults.aliases, ' . CJSON::encode($this->aliases) . ");\n";
        }
        /** setting the plugin option hash varaible */
        $this->hashPluginOptions();
        if ($this->selector === null) {
            list($this->name, $this->id) = $this->resolveNameId();
            $this->selector = '#' . $this->id;
        }
        $this->maskedInputJs .= '$("' . $this->selector . '").' . self::PLUGIN_NAME . "(" . $this->_hashVar . ");\n";
        /**
         * Adding the javascript when document is ready
         */
        $cs->registerScript("masked-input-javascript-".$this->_hashVar, ""
                . "$this->hashVariableJs"
                . "$this->maskedInputJs", CClientScript::POS_READY);
    }

}
