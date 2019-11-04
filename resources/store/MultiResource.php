<?php namespace Shohabbos\Shopaholicapi\Resources\Store;

use Illuminate\Http\Resources\Json\Resource;

use Shohabbos\Shopaholicapi\Resources\ImageResource;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contacts' => $this->contacts,
            'legal_name' => $this->legal_name,
            'email' => $this->email,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'logo' => new ImageResource($this->whenLoaded('logo')),
        ];
    }
    
}