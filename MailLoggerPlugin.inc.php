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
use PKP\file\PrivateFileManager;

class MailLoggerPlugin extends GenericPlugin
{
    const LOG_PATH_RELATIVE = 'logs' . DIRECTORY_SEPARATOR . 'email.log';
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
        $mailLogConfig = [
            'driver' => 'single',
            'path' => (new PrivateFileManager())->getBasePath() . DIRECTORY_SEPARATOR . self::LOG_PATH_RELATIVE,
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
}
