<?php
/**
 * Control Panel Body Classes plugin for Craft CMS
 *
 * Adds special classes to the Control Panel's <body> tag.
 *
 * @author    Double Secret Agency
 * @link      https://www.doublesecretagency.com/
 * @copyright Copyright (c) 2015 Double Secret Agency
 */

namespace doublesecretagency\cpbodyclasses;

use Craft;
use craft\base\Plugin;

use doublesecretagency\cpbodyclasses\models\Settings;
use doublesecretagency\cpbodyclasses\services\BodyClasses;
use doublesecretagency\cpbodyclasses\web\assets\SettingsAssets;

/**
 * Class CpBodyClasses
 * @since 2.0.0
 */
class CpBodyClasses extends Plugin
{

    /** @var Plugin  $plugin  Self-referential plugin property. */
    public static $plugin;

    /** @var bool  $hasCpSettings  The plugin has a settings page. */
    public $hasCpSettings = true;

    /** @inheritDoc */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Load plugin components
        $this->setComponents([
            'bodyClasses' => BodyClasses::class,
        ]);

        // If control panel page
        if (Craft::$app->getRequest()->getIsCpRequest()) {

            // Apply body classes as needed
            Craft::$app->getView()->hook('cp.layouts.base', function(array &$context) {

                // Get plugin settings
                $s = CpBodyClasses::$plugin->getSettings();

                // Load class services
                $c = CpBodyClasses::$plugin->bodyClasses;

                // Add all requested groups
                if ($s->showUserGroups)        {$c->classUserGroups();}
                if ($s->showUserAdmin)         {$c->classUserAdmin();}
                if ($s->showUserId)            {$c->classUserId();}
                if ($s->showProfileUserGroups) {$c->classProfileUserGroups();}
                if ($s->showProfileUserAdmin)  {$c->classProfileUserAdmin();}
                if ($s->showProfileId)         {$c->classProfileId();}
                if ($s->showCurrentSection)    {$c->classCurrentSection();}
                if ($s->showCurrentPage)       {$c->classCurrentPage();}
                if ($s->showEntriesSection)    {$c->classEntriesSection();}
                if ($s->showEntriesSite)       {$c->classEntriesSite();}
                if ($s->showEntryVersion)      {$c->classEntryVersion();}

                // If any body classes have been set, apply them
                if (!empty($c->bodyClasses)) {
                    // Determine current structure of body classes
                    if (is_array($context['bodyClass'])) {
                        // Craft >3.5, merge with existing array
                        $context['bodyClass'] = array_merge($context['bodyClass'], $c->bodyClasses);
                    } else if (is_string($context['bodyClass'])) {
                        // Craft <3.4, merge with existing string
                        $allClasses = implode(' ', $c->bodyClasses);
                        $context['bodyClass'] .= " $allClasses";
                    } else {
                        // Fallback, set as new array
                        $context['bodyClass'] = $c->bodyClasses;
                    }
                }

            });

        }

    }

    /**
     * @return Settings  Plugin settings model.
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @return string  The fully rendered settings template.
     */
    protected function settingsHtml(): string
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(SettingsAssets::class);
        return $view->renderTemplate('cp-body-classes/settings', [
            'settings' => $this->getSettings(),
        ]);
    }

}
