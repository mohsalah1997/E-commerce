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
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{old('name',$category->name)}}">
        @error('name')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="parent_id" class="col-sm-2 col-form-label">Parent</label>
    <div class="col-sm-10">
        <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
            <option value="">No parent</option>
            @foreach($parents as $parent)
                <option @if($parent->id ==old('parent_id',$category->parent_id)) selected @endif value="{{$parent->id}}">{{$parent->name}}</option>
            @endforeach

        </select>
        @error('parent_id')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="description" class="col-sm-2 col-form-label">Description</label>
    <div class="col-sm-10">
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{old('description',$category->description)}}</textarea>
        @error('description')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="image" class="col-sm-2 col-form-label">Image</label>
    <div class="col-sm-10">
        @if($category->image)
        <img class="mb-2 border border-bottom-success" height="200" src="{{asset('storage/'.$category->image)}}">
            <input type="checkbox"  value="1" name="delete_image" > Deleted Image
        @endif
        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
        @error('image')
        <p class="text-danger">{{$message}}</p>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <button class="btn btn-outline-primary" type="submit"> save</button>
</div>
