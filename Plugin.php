<?php
namespace Riuson\BlogFrontEnd;

use System\Classes\PluginBase;

/**
 * BlogFrontEnd Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = [
        'RainLab.Blog'
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Blog Front-End',
            'description' => 'Front-End interface for RainLab.Blog.',
            'author' => 'Riuson',
            'icon' => 'icon-leaf'
        ];
    }
}
