<?php namespace Shohabbos\Shopaholicapi\Resources;

use Illuminate\Http\Resources\Json\Resource;

class StoreResource extends Resource
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
            'name' => $this->address,
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