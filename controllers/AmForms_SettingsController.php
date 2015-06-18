<?php
namespace Craft;

/**
 * AmForms - Settings controller
 */
class AmForms_SettingsController extends BaseController
{
    /**
     * Show General settings.
     */
    public function actionIndex()
    {
        $variables = array(
            'type' => AmFormsModel::SettingGeneral,
            'general' => craft()->amForms_settings->getAllSettingsByType(AmFormsModel::SettingGeneral)
        );
        $this->renderTemplate('amForms/settings/index', $variables);
    }

    /**
     * Saves settings.
     */
    public function actionSaveSettings()
    {
        $this->requirePostRequest();

        // Settings type
        $settingsType = craft()->request->getPost('settingsType', false);

        // Save settings!
        if ($settingsType) {
            $this->_saveSettings($settingsType);
        }
        else {
            craft()->userSession->setError(Craft::t('Couldn’t find settings type.'));
        }

        $this->redirectToPostedUrl();
    }

    /**
     * Save the settings for a specific type.
     *
     * @param string $type
     */
    private function _saveSettings($type)
    {
        $success = true;

        // Get all available settings for this type
        $availableSettings = craft()->amForms_settings->getAllSettingsByType($type);

        // Save each available setting
        foreach ($availableSettings as $setting) {
            // Find new settings
            $newSettings = craft()->request->getPost($setting->handle, false);

            if ($newSettings !== false) {
                $setting->value = $newSettings;
                if(! craft()->amForms_settings->saveSettings($setting)) {
                    $success = false;
                }
            }
        }

        if ($success) {
            craft()->userSession->setNotice(Craft::t('Settings saved.'));
        }
        else {
            craft()->userSession->setError(Craft::t('Couldn’t save settings.'));
        }
    }
}