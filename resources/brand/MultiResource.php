<?php namespace Shohabbos\Shopaholicapi\Resources\Brand;

use Illuminate\Http\Resources\Json\Resource;

class MultiResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'preview_text' => $this->preview_text,
            'description' => $this->description
        ];

        // image for preview
        if ($this->preview_image) {
            $data['preview_image'] = $this->preview_image->getThumb(250, 250, ['mode' => 'crop']);
            $data['original_preview_image'] = $this->preview_image->getPath();
        }

        return $data;
    }
    
}