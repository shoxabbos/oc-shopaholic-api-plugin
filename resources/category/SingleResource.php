<?php namespace Shohabbos\Shopaholicapi\Resources\Category;

use Illuminate\Http\Resources\Json\Resource;

use Shohabbos\Shopaholicapi\Resources\ImageResource;

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
            'description' => $this->description,
            'nest_depth' => $this->nest_depth,
            'children_id_list' => $this->children_id_list,
            'property_set_id' => $this->property_set_id,
            'parent_id' => $this->parent_id,
            'original_image' => new ImageResource($this->whenLoaded('preview_image')),
            'original_images' => ImageResource::collection($this->whenLoaded('images')),
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

        $data['product_filters'] = $this->loadProductFilters();

        return $data;
    }
    
    public function loadProductFilters() {
        $data = [];
        $properties = $this->product_filter_property;
 
        if (is_array($properties)) foreach ($properties as $key => $property) {
            $filter = [
                'id' => $property->id,
                'name' => $properties->getFilterName($property->id),
                'type' => $properties->getFilterType($property->id),
            ];
 
            $propertyValues = $property->property_value->sort();

            foreach ($propertyValues as $value) {
                $filter['values'][] = [
                    'slug' => $value->slug,
                    'value' => $value->value,
                ];
            }

            $data[] = $filter;            
        }

        return $data;
    }


}