<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{

    public function index()
    {

        $products = Product::where('is_active', 1)
            ->with('category')
            ->latest('id')
            ->select('id', 'category_id', 'name', 'slug', 'product_price', 'product_stock', 'alert_quantity', 'product_image', 'product_rating', 'updated_at')
            ->paginate(20);

        //return $products;

            return view('backend.pages.product.index', compact('products'));

    }


    public function create()
    {
        //return view('backend.pages.product.create');
        $categories = Category::select(['id','title'])->get();
        return view('backend.pages.product.create', compact('categories'));
    }


    public function store(ProductStoreRequest $request)
    {
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'product_code' => $request->product_code,
            'product_price' => $request->product_price,
            'product_stock' => $request->product_stock,
            'alert_quantity' => $request->alert_quantity,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'additional_info' => $request->additional_info,
        ]);

        $this->image_upload($request, $product->id);
        $this->multiple_image__upload($request, $product->id);

        Toastr::success('Data Stored Successfully!');
        return redirect()->route('product.index');
    }


    public function show(string $id)
    {

    }


    public function edit( $slug )
    {
        $product = Product::whereSlug($slug)->first();
        // return $product;
        $categories = Category::select(['id','title'])->get();
        return view('backend.pages.product.edit', compact('categories', 'product'));
    }


    public function update(ProductUpdateRequest $request, $slug)
    {
        $product = Product::whereSlug($slug)->first();
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'product_code' => $request->product_code,
            'product_price' => $request->product_price,
            'product_stock' => $request->product_stock,
            'alert_quantity' => $request->alert_quantity,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'additional_info' => $request->additional_info,
        ]);

        $this->image_upload($request, $product->id);
        $this->multiple_image__upload($request, $product->id);

        Toastr::success('Data Updated Successfully!');
        return redirect()->route('product.index');
    }


    public function destroy(string $id)
    {
        $product = Product::whereSlug($slug)->first();
        if($product->product_image){
            $photo_location = 'uploads/product_photos/'.$product->product_image;
            unlink($photo_location);
        }

        $product->delete();

        Toastr::success('Data Deleted Successfully!');
        return redirect()->route('product.index');
    }

    public function image_upload($request, $product_id)
    {
        $product = Product::findorFail($product_id);
        if ($request->hasFile('product_image')) {
            if ($product->product_image != 'default_product.jpg') {
                //delete old photo
                $photo_location = 'public/uploads/product_photos/';
                $old_photo_location = $photo_location . $product->product_image;
                unlink(base_path($old_photo_location));
            }
            $photo_location = 'public/uploads/product_photos/';
            $uploaded_photo = $request->file('product_image');
            $new_photo_name = $product->id . '.' . $uploaded_photo->getClientOriginalExtension();
            $new_photo_location = $photo_location . $new_photo_name;
            Image::make($uploaded_photo)->resize(600, 600)->save(base_path($new_photo_location), 40);
            $check = $product->update([
                'product_image' => $new_photo_name,
            ]);
        }
    }

    public function multiple_image__upload($request, $product_id)
    {
        if ($request->hasFile('product_multiple_image')) {

            // delete old photo first
            $multiple_images = ProductImage::where('product_id', $product_id)->get();
            foreach ($multiple_images as $multiple_image) {
                if ($multiple_image->product_multiple_photo_name != 'default_product.jpg') {
                    //delete old photo
                    $photo_location = 'public/uploads/product_photos/';
                    $old_photo_location = $photo_location . $multiple_image->product_multiple_photo_name;
                    unlink(base_path($old_photo_location));
                }
                // delete old value of db table
                $multiple_image->delete();
            }

            $flag = 1; // Assign a flag variable

            foreach ($request->file('product_multiple_image') as $single_photo) {
                $photo_location = 'public/uploads/product_photos/';
                $new_photo_name = $product_id.'-'.$flag.'.'. $single_photo->getClientOriginalExtension();
                $new_photo_location = $photo_location . $new_photo_name;
                Image::make($single_photo)->resize(600, 622)->save(base_path($new_photo_location), 40);
                ProductImage::create([
                    'product_id' => $product_id,
                    'product_multiple_image' => $new_photo_name,
                ]);
                $flag++;
            }
        }
    }
}
