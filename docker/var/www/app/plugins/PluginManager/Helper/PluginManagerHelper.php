<?php

namespace Kanboard\Plugin\PluginManager\Helper;

use Kanboard\Core\Base;
use Kanboard\Core\Http\Client;
use Kanboard\Core\Plugin\Directory;

/**
 * Plugin PluginManager
 * Class PluginManagerHelper
 *
 * @package  Plugin\PluginManager\Helper
 * @author   aljawaid
 * @author   creecros       Craig Crosby
 * @author   alfredbuehler  Alfred Bühler
 * @author   cptsanifair    Cpt. Sanifair
 */
class PluginManagerHelper extends Base
{
    /**
     * Get SVG Icons for Inline Markup and CSS Styling (Embed SVG Fly Method)
     *
     * Include filename as class inside SVG file
     * Set 'color' as 'currentColor' to use CSS or define a color
     * Style using normal CSS
     * @uses    $this->helper->myHelper->embedSVGIcon('icon-filename-without-extension')
     * @var     $svgFilename
     * @return  svg
     * @author  Erik Moberg https://www.erikmoberg.net/article/embed-svg-icons-in-html-with-php
     * @author  aljawaid
     */
    public function embedSVGIcon($icon_filename)
    {
        if ($this->router->getPlugin()) {
            // For plugin templates
            return file_get_contents('plugins/' . $this->helper->app->getPluginName() . '/Assets/icons/' . $icon_filename . '.svg');
        } else {
            // For core templates
            return file_get_contents('plugins/PluginManager/Assets/icons/' . $icon_filename . '.svg');
        }
    }

    /**
     * Get README File Based on Domain
     *
     * @access  public
     * @see     /plugin/show.php
     * @var     $url
     * @return  string
     * @author  aljawaid
     * @author  cptsanifair     Cpt. Sanifair
     */
    public function checkRootDomain($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        $domain = implode('.', array_slice(explode('.', parse_url($url, PHP_URL_HOST)), -2));

        switch ($domain) {
            case 'github.com':
                return '/blob/master/README.md';
            case 'gitlab.com':
                return '/-/blob/master/README.md';
            case 'gitea.com':
                return '/src/branch/main/README.md';
            case 'codeberg.org':
                return '/src/branch/master/README.md';
            default:
                return false;
        }
    }

    /**
     * Count Total Types
     *
     * @var     $available_plugins
     * @return  int
     * @author  creecros       Craig Crosby
     * @author  cptsanifair    Cpt. Sanifair
     */
    public function countTypes($available_plugins)
    {
        $types = array();

        foreach ($available_plugins as $plugin) {
            if (isset($plugin['is_type']) && in_array($plugin['is_type'], ['plugin', 'action', 'theme', 'multi', 'connector'], false)) {
                array_push($types, $plugin['is_type']);
            }
        }

        $count_types = array_count_values($types);

        return $count_types;
    }

    /**
     * Get All Plugins
     *
     * @var     $url            string
     * @return  int
     * @author  aljawaid
     * @author  cptsanifair     Cpt. Sanifair
     */
    public function getAllPlugins($url = PLUGIN_API_URL)
    {
        return array_filter($this->httpClient->getJson($url), array($this, 'isNotInstallable'));
    }

    /**
     * Get Not Installable Plugins
     *
     * @return  int
     * @author  aljawaid
     * @author  cptsanifair     Cpt. Sanifair
     */
    public function isNotInstallable(array $plugin)
    {
        return $plugin['remote_install'] === false;
    }

    /**
     * Get Timestamp of Remote File
     *
     * Snippet from: https://www.appsloveworld.com/php/12/get-the-last-modified-date-of-a-remote-file
     * @access  public
     * @return  string
     * @author  aljawaid
     */
    public function lastUpdatedDirectory()
    {
        $curlCheck = $this->httpClient->backend();

        if ($curlCheck == 'cURL') {
            $curl = curl_init(PLUGIN_API_URL);

            // Don't fetch the actual page, we only want the headers
            curl_setopt($curl, CURLOPT_NOBODY, true);

            // Stop it from outputting stuff to `stdout`
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            // Attempt to retrieve the modification date
            curl_setopt($curl, CURLOPT_FILETIME, true);

            // Set HTTP_VERIFY_SSL_CERTIFICATE from config.php
            if (HTTP_VERIFY_SSL_CERTIFICATE === false) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }

            $result = curl_exec($curl);

            if ($result === false) {
                die(curl_error($curl));
            }

            $timestamp = curl_getinfo($curl, CURLINFO_FILETIME);

            if ($timestamp != -1) { // otherwise unknown
                return date("d F Y H:i", $timestamp); // etc
            }
        } else {
            return t('Not Available');
        }
    }

    /**
     * Create List of Updatable Plugins
     *
     * @return  array with plugin titles
     * @author  creecros    Craig Crosby
     */
    public function getPluginUpdates(): array
    {
        $installed_plugins = array();
        $updatables = array();

        foreach ($this->pluginLoader->getPlugins() as $plugin) {
            $installed_plugins[$plugin->getPluginName()] = $plugin->getPluginVersion();
        }

        foreach (Directory::getInstance($this->container)->getAvailablePlugins() as $plugin) {
            $proband = &$installed_plugins[$plugin['title']];
            if (isset($proband) && $proband < $plugin['version']) {
                $updatables[$plugin['title']] = $plugin['download'];
            }
        }

        return $updatables;
    }

    /**
     * Get All Plugins Available
     *
     * Duplicate function of 'Kanboard\Core\Plugin\Directory\getAvailablePlugins()'
     * @see     getAvailablePlugins()
     * @access  public
     * @var     $url
     * @param   string
     * @return  array
     */
    public function getAllInstallablePlugins($url = PLUGIN_API_URL)
    {
        return Directory::getInstance($this->container)->getAvailablePlugins($url);
    }
}
