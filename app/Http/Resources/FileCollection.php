<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $path = $this->path . $this->filename;
        $width = 0;
        $height = 0;

        if(file_exists($path)){
            $data = getimagesize($path);
            $width = $data[0];
            $height = $data[1];
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'alt' => $this->alt,
            'url' => $this->url,
            'path' => $this->path . $this->filename,
            'width'    => $width,
            'height'    => $height,
            'filename' => $this->filename,
            'basename' => $this->basename,
            'extension' => $this->extension,
            'thumb'     => $this->thumbUrl,
        ];
    }
}
