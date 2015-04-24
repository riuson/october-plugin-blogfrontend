<?php
namespace Riuson\BlogFrontEnd\Components;

use Cms\Classes\ComponentBase;
use RainLab\Blog\Models\Category as BlogCategoryModel;

class CategoriesViewer extends \RainLab\Blog\Components\Categories
{

    public function componentDetails()
    {
        return [
            'name' => 'Categories Viewer',
            'description' => 'Lists categories with access rights'
        ];
    }

    protected function loadCategories()
    {
        $userGroups = \Riuson\ACL\Classes\Acl::userGroups();

        $blockedCategories = \DB::table('rainlab_blog_categories')->whereIn('rainlab_blog_categories.id', function ($query) {
            $query->select('riuson_eveapiuser_blog_category_groups.category_id')
                ->from('riuson_eveapiuser_blog_category_groups');
        })
            ->whereNotIn('rainlab_blog_categories.id', function ($query2) use($userGroups) {
            $query2->select('riuson_eveapiuser_blog_category_groups.category_id')
                ->from('riuson_eveapiuser_blog_category_groups')
                ->leftJoin('riuson_acl_groups', 'riuson_acl_groups.id', '=', 'riuson_eveapiuser_blog_category_groups.group_id')
                ->whereIn('riuson_acl_groups.name', $userGroups); // ['academy']
        })
            ->lists('rainlab_blog_categories.id');

        $categories = BlogCategoryModel::whereNotIn('id', $blockedCategories)->orderBy('name');

        if (! $this->property('displayEmpty')) {
            $categories->whereExists(function ($query) {
                $query->select(\Db::raw(1))
                    ->from('rainlab_blog_posts_categories')
                    ->join('rainlab_blog_posts', 'rainlab_blog_posts.id', '=', 'rainlab_blog_posts_categories.post_id')
                    ->whereNotNull('rainlab_blog_posts.published')
                    ->where('rainlab_blog_posts.published', '=', 1)
                    ->whereRaw('rainlab_blog_categories.id = rainlab_blog_posts_categories.category_id');
            });
        }

        $categories = $categories->get();
        /*
         * Add a "url" helper attribute for linking to each category
         */
        $categories->each(function ($category) {
            $category->setUrl($this->categoryPage, $this->controller);
        });
        return $categories;
    }
}