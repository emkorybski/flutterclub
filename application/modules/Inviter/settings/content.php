<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(

    array(
        'title' => 'Main Inviter',
        'description' => 'Displays a main inviter form which allows to invite friends. It should be placed at Friend Inviter page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.home-inviter',
    ),

    array(
        'title' => 'People You May Know',
        'description' => 'Suggests friends based on mutual friendship. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.list-suggest',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_People You May Know',
        ),
    ),

    array(
        'title' => 'Top Inviters',
        'description' => 'Displays Top Inviters in your site.',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.list-top-inviters',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Top Inviters',
        ),
    ),

    array(
        'title' => 'Top Referrals',
        'description' => 'Displays Top Referrals in your site.',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.list-top-referrals',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Top Referrals',
        ),
    ),

    array(
        'title' => 'Facebook Friends',
        'description' => 'Suggests friends from Facebook. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.facebook-suggest',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Facebook Friends',
        ),
    ),

    array(
        'title' => 'Facebook Members',
        'description' => 'Show friends from Facebook. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.facebook-members',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Facebook Friends on Site',
        ),
    ),

    array(
        'title' => 'Friended on Facebook and Site',
        'description' => 'Show friends from Facebook. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.facebook-connected-friends',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Friended on Facebook and Site',
        ),
    ),

    array(
        'title' => 'Introduce Yourself',
        'description' => 'Introduce Yourself',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.introduce-yourself',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Introduce Yourself',
        ),
    ),

    array(
        'title' => 'Introduce Member',
        'description' => 'Introduce Member',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.introduce-member',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Introduce Member',
        ),
    ),

    array(
        'title' => 'Profile Introduce',
        'description' => 'Profile Introduce',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.profile-introduce',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Profile Introduce',
        ),
    ),

    array(
        'title' => 'Find More Friends',
        'description' => 'Find More Friends',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.find-more-friends',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Find More Friends',
        ),
    ),

    array(
        'title' => 'Twitter Followers',
        'description' => 'Suggests followers from Twitter. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.twitter-suggest',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Twitter Followers',
        ),
    ),

    array(
        'title' => 'Twitter Members',
        'description' => 'Show followers from Twitter. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.twitter-members',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Twitter Followers on Site',
        ),
    ),

    array(
        'title' => 'Friended on Twitter and Site',
        'description' => 'Show friends from Twitter. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.twitter-connected-friends',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Friended on Twitter and Site',
        ),
    ),

    array(
        'title' => 'LinkedIn Connections',
        'description' => 'Suggests connections from LinkedIn. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.linkedin-suggest',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_LinkedIn Connections',
        ),
    ),

    array(
        'title' => 'LinkedIn Members',
        'description' => 'Show connections from LinkedIn. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.linkedin-members',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_LinkedIn Connections on Site',
        ),
    ),

    array(
        'title' => 'Friended on LinkedIn and Site',
        'description' => 'Show friends from LinkedIn. Please put it on any wished page',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.linkedin-connected-friends',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Friended on LinkedIn and Site',
        ),
    ),
    array(
        'title' => 'Referral Link',
        'description' => 'Displays referral link for the member, please put this widget on any wished page.',
        'category' => 'Inviter',
        'type' => 'widget',
        'name' => 'inviter.referral-link',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'INVITER_Referral Link',
        ),
    )

) ?>