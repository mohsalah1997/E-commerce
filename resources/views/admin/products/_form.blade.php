{{--@if($errors->any())--}}
{{--    <div class="alert alert-danger">--}}

{{--        <ul>--}}
{{--            @foreach($errors->all() as $message)--}}
{{--                <li>{{$message}}</li>--}}
{{--            @endforeach--}}
{{--        </ul>--}}
{{--    </div>--}}

{{--    @endif--}}



@csrf
<div class="row mb-3">
    <label for="name" class="col-sm-2 col-form-label">Name</label>
    <div class="col-sm-10">
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{old('name',$product->name)}}">
        @error('name')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="category_id" class="col-sm-2 col-form-label">Category</label>
    <div class="col-sm-10">
        <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
            <option value="">No category</option>
            @foreach(\App\Models\Category::all() as $category)
                <option @if($category->id ==old('category_id',$product->category_id)) selected @endif value="{{$category->id}}">{{$category->name}}</option>
            @endforeach

        </select>
        @error('category_id')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="description" class="col-sm-2 col-form-label">Description</label>
    <div class="col-sm-10">
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{old('description',$product->desc->description)}}</textarea>
        @error('description')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="image" class="col-sm-2 col-form-label">Image</label>
    <div class="col-sm-10">
        @if($product->image)
        <img class="mb-2 border border-bottom-success" height="200" src="{{asset('storage/'.$product->image)}}">
            <input type="checkbox"  value="1" name="delete_image" > Deleted Image
        @endif
        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
        @error('image')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="price" class="col-sm-2 col-form-label">Price</label>
    <div class="col-sm-10">
        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{old('price',$product->price)}}">
        @error('price')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="quantity" class="col-sm-2 col-form-label">Quantity</label>
    <div class="col-sm-10">
        <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{old('quantity',$product->quantity)}}">
        @error('quantity')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="tags" class="col-sm-2 col-form-label">Tags</label>
    <div class="col-sm-10">
        <input type="text" class="form-control @error('tags') is-invalid @enderror" id="tags" name="tags" value="{{old('tags',$tags??'')}}">
        @error('tags')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <button class="btn btn-outline-primary" type="submit"> save</button>
</div>
