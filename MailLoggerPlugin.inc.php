<?php

/**
 * @file plugins/generic/mailLogger/MailLoggerPlugin.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class MailLoggerPlugin
 * @ingroup plugins_generic_mailLogger
 *
 * @brief Mail Logger plugin class.
 */

use PKP\plugins\GenericPlugin;
use PKP\linkAction\request\AjaxModal;
use PKP\linkAction\LinkAction;
use PKP\core\JSONMessage;
use APP\core\Application;

class MailLoggerPlugin extends GenericPlugin
{
    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName(): string
    {
        return __('plugins.generic.mailLogger.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription(): string
    {
        return __('plugins.generic.mailLogger.description');
    }

    /**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) {
            return $success;
        }

        if ($success && $this->getEnabled($mainContextId)) {
            $this->registerLogger();
        }

        return $success;
    }

    /**
     * Override mail log config
     */
    public function registerLogger()
    {
        $context = $this->getRequest()->getContext();
        $contextId = $context ? $context->getId() : Application::CONTEXT_SITE;

        $mailLogConfig = [
            'driver' => 'single',
            'path' => $this->getSetting($contextId, 'logFilePath'),
            'level' => 'debug',
        ];
        config([
            'logging.channels.maillog' => $mailLogConfig,
            'mail.mailers.log' => [
                'transport' => 'log',
                'channel' => 'maillog',
            ],
        ]);
    }

    /**
     * @see Plugin::getActions()
     */
    public function getActions($request, $actionArgs)
    {
        $actions = parent::getActions($request, $actionArgs);

        if (!$this->getEnabled()) {
            return $actions;
        }

        $router = $request->getRouter();
        $linkAction = new LinkAction(
            'settings',
            new AjaxModal(
                $router->url(
                    $request,
                    null,
                    null,
                    'manage',
                    null,
                    [
                        'verb' => 'settings',
                        'plugin' => $this->getName(),
                        'category' => 'generic'
                    ]
                ),
                $this->getDisplayName()
            ),
            __('manager.plugins.settings'),
            null
        );

        array_unshift($actions, $linkAction);

        return $actions;
    }

    /**
     * @see Plugin::manage()
     */
    public function manage($args, $request)
    {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $this->import('MailLoggerSettingsForm');
                $form = new MailLoggerSettingsForm($this);

                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new JSONMessage(true);
                    }
                }

                $form->initData();
                return new JSONMessage(true, $form->fetch($request));
        }
        return parent::manage($args, $request);
    }

}
