<?php
namespace Riuson\BlogFrontEnd\Components;

use Cms\Classes\ComponentBase;
use RainLab\Blog\Models\Post as PostModel;

class PostsViewer extends \RainLab\Blog\Components\Posts
{

    public function componentDetails()
    {
        return [
            'name' => 'Posts Viewer',
            'description' => 'Posts list viewer for frontend'
        ];
    }

    public function onRun()
    {
        parent::onRun();

        $this->authors = array();

        if ($this->posts != null && ! empty($this->posts)) {
            foreach ($this->posts as $post) {
                if ($post != null) {
                    $frontend_user = $post->frontend_user->first();

                    if ($frontend_user != null) {
                        $post_user_name = $frontend_user->name;

                        if ($frontend_user->key != null) {
                            if ($frontend_user->key->primaryCharacter() != null) {
                                $post_user_name = $frontend_user->key->primaryCharacter()->characterName;
                            }
                        }

                        $this->authors[$post->getKey()] = $post_user_name;
                    }
                }
            }
        }
    }

    protected function listPosts()
    {
        $categories = $this->category ? $this->category->id : null;
        /*
         * $posts = PostModel::with('categories.groups')->listFrontEnd([
         * 'page' => $this->property('pageNumber'),
         * 'sort' => $this->property('sortOrder'),
         * 'perPage' => $this->property('postsPerPage'),
         * 'categories' => $categories
         * ]);
         */

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

        /*
         * List all accessible posts, eager load their categories
         */
        $posts = PostModel::with('categories')->whereNotIn('id', function ($query) use($blockedCategories) {
            $query->select('post_id')
                ->from('rainlab_blog_posts_categories')
                ->whereIn('category_id', $blockedCategories);
        })
            ->listFrontEnd([
            'page' => $this->property('pageNumber'),
            'sort' => $this->property('sortOrder'),
            'perPage' => $this->property('postsPerPage'),
            'categories' => $categories
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function ($post) {
            $post->setUrl($this->postPage, $this->controller);
            $post->categories->each(function ($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });
        return $posts;
    }
}