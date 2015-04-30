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
            $query->select('riuson_blogfrontend_blog_category_groups.category_id')
                ->from('riuson_blogfrontend_blog_category_groups');
        })
            ->whereNotIn('rainlab_blog_categories.id', function ($query2) use($userGroups) {
            $query2->select('riuson_blogfrontend_blog_category_groups.category_id')
                ->from('riuson_blogfrontend_blog_category_groups')
                ->leftJoin('riuson_acl_groups', 'riuson_acl_groups.id', '=', 'riuson_blogfrontend_blog_category_groups.group_id')
                ->whereIn('riuson_acl_groups.name', $userGroups); // ['academy']
        })
            ->lists('rainlab_blog_categories.id');

        $categories = BlogCategoryModel::whereNotIn('id', $blockedCategories)->orderBy('name');

        /*
         * Possible query to get categories and available posts count
         *
         * select `rainlab_blog_categories`.*,
         * (
         * select count(*) from `rainlab_blog_posts`
         * left join `rainlab_blog_posts_categories` on `rainlab_blog_posts_categories`.`post_id` = `rainlab_blog_posts`.`id`
         * where `rainlab_blog_posts`.`id` not in (
         * select `post_id` from `rainlab_blog_posts_categories`
         * where `category_id` in (4, 5)
         * )
         * and
         * `rainlab_blog_posts_categories`.`category_id` = `rainlab_blog_categories`.`id`
         *
         * ) as `related_count`
         * from `rainlab_blog_categories`
         * where `id` not in (5, 4) and exists (
         * select 1
         * from `rainlab_blog_posts_categories`
         * inner join `rainlab_blog_posts` on `rainlab_blog_posts`.`id` = `rainlab_blog_posts_categories`.`post_id`
         * where `rainlab_blog_posts`.`published` is not null
         * and `rainlab_blog_posts`.`published` = 1
         * and rainlab_blog_categories.id = rainlab_blog_posts_categories.category_id
         * ) order by `name` asc
         */

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

        // get posts count
        $counts = \DB::table('rainlab_blog_posts')->select('rainlab_blog_posts_categories.category_id', \DB::raw('count(*) as posts_count'))
            ->from('rainlab_blog_posts')
            ->leftJoin('rainlab_blog_posts_categories', 'rainlab_blog_posts_categories.post_id', '=', 'rainlab_blog_posts.id')
            ->whereNotIn('rainlab_blog_posts.id', function ($query) use($blockedCategories) {
            $query->select('post_id')
                ->from('rainlab_blog_posts_categories')
                ->whereIn('category_id', $blockedCategories);
        })
            ->groupBy('rainlab_blog_posts_categories.category_id')
            ->lists('posts_count', 'category_id');

        $this->posts_count = $counts;

        return $categories;
    }
}