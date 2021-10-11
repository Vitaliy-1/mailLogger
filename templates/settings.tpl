{**
 * plugins/generic/mailLogger/templates/settings.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Form to set email log file
 *
 *}
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#MailLoggerSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="MailLoggerSettingsForm" method="post" action="{url router=\PKP\core\PKPApplication::ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	{fbvFormArea id="MailLoggerPluginSettings"}
		{fbvFormSection}
			<div id="description">{translate key="plugins.generic.mailLogger.settings.logFilePath.description"}</div>
			{fbvElement type="text" id="logFilePath" value=$logFilePath label="plugins.generic.mailLogger.settings.logFilePath"}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons}
</form>
