<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Rules\checkParent;
use http\QueryString;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    public function index(){

//        $categories=Category::leftJoin('categories as parents','parents.id','=','categories.parent_id')
//            ->select(['categories.*','parents.name as parent_name'])->get();

        $categories=Category::query()->withCount('products')->paginate(5);
//        $categories=Category::query()->has('products')->withCount('products')->paginate(1);
//        $categories=Category::query()->has('products','>',1)->withCount('products')->paginate(1);
//        $categories=Category::query()->whereHas('products',function ($query){
//           $query->where('price','>','10');
//        })->withCount('products')->paginate(1);
//
//        $categories=Category::query()->doesntHave('products')->withCount('products')->paginate(1);


        return view('admin.categories.index',[
            'categories'=>$categories,
        ]);

    }
    public function update(Category $category,Request $request){
//        $request->validate([
//            'name'=>'required|max:255|min:4|alpha',
//            'description'=>'max:4000',
//            'parent_id'=>'nullable|exists:categories,id'
//        ]);
        $image=$this->storeImage($request);
        $this->validator($request,$category->id)->validate();
        //$category=Category::findOrFail($id);
        $category->name=$request->name;
        $category->parent_id=$request->parent_id;
        $category->description=$request->description;
        if($image){
            Storage::disk('public')->delete($category->image);
            $category->image=$image;
        }elseif ($request->delete_image==1){
            Storage::disk('public')->delete($category->image);
            $category->image=null;
        }


        $category->save();
        $message=sprintf('categories "%s" updated',$category->name);
        return redirect()->route('admin.categories.index')
            ->with('success',$message);

    }


    public  function  edit(Category $category){
        //$category=Category::findOrFail($id);

        $parents=Category::all()
        ;/*where('id','<>',$id)->where(function ($query) use ($id){
            $query->where('parent_id','<>',$id)->orWhereNull('parent_id');

        })->get();
*/
        return view('admin.categories.edit',[
            'category'=>$category,
            'parents'=>$parents,

        ]);
    }
    public function create(){
        return view('admin.categories.create',[
            'category'=>new Category(),
            'parents'=>Category::all(),
        ]);
    }

    public  function  store(Request $request){
//        $request->validate([
//           'name'=>'required|max:255|min:4|alpha',
//           'description'=>'max:4000',
//            'parent_id'=>'nullable|exists:categories,id'
//        ]);
        $this->validator($request)->validate();


        $category=new Category();
        $category->name=$request->name;
        $category->parent_id=$request->parent_id;
        $category->description=$request->description;
        $category->image=$this->storeImage($request);

        $category->save();
        $message=sprintf('categories "%s" created',$category->name);
        return redirect()->route('admin.categories.index')
            ->with('success',$message);



    }

    public function destroy(Category $category){
        //$category=Category::findOrFail($id);

        try{
            $category->delete();
            Storage::disk('public')->delete($category->image);
        }catch (QueryException $e){
            if(strpos($e->getMessage(),'1451')!==false){
                $message='cannot delete parent category';
            }else {
                $message=$e->getMessage();

            }
            return redirect()->route('admin.categories.index')
                ->with('error',$message);
        }

        $message=sprintf('categories "%s" deleted',$category->name);

        return redirect()->route('admin.categories.index')
            ->with('success',$message);
    }

    protected function validator(Request $request,$id=''){
        $validate=Validator::make($request->all(),[
            'name'=>'required|max:255|min:4|string',
//            'description'=>['size:9',
//                function($attribute, $value,$fail){
//                    $sum=0;
//                    $result=0;
//            for ($i=0;$i<8;$i++){
//                $factor=$i%2==0?1:2;
//                $result=(string)($value[$i]*$factor);
//                if($result>9){
//                    $sum+=  $result[0]+$result[1];
//                }else {
//                    $sum+=$result;
//                }
//
//
//
//
//            }
//            $last_digit=substr($sum,-1);
//            if(10-$last_digit !=$value[8]){
//              $fail('invalid ID Number');
//            }
//
//
//                }
//
//                ],
            'parent_id'=>['nullable',
                'exists:categories,id',
//                $id!==''?new checkParent($id):'',
                new checkParent($id)
                ],
            'image'=>'nullable|image|max:512'

        ]);
        return $validate;
    }

    protected function storeImage(Request $request){
        if($request->hasFile('image') && $request->file('image')->isValid()){
            $image=$request->file('image');
            $name=$image->getClientOriginalName();

            return $image->store('images','public');
        }
    }

    public function products(Category $category){
        return view('admin.categories.products',[
            'category'=>$category,
            ]

        );
    }
}
