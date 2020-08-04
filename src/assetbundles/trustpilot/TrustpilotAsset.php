<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\trustpilot\assetbundles\trustpilot;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class TrustpilotAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@scaramangagency/trustpilot/assetbundles/trustpilot/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Trustpilot.js',
        ];

        $this->css = [
            'css/Trustpilot.css',
        ];

        parent::init();
    }
}
