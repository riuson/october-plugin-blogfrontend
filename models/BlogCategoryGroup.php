<?php
namespace Riuson\BlogFrontEnd\Models;

use Model;

/**
 * BlogCategoryGroup Model
 */
class BlogCategoryGroup extends Model
{

    /**
     *
     * @var string The database table used by the model.
     */
    public $table = 'riuson_blogfrontend_blog_category_groups';

    public static function getTableName()
    {
        return 'riuson_blogfrontend_blog_category_groups';
    }

    /**
     *
     * @var array Guarded fields
     */
    protected $guarded = [
        '*'
    ];

    /**
     *
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     *
     * @var array Relations
     */
    public $hasOne = [
        'group' => [
            'Riuson\ACL\Models\Group'
        ]
    ];

    public $hasMany = [];

    public $belongsTo = [
        'category' => [
            'RainLab\Blog\Models\Category'
        ]
    ];

    public $belongsToMany = [];

    public $morphTo = [];

    public $morphOne = [];

    public $morphMany = [];

    public $attachOne = [];

    public $attachMany = [];
}