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

    public function registerComponents()
    {
        return [
            'Riuson\BlogFrontEnd\Components\PostEditor' => 'postEditor',
            'Riuson\BlogFrontEnd\Components\PostViewer' => 'postViewer',
            'Riuson\BlogFrontEnd\Components\PostsViewer' => 'postsViewer'
        ];
    }

    public function boot()
    {
        // extend Rainlab.User model
        \RainLab\User\Models\User::extend(function ($model) {
            $model->belongsToMany['posts'] = [
                'RainLab\Blog\Models\Post',
                'table' => 'riuson_blogfrontend_blog_post_users',
                'key' => 'user_id',
                'other_key' => 'post_id'
            ];
        });
        // extend Rainlab.Blog model
        \RainLab\Blog\Models\Post::extend(function ($model) {
            $model->belongsToMany['frontend_user'] = [
                'RainLab\User\Models\User',
                'table' => 'riuson_blogfrontend_blog_post_users',
                'key' => 'post_id',
                'other_key' => 'user_id'
            ];
        });
    }
}
