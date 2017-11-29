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

use yii\web\IdentityInterface;

/**
 * Class BodyClasses
 * @since 2.0.0
 */
class BodyClasses extends Component
{

    /** @var array  $bodyClasses  Collection of classes to be applied. */
    public $bodyClasses = [];

    /**
     * User group classes.
     *
     * @return void
     */
    public function classUserGroups()
    {
        // Get current user
        $user = Craft::$app->user->getIdentity();
        // Add user group classes
        $this->_addUserGroupClasses('usergroup', $user);
    }

    /**
     * User admin class.
     *
     * @return void
     */
    public function classUserAdmin()
    {
        // Get current user
        $user = Craft::$app->user->getIdentity();
        // Add admin class
        $this->_addUserAdminClass('userlevel', $user);
    }

    /**
     * Profile's user group classes.
     *
     * @return void
     */
    public function classProfileUserGroups()
    {
        // Get user in profile
        $user = $this->_getProfileUser();
        // Add user group classes
        $this->_addUserGroupClasses('profilegroup', $user);
    }

    /**
     * Profile's user admin class.
     *
     * @return void
     */
    public function classProfileUserAdmin()
    {
        // Get user in profile
        $user = $this->_getProfileUser();
        // Add admin class
        $this->_addUserAdminClass('profilelevel', $user);
    }

    /**
     * Current CP section class.
     *
     * @return void
     */
    public function classCurrentSection()
    {
        // Get first segment
        $section = Craft::$app->request->getSegment(1);
        // If section can be determined
        if ($section) {
            $this->_addClass('currentsection', $section);
        }
    }

    /**
     * Current page class.
     *
     * @return void
     */
    public function classCurrentPage()
    {
        // Get all segments
        $segments = Craft::$app->request->getSegments();
        // Compile page class
        $page = implode('-', $segments);
        $this->_addClass('currentpage', $page);
    }

    /**
     * Entries section class.
     *
     * @return void
     */
    public function classEntriesSection()
    {
        // Get URL segments
        $section = Craft::$app->request->getSegment(1);
        $channel = Craft::$app->request->getSegment(2);
        // If entries section and specific channel
        if ('entries' == $section && $channel) {
            $this->_addClass('entriessection', $channel);
        }
    }

    // ======================================================================== //

    /**
     * Append body class to master array.
     *
     * @param string  $prefix  Prefix to show the class purpose.
     * @param string  $class   Non-prefixed class.
     *
     * @return void
     */
    private function _addClass($prefix, $class)
    {
        $this->bodyClasses[] = $prefix.'-'.$class;
    }

    /**
     * Add classes for user groups.
     *
     * @param string                   $prefix  Either `usergroup` or `profilegroup`.
     * @param false|IdentityInterface  $user    The user account to be checked.
     *
     * @return void
     */
    private function _addUserGroupClasses($prefix, $user)
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
     * @param string                   $prefix  Either `userlevel` or `profilelevel`.
     * @param false|IdentityInterface  $user    The user account to be checked.
     *
     * @return void
     */
    private function _addUserAdminClass($prefix, $user)
    {
        // If user is an admin
        if ($user && $user->admin) {
            $this->_addClass($prefix, 'admin');
        }
    }

    /**
     * Get user of profile being viewed.
     *
     * @return false|IdentityInterface  A valid user model for the profile being viewed, or false if invalid.
     */
    private function _getProfileUser()
    {
        // Get URL segments
        $section = Craft::$app->request->getSegment(1);
        $userId  = Craft::$app->request->getSegment(2);
        // Determine whether page is a profile
        $myaccount = ('myaccount' == $section);
        $profile   = (('users' == $section) && is_numeric($userId));
        // If not viewing a profile
        if (!$myaccount && !$profile) {
            return false;
        }
        // If viewing your own profile
        if ($myaccount) {
            return Craft::$app->user->getIdentity();
        }
        // Return other user
        return Craft::$app->users->getUserById($userId);
    }

}