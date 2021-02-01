<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\Tag;
use http\QueryString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ProductsController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

//        $products = Product::query()->join('categories', 'products.category_id', '=', 'categories.id')->
//        select(['products.*', 'categories.name as categories_name'])->paginate(2);

        $request=\request();
        $products = Product::query()->with('category')->filter($request->query())->paginate();


        //$products = Product::query()->with('category')->withoutGlobalScope('quantity')->price(10,50)->paginate(5);
        return view('admin.products.index', [
            'products' => $products,
            'filters'=>$request->query(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.products.create', [
            'product' => new Product(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request);
        $data = $request->except('image', '_token', 'tags');
        $image = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image')->store('images', 'public');
        }
        $data['image'] = $image;

        DB::beginTransaction();
        try {

            $product = Product::query()->create($data);
            $desc=ProductDescription::query()->firstOrCreate(['product_id'=>$product->id],['description'=>$request->description]);
            $desc->product()->associate($product);
            $this->create_tags($request, $product);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.products.index')->with('error', 'operation failed');

        }
        $message = sprintf('product %s created', $product->name);
        return redirect()->route('admin.products.index')->with('success', $message);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //$product = Product::query()->findOrFail($id);
        $tags = $product->tags->pluck('name')->toArray();
        $tags = implode(',', $tags);
        //  return $tags;

        return view('admin.products.edit', [
            'product' => $product,
            'tags' => $tags,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // $product=Product::query()->findOrFail($id);
        $this->validator($request);
        $data = $request->except('image', '_token', 'tags');

        $image = $product->image;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            Storage::disk('public')->delete($product->image);
            $image = $request->file('image')->store('images', 'public');
        }
        $data['image'] = $image;

        DB::beginTransaction();
        try {
            $product->update($data);
            $desc=ProductDescription::query()->firstOrCreate(['product_id'=>$product->id],['description'=>$request->description]);
            $desc->product()->associate($product);
            $this->create_tags($request, $product);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.products.index')->with('error', 'operation failed' . $e->getMessage());

        }
        $message = sprintf('product %s updated', $product->name);
        return redirect()->route('admin.products.index')->with('success', $message);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::withTrashed()->findOrFail($id);

        if($product->trashed()){
            $product->forceDelete();
            $message = sprintf('product %s deleted Permanently', $product->name);

        }else {
            $product->delete();
            $message = sprintf('product %s deleted', $product->name);

        }

        return redirect()->route('admin.products.index')->with('success', $message);

    }

    protected function validator(Request $request)
    {

        $request->validate([
            'name' => 'required|max:255',
            'price' => 'numeric',
            'quantity' => 'numeric',
            'category_id' => 'required|exists:categories,id',
            'image ' => 'nullable,image',
            'tags' => 'nullable|string'

        ]);


    }

    public function create_tags($request, $product)
    {
        $tags = $request->tags;
        $tags_ids = [];
        if ($tags) {
            $tags_array = explode(',', $tags);
            // Tag::query()->firstOrCreate();
            foreach ($tags_array as $tags_name) {
                $tags_name = trim($tags_name);
                $tag = Tag::query()->where('name', $tags_name)->first();
                if (!$tag) {
                    $tag = Tag::query()->create([
                        'name' => $tags_name
                    ]);
                }
                $tags_ids[] = $tag->id;
            }
        }
        $product->tags()->sync($tags_ids);

        // DB::table('products_tags')->where('product_id',$product->id)->delete();
//        if ($tags) {
//            $tags_array = explode(',', $tags);
//            // Tag::query()->firstOrCreate();
//            foreach ($tags_array as $tags_name) {
//                $tags_name = trim($tags_name);
//                $tag = Tag::query()->where('name', $tags_name)->first();
//                if (!$tag) {
//                    $tag = Tag::query()->create([
//                        'name' => $tags_name
//                    ]);
//                }
//
//                DB::table('products_tags')->insert([
//                    'product_id' => $product->id,
//                    'tag_id' => $tag->id,
//                ]);
//            }
//        }
    }


    public function trash(){
       return view('admin.products.index',[
           'products'=>Product::onlyTrashed()->paginate(),
       ]);

    }

    public function restore(Request $request,$id){
        $product=Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        $message = sprintf('product %s restored', $product->name);

        return redirect()->route('admin.products.index')->with('success', $message);

    }


}
