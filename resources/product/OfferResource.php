<?php namespace Shohabbos\Shopaholicapi\Resources\Product;

use Illuminate\Http\Resources\Json\Resource;

class OfferResource extends Resource
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
            'code' => $this->code,
            'name' => $this->name,
            'preview_text' => $this->preview_text,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'price_value' => (float) $this->price_value,
            'old_price_value' => (float) $this->old_price_value,
            'bch_price_value' => ($this->old_price_value > 0) 
                                    ? round($this->old_price_value - $this->price_value, 2) 
                                    : 0
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
                    'image' => $image->getThumb(300, 300, ['mode' => 'crop']),
                    'original_image' => $image->getPath()
                ]; 
            }

            $data['images'] = $images;
        }

        return $data;
    }
    
}