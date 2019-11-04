<?php namespace Shohabbos\Shopaholicapi\Resources\Brand;

use Illuminate\Http\Resources\Json\Resource;

class SingleResource extends Resource
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

        // images for gallery
        if ($this->images) {
            $images = [];

            foreach ($this->images as $key => $image) {
                $images[] = [
                    'image' => $image->getThumb(500, 500, ['mode' => 'crop']),
                    'original_image' => $image->getPath()
                ]; 
            }

            $data['images'] = $images;
        }

        return $data;
    }
    
}