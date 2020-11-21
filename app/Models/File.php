<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';
    protected $fillable=['title', 'alt', 'path','type','filename','basename', 'extension'];

    public function getUrlAttribute()
    {
        return url($this->path . $this->filename);
    }


    public function getThumbUrlAttribute()
    {
        return url($this->path . 'thumb_' .  $this->filename);
    }
}
