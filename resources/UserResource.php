<?php namespace Shohabbos\Shopaholicapi\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
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
        	'email' => $this->email,
        	'phone' => $this->phone,
        ];
    }
    
}