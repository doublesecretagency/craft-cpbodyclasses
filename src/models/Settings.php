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

namespace doublesecretagency\cpbodyclasses\models;

use craft\base\Model;

/**
 * Class Settings
 * @since 2.0.0
 */
class Settings extends Model
{

    /** @var bool Whether to show user group classes. */
    public $showUserGroups = true;

    /** @var bool Whether to show a class for admins. */
    public $showUserAdmin = true;

    /** @var bool Whether to show a class for the user's ID. */
    public $showUserId = false;

    /** @var bool When viewing another user's profile, whether to show their user group classes. */
    public $showProfileUserGroups = false;

    /** @var bool When viewing another user's profile, whether to show a class if they are an admin. */
    public $showProfileUserAdmin = false;

    /** @var bool When viewing another user's profile, whether to show a class for their user ID. */
    public $showProfileId = false;

    /** @var bool Whether to show a class for the current section. */
    public $showCurrentSection = false;

    /** @var bool Whether to show a class for the current page. */
    public $showCurrentPage = false;

    /** @var bool Whether to show a class for entries of a particular section. */
    public $showEntriesSection = false;

    /** @var bool Whether to show a class for entries of a particular site. */
    public $showEntriesSite = false;

    /** @var bool When editing an entry, whether to show a class for the version type. */
    public $showEntryVersion = false;

}
