<?php

namespace Riuson\BlogFrontEnd\Components;

use Cms\Classes\ComponentBase;

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
}