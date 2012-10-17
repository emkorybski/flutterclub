<?php return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'inviter',
        'version' => '4.2.3p3',
        'path' => 'application/modules/Inviter',
        'title' => 'Friends Inviter',
        'description' => 'Friend Inviter',
        'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
        'meta' =>
        array(
            'title' => 'Friends Inviter',
            'description' => 'Friend Inviter',
            'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
        ),
        'callback' => array(
            'path' => 'application/modules/Inviter/settings/install.php',
            'class' => 'Inviter_Installer',
        ),
        'actions' => array(
            'preinstall',
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'directories' =>
        array(
            'application/modules/Inviter',
            'application/libraries/Zend/Oauth',
        ),
        'files' =>
        array(
            'application/languages/en/inviter.csv',
            'application/libraries/Zend/Oauth.php',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserCreateAfter',
            'resource' => 'Inviter_Plugin_Core',
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Inviter_Plugin_Core',
        )
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(

        'inviter_general' => array(
            'route' => 'invite',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'index',
                'action' => 'index',
            ),
        ),

        'inviter_referral' => array(
            'route' => 'referral-code/:code',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'referrals',
                'action' => 'referral',
                'code' => 0
            ),
        ),

        'inviter_facebook' => array(
            'route' => 'inviter-facebook',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'facebook',
                'action' => 'index',
            ),
        ),

        'page_inviter' => array(
            'route' => 'page-inviter/*',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'index',
                'action' => 'pageinviter',
            ),
        ),

        'page_inviter_members' => array(
            'route' => 'page-inviter/members/*',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'index',
                'action' => 'pagemembers'
            )
        ),
        'page_inviter_contacts' => array(
            'route' => 'page-inviter/contacts/*',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'index',
                'action' => 'pagecontacts',
//          'page_id' => 0
            )
        ),
        'page_inviter_send' => array(
            'route' => 'page-inviter/send/*',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'index',
                'action' => 'pageinvitationsend'
            )
        ),


        'inviter_members' => array(
            'route' => 'inviter/members/*',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'index',
                'action' => 'members'
            )
        ),
        'inviter_contacts' => array(
            'route' => 'inviter/contacts/*',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'index',
                'action' => 'contacts'
            )
        ),
        'inviter_invitations' => array(
            'route' => 'inviter/invitations/:page',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'invitations',
                'action' => 'index',
                'page' => 1
            )
        ),
        'inviter_invitations_delete' => array(
            'route' => 'inviter/invitations/delete/:id',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'invitations',
                'action' => 'delete',
                'id' => 0
            )
        ),
        'inviter_invitations_delete_selected' => array(
                    'route' => 'inviter/invitations/delete-selected/',
                    'defaults' => array(
                        'module' => 'inviter',
                        'controller' => 'invitations',
                        'action' => 'delete-selected'
                    )
                ),
        'inviter_invitations_sendnew' => array(
            'route' => 'inviter/invitations/sendnew/:id',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'invitations',
                'action' => 'sendnew',
                'id' => 0
            )
        ),
        'inviter_referrals' => array(
            'route' => 'inviter/referrals/:page',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'referrals',
                'action' => 'index',
                'page' => 1
            )
        ),
        'inviter_ru' => array(
            'route' => 'inviter/oauth/access/',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'oauth',
                'action' => 'access',
            )
        ),
        'inviter_facebook' => array(
            'route' => 'inviter/facebook',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'facebook',
                'action' => 'index'
            )
        ),
        'inviter_facebook_signup' => array(
            'route' => 'facebook/signup/*',
            'defaults' => array(
                'module' => 'inviter',
                'controller' => 'facebook',
                'action' => 'join'
            )
        ),
    ),
    // end routes
);