<?php namespace Shohabbos\Shopaholicapi\Resources\Store;

use Illuminate\Http\Resources\Json\Resource;

use Shohabbos\Shopaholicapi\Resources\ImageResource;
use Shohabbos\Shopaholicapi\Resources\UserResource;
use Shohabbos\Shopaholicapi\Resources\BannerResource;
use Shohabbos\Shopaholicapi\Resources\Product\SingleResource as ProductResource;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contacts' => $this->contacts,
            'legal_name' => $this->legal_name,
            'email' => $this->email,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user' => new UserResource($this->whenLoaded('user')),
            'logo' => new ImageResource($this->whenLoaded('logo')),
            'header_image' => new ImageResource($this->whenLoaded('header_image')),

            'products' => ProductResource::collection($this->whenLoaded('products')),
            'banners' => BannerResource::collection($this->whenLoaded('banners')),
        ];
    }
    
}