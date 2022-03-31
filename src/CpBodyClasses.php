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
use craft\base\Model;
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

    /**
     * @var Plugin Self-referential plugin property.
     */
    public static Plugin $plugin;

    /**
     * @var bool The plugin has a settings page.
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Load plugin components
        $this->setComponents([
            'bodyClasses' => BodyClasses::class,
        ]);

        // If control panel page, load body classes
        if (Craft::$app->getRequest()->getIsCpRequest()) {
            $this->_bodyClasses();
        }
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(SettingsAssets::class);
        return $view->renderTemplate('cp-body-classes/settings', [
            'settings' => $this->getSettings(),
        ]);
    }

    // ========================================================================= //

    /**
     * Load all specified body classes.
     */
    private function _bodyClasses(): void
    {
        // Apply body classes as needed
        Craft::$app->getView()->hook(
            'cp.layouts.base',
            static function(array &$context) {

                /** @var Settings $s */
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

                // If no body classes have been set, bail
                if (empty($c->bodyClasses)) {
                    return;
                }

                // Append body classes to bodyAttributes.class
                array_push($context['bodyAttributes']['class'], ...$c->bodyClasses);

            }
        );
    }

}
