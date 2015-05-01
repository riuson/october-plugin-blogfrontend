<?php
namespace Riuson\BlogFrontEnd;

use System\Classes\PluginBase;

/**
 * BlogFrontEnd Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = [
        'RainLab.Blog',
        'Riuson.ACL',
        'Riuson.EveIGB'
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'riuson.blogfrontend::lang.plugin.name',
            'description' => 'riuson.blogfrontend::lang.plugin.description',
            'author' => 'Riuson',
            'icon' => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            'Riuson\BlogFrontEnd\Components\PostEditor' => 'postEditor',
            'Riuson\BlogFrontEnd\Components\PostViewer' => 'postViewer',
            'Riuson\BlogFrontEnd\Components\PostsViewer' => 'postsViewer',
            'Riuson\BlogFrontEnd\Components\CategoriesViewer' => 'categoriesViewer'
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

        \RainLab\Blog\Models\Category::extend(function ($model) {
            $model->belongsToMany['groups'] = [
                'Riuson\ACL\Models\Group',
                'table' => 'riuson_blogfrontend_blog_category_groups',
                'other_key' => 'group_id'
            ];
        });

        \Event::listen('backend.form.extendFields', function ($widget) {
            if (! $widget->getController() instanceof \RainLab\Blog\Controllers\Categories)
                return;
            if (! $widget->model instanceof \RainLab\Blog\Models\Category)
                return;

            $widget->addFields([
                'groups' => [
                    'label' => 'riuson.blogfrontend::lang.backend.field_groups_label',
                    'commentAbove' => 'riuson.blogfrontend::lang.backend.field_groups_comment_above',
                    'tab' => 'riuson.blogfrontend::lang.backend.field_groups_tab_name',
                    'type' => 'relation'
                ]
            ], 'primary');
        });
    }
}
