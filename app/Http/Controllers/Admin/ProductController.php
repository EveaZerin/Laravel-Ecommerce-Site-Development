<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Image;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $product=DB::table('products')
        ->join('categories','products.category_id','categories.id')
        ->join('brands','products.brand_id','brands.id')
        ->select('products.*','categories.category_name','brands.brand_name')
        ->get();
        //return response()->json($product);
        return view('admin.product.index',compact('product'));
    }

    public function create(){
        $category =DB::table('categories')->get();
        $brand = DB::table('brands')->get();

        return view('admin.product.create', compact('category','brand'));
    }

    public function store(Request $request){


        $data['product_name'] = $request->product_name;
        $data['product_code'] = $request->product_code;
        $data['product_quantity'] = $request->product_quantity;
        $data['product_size'] = $request->product_size;
        $data['category_id'] = $request->category_id;
        $data['brand_id'] = $request->brand_id;
        $data['product_color'] = $request->product_color;
        $data['selling_price'] = $request->selling_price;
        $data['best_rated'] = $request->best_rated;
        $image_one = $request->image_one;
        $data['new_arrivals'] = $request->new_arrivals;
        $data['status'] = 1;
        //not save on database due to composer global/local error
        //will resolve later
        //return response()->json($data);

        if($image_one){
            $image_one_name=date('dmy_H_s_i').'.'.strtolower($image_one->getClientOriginalExtension());

            $upload_path='public/media/products/';

            $image_url=$upload_path.$image_one_name;


            $success = $image_one->move($upload_path,$image_one_name);
        

         
            $data['image_one'] = $image_url;

            $product = DB::table('products')->insert($data);
            $notification=array(
                'messege'=>'Product inserted Successfully...',
                'alert-type'=>'success'
                 );
               return Redirect()->back()->with($notification);
        }
        

    }

    public function DeleteProduct($id){
        
        $product=DB::table('products')->where('id',$id)->first();
        $image=$product->image_one;
        unlink($image);
        DB::table('products')->where('id',$id)->delete();
        $notification=array(
            'messege'=>'product Deleted Successfully...',
            'alert-type'=>'success'
            );
            return Redirect()->back()->with($notification);
    }

    public function ViewProduct($id){
        $product=DB::table('products')
        ->join('categories','products.category_id','categories.id')
        ->join('brands','products.brand_id','brands.id')
        ->select('products.*','categories.category_name','brands.brand_name')
        ->where('products.id',$id)
        ->first();
        //return response()->json($product);
        return view('admin.product.show',compact('product'));
    }

}
