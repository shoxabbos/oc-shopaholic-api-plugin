# oc-shopaholic-api-plugin
API plugin for Shopaholic by Itmaker. The plugin contains mere than 25 API methods. You can see it here [Postman Collection](https://raw.githubusercontent.com/shoxabbos/oc-shopaholic-api-plugin/master/Shopaholic%20API.postman_collection.json)


## List of products
* METHOD: `GET`
* URL:  `/api/products`
* PARAMS:
```
//sort:'no', 'price|asc', 'price|desc', 'new', 'popularity|desc', 'rating|desc', 
//category:1
//brand:1
//tag:1
//viewed:1 (boolean)
//label:1
//categoryies:[1, 2] (array)
//page:2
//perpage:20
//search:sneakers
```
* BODY:
```
{
    "data": [
        {
            "id": 632,
            "active": "1",
            "name": "Джинсы CARRERA зимние синие",
            "slug": "dzhinsy-carrera-zimnie-sinie",
            "code": "700",
            "category_id": "71",
            "brand_id": null,
            "preview_text": "",
            "rating": "0.00",
            "rating_data": {
                "1": 0,
                "2": 0,
                "3": 0,
                "4": 0,
                "5": 0
            },
            "preview_image": "https://domain/storage/app/uploads/public/5dd/7a0/687/thumb_1515_250_250_0_0_crop.jpg",
            "original_preview_image": "https://domain/storage/app/uploads/public/5dd/7a0/687/5dd7a0687a8d2932768901.jpg",
            "price": {
                "id": 1076,
                "code": null,
                "name": "54",
                "preview_text": null,
                "description": null,
                "quantity": "2",
                "price_value": 790000,
                "old_price_value": 0,
                "bch_price_value": 0
            },
            "images": [
                {
                    "image": "https://domain/storage/app/uploads/public/5dd/7a0/687/thumb_1516_500_500_0_0_crop.jpg",
                    "original_image": "https://domain/storage/app/uploads/public/5dd/7a0/687/5dd7a0687b87c907057695.jpg"
                }
            ]
        }
   ]
}
```
