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

namespace doublesecretagency\cpbodyclasses\services;

use Craft;
use craft\base\Component;
use craft\elements\User;

/**
 * Class BodyClasses
 * @since 2.0.0
 */
class BodyClasses extends Component
{

    /**
     * @var array Collection of classes to be applied.
     */
    public array $bodyClasses = [];

    /**
     * User group classes.
     */
    public function classUserGroups(): void
    {
        // Get current user
        $user = Craft::$app->user->getIdentity();
        // Add user group classes
        $this->_addUserGroupClasses('usergroup', $user);
    }

    /**
     * User admin class.
     */
    public function classUserAdmin(): void
    {
        // Get current user
        $user = Craft::$app->user->getIdentity();
        // Add admin class
        $this->_addUserAdminClass('userlevel', $user);
    }

    /**
     * User ID class.
     */
    public function classUserId(): void
    {
        // Get current user
        $user = Craft::$app->user->getIdentity();
        // Add ID class
        $this->_addUserIdClass('userid', $user);
    }

    /**
     * Profile's user group classes.
     */
    public function classProfileUserGroups(): void
    {
        // Get user in profile
        $user = $this->_getProfileUser();
        // Add user group classes
        $this->_addUserGroupClasses('profilegroup', $user);
    }

    /**
     * Profile's user admin class.
     */
    public function classProfileUserAdmin(): void
    {
        // Get user in profile
        $user = $this->_getProfileUser();
        // Add admin class
        $this->_addUserAdminClass('profilelevel', $user);
    }

    /**
     * Profile's user ID class.
     */
    public function classProfileId(): void
    {
        // Get user in profile
        $user = $this->_getProfileUser();
        // Add ID class
        $this->_addUserIdClass('profileid', $user);
    }

    /**
     * Current CP section class.
     */
    public function classCurrentSection(): void
    {
        // Get request service
        $request = Craft::$app->request;

        // Get first segment
        $section = $request->getSegment(1);

        // If first segment is `content` and using Craft 5.9+
        if ($section === 'content' && !$this->_pre59()) {
            // Get the second segment instead
            $section = $request->getSegment(2);
        }

        // If section can be determined
        if ($section) {
            $this->_addClass('currentsection', $section);
        }
    }

    /**
     * Current page class.
     */
    public function classCurrentPage(): void
    {
        // Get all segments
        $segments = Craft::$app->request->getSegments();

        // Get whether entry is fresh
        $fresh = Craft::$app->getRequest()->getQueryParam('fresh');

        // If entry is fresh
        if ($fresh) {
            // Get last segment
            $lastSegment = array_pop($segments);
            // If last segment is numeric, replace it
            $segments[] = (is_numeric($lastSegment) ? 'new' : $lastSegment);
        }

        // Compile page class
        $page = implode('-', $segments);
        $this->_addClass('currentpage', $page);
    }

    /**
     * Entries section class.
     */
    public function classEntriesSection(): void
    {
        // Whether Craft version is pre-5.9 (entries moved from /entries/ to /content/entries/)
        $pre59 = $this->_pre59();

        // Get request service
        $request = Craft::$app->request;

        // Get URL segments
        $section = $request->getSegment($pre59 ? 1 : 2);
        $channel = $request->getSegment($pre59 ? 2 : 3);

        // If entries section and specific channel
        if ('entries' === $section && $channel) {
            $this->_addClass('entriessection', $channel);
        }
    }

    /**
     * Entries site class.
     */
    public function classEntriesSite(): void
    {
        // Whether Craft version is pre-5.9 (entries moved from /entries/ to /content/entries/)
        $pre59 = $this->_pre59();

        // Get request service
        $request = Craft::$app->request;

        // Get URL segments
        $section = $request->getSegment($pre59 ? 1 : 2);
        $entry   = $request->getSegment($pre59 ? 3 : 4);
        $site    = $request->getSegment($pre59 ? 4 : 5);

        // If entries section and specific entry
        if ('entries' === $section && $entry) {
            if (!$site) {
                // If no site detected, assume primary site
                $site = Craft::$app->getSites()->getPrimarySite()->handle;
            }
            $this->_addClass('entriessite', $site);
        }
    }

    /**
     * Entry version class.
     */
    public function classEntryVersion(): void
    {
        // Whether Craft version is pre-5.9 (entries moved from /entries/ to /content/entries/)
        $pre59 = $this->_pre59();

        // Get request service
        $request = Craft::$app->request;

        // Get URL segments
        $section = $request->getSegment($pre59 ? 1 : 2);
        $entry   = $request->getSegment($pre59 ? 3 : 4);

        // If not editing an entry, bail
        if ('entries' !== $section || !$entry) {
            return;
        }

        // Get query parameters
        $fresh      = Craft::$app->getRequest()->getQueryParam('fresh');
        $draftId    = Craft::$app->getRequest()->getQueryParam('draftId');
        $revisionId = Craft::$app->getRequest()->getQueryParam('revisionId');

        // Set which entry version
        if ($fresh) {
            // Brand new
            $version = 'new';
        } else if ($draftId) {
            // Draft of existing
            $version = 'draft';
        } else if ($revisionId) {
            // Previously published revision
            $version = 'revision';
        } else {
            // Current version
            $version = 'current';
        }

        // Compile page class
        $this->_addClass('entryversion', $version);
    }

    // ======================================================================== //

    /**
     * Append body class to master array.
     *
     * @param string $prefix Prefix to show the class purpose.
     * @param string $class Non-prefixed class.
     */
    private function _addClass(string $prefix, string $class): void
    {
        $this->bodyClasses[] = $prefix.'-'.$class;
    }

    /**
     * Add classes for user groups.
     *
     * @param string $prefix Either `usergroup` or `profilegroup`.
     * @param null|User $user The user account to be checked.
     */
    private function _addUserGroupClasses(string $prefix, ?User $user): void
    {
        // Get user groups
        if ($user) {
            foreach ($user->getGroups() as $group) {
                $this->_addClass($prefix, $group->handle);
            }
        }
    }

    /**
     * Add class for admin.
     *
     * @param string $prefix Either `userlevel` or `profilelevel`.
     * @param null|User $user The user account to be checked.
     */
    private function _addUserAdminClass(string $prefix, ?User $user): void
    {
        // If user is an admin
        if ($user && $user->admin) {
            $this->_addClass($prefix, 'admin');
        }
    }

    /**
     * Add class for user ID.
     *
     * @param string $prefix Either `userid` or `profileid`.
     * @param null|User $user The user account to be checked.
     */
    private function _addUserIdClass(string $prefix, ?User $user): void
    {
        // Get user ID
        if ($user) {
            $this->_addClass($prefix, $user->id);
        }
    }

    /**
     * Whether the current Craft version is pre-5.9.
     * In Craft 5.9+, entries moved from /entries/ to /content/entries/.
     */
    private function _pre59(): bool
    {
        return version_compare(Craft::$app->version, '5.9', '<');
    }

    /**
     * Get user of profile being viewed.
     *
     * @return null|User A valid user model for the profile being viewed, or null if invalid.
     */
    private function _getProfileUser(): ?User
    {
        // Get URL segments
        $section = Craft::$app->request->getSegment(1);
        $userId  = Craft::$app->request->getSegment(2);
        // Determine whether page is a profile
        $myaccount = ('myaccount' === $section);
        $profile   = (('users' === $section) && is_numeric($userId));
        // If not viewing a profile
        if (!$myaccount && !$profile) {
            return null;
        }
        // If viewing your own profile
        if ($myaccount) {
            return Craft::$app->user->getIdentity();
        }
        // Return other user
        return Craft::$app->getUsers()->getUserById($userId);
    }

}
