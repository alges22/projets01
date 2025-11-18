<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\RequiredDocumentPackage\Models\DocumentType;

class SimvebFile extends Model
{
    use HasFactory, HasUuids;
    // TODO: soft deletes files

    protected $casts = ["path" => "array"];

    protected $appends = ['url'];
    protected $guarded=[];

    const IMAGE = 'image';
    const FILE = 'file';

    public function getUrlAttribute()
    {
        return asset(json_decode($this->attributes['path'])->path);
    }

    public function fileType()
    {
        return $this->belongsTo(DocumentType::class, 'file_type_id');
    }
}
