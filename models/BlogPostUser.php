<?php
namespace Riuson\BlogFrontEnd\Models;

use Model;

/**
 * BlogPostUser Model
 */
class BlogPostUser extends Model
{

    /**
     *
     * @var string The database table used by the model.
     */
    public $table = 'riuson_blogfrontend_blog_post_users';

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
    public $hasOne = [];

    public $hasMany = [];

    public $belongsTo = [];

    public $belongsToMany = [];

    public $morphTo = [];

    public $morphOne = [];

    public $morphMany = [];

    public $attachOne = [];

    public $attachMany = [];
}