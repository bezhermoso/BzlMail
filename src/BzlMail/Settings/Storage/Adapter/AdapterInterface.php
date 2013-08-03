<?php

/**
 *
 * @author Bezalel
 */
namespace BzlMail\Settings\Storage\Adapter;

use BzlMail\Settings\Settings;

interface AdapterInterface
{
    /**
     * @param string $transport
     * @return boolean
     */
    public function hasSettings($transport);
    /**
     * @return Settings
     */
    public function getSettings();
    public function saveSettings(Settings $settings);
}