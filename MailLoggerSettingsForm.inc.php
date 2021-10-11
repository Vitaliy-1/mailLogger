<?php
/**
 * @file MailLoggerSettingsForm.inc.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class MailLoggerSettingsForm
 * @ingroup plugins_generic_mailLogger
 *
 * @brief Form for setting up the path to the email log file.
 */

use PKP\form\Form;
use PKP\plugins\Plugin;
use PKP\file\PrivateFileManager;
use APP\core\Application;

class MailLoggerSettingsForm extends Form
{
    public Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin->getTemplateResource('settings.tpl'));
        $this->plugin = $plugin;
        $this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));
    }

    /**
    * @copydoc Form::init
    */
    public function initData()
    {
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        $contextId = $context ? $context->getId() : Application::CONTEXT_SITE;
        $logFilePath = $this->plugin->getSetting($contextId, 'logFilePath');

          // If not set, suggest path to the files_directory/log/mail.log
        if (!$logFilePath) {
            $logFilePath = (new PrivateFileManager())->getBasePath() . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'mail.log';
        }

        $this->setData('logFilePath', $logFilePath);
    }

    /**
     * Assign form data to user-submitted data.
     */
    public function readInputData()
    {
        $this->readUserVars([
            'logFilePath',
        ]);
    }

    /**
     * @copydoc Form::fetch()
     */
    public function fetch($request, $template = null, $display = false)
    {
        $templateMgr = TemplateManager::getManager($request);
        $logFilePath = $this->getData('logFilePath');

        $templateMgr->assign([
            'logFilePath' => $logFilePath,
            'pluginName' => $this->plugin->getName(),
        ]);
        return parent::fetch($request, $template, $display);
    }

    /**
     * @copydoc Form::execute()
     */
    public function execute(...$functionArgs)
    {
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        $contextId = $context ? $context->getId() : 0;
        $this->plugin->updateSetting($contextId, 'logFilePath', $this->getData('logFilePath'));
        return parent::execute(...$functionArgs);
    }
}
