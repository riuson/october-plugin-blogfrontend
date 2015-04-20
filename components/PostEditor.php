<?php
namespace Riuson\BlogFrontEnd\Components;

use Cms\Classes\ComponentBase;

class PostEditor extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Post Editor',
            'description' => 'Front-End post editor.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }
}
