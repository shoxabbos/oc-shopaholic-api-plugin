<?php namespace Shohabbos\Shopaholicapi\Resources\Product;

use Illuminate\Http\Resources\Json\Resource;

use Shohabbos\Shopaholicapi\Resources\OfferResource;

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
            'active' => $this->active,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'preview_text' => $this->preview_text,
            'description' => $this->description,
            'rating' => $this->rating,
            'rating_data' => $this->rating_data,
        ];

        // image for preview
        if ($this->preview_image) {
            $data['preview_image'] = $this->preview_image->getThumb(250, 250, ['mode' => 'crop']);
            $data['original_preview_image'] = $this->preview_image->getPath();
        }

        // load offers
        if ($this->offer) {
            $data['price'] = new OfferResource($this->offer()->first());
        }

        return $data;
    }
    
}