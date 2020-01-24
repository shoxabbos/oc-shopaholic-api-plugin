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
        $data = parent::toArray($request);

        $data['logo'] = $this->logo;
        $data['pmall_phone'] = "+998954788020";

        return $data;
    }
    
}