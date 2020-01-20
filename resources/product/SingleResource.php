<?php namespace Shohabbos\Shopaholicapi\Resources\Product;

use Illuminate\Http\Resources\Json\Resource;

use Shohabbos\Shopaholicapi\Resources\StoreResource;
use Shohabbos\Shopaholicapi\Resources\ImageResource;
use Shohabbos\Shopaholicapi\Resources\ReviewResource;
use Shohabbos\Shopaholicapi\Resources\Brand\SingleResource as BrandSingleResource;
use Shohabbos\Shopaholicapi\Resources\Category\SingleResource as CategorySingleResource;

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
            'active' => $this->active,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'store_id' => $this->store_id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'preview_text' => $this->preview_text,
            'description' => strip_tags($this->description),
            'rating' => $this->rating,
            'rating_data' => $this->rating_data,
            'offer_id_list' => $this->offer_id_list,
            'additional_category_id' => $this->additional_category_id,
            'trashed' => $this->trashed,
            'property_value_array' => $this->property_value_array,

            'offer' => OfferResource::collection($this->whenLoaded('offer')),
            'related' => MultiResource::collection($this->whenLoaded('related')),
            'accessory' => MultiResource::collection($this->whenLoaded('accessory')),
            'review' => ReviewResource::collection($this->whenLoaded('review')),
            'original_image' => new ImageResource($this->whenLoaded('preview_image')),
            'original_images' => ImageResource::collection($this->whenLoaded('images')),
        ];

        // image for preview
        if ($this->preview_image) {
            $data['preview_image'] = $this->preview_image->getThumb(250, 250, ['mode' => 'crop']);
            $data['original_preview_image'] = $this->preview_image->getPath();
        }

        // load offers
        if ($this->offer) {
            $data['offer'] = [];

            foreach ($this->offer as $key => $value) {
                $data['offer'][] = new OfferResource($value);                
            }
        }

        // load offers
        if ($this->offer) {
            $data['price'] = new OfferResource($this->offer()->first());
        }

        // load offers
        if ($this->brand) {
            $data['brand'] = new BrandSingleResource($this->brand);
        }

        // load brand
        if ($this->category) {
            $data['category'] = new CategorySingleResource($this->category);
        }

        if ($this->store) {
            $data['store'] = new StoreResource($this->store);
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