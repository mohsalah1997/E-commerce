@extends('layouts.admin')
@section('content')


    <div class="d-flex justify-content-between mb-5">
        <h1>products</h1>
        <div>
            <a class="btn btn-outline-dark" href="{{route('admin.products.create')}}">create New</a>
            <a class="btn btn-outline-danger" href="{{route('admin.products.trash')}}">Trash</a>

        </div>
    </div>

    @include('_alert')

    <form action="{{route('admin.products.index')}}" method="get" class="form-inline mb-2">
        <input type="text" name="name" id="name" placeholder="set the product name" class="form-control" value="{{$filters['name']??''}}">
        <select name="category_id" class="form-control ml-1" >
            <option value="">All Categories</option>
            @foreach(\App\Models\Category::all() as $category)
                <option value="{{$category->id}}" @if($category->id ==($filters['category_id']??0))  selected @endif>{{$category->name}}</option>

            @endforeach

        </select>

        <button type="submit" class="btn btn-outline-primary ml-1">filter</button>
    </form>


    <table class="table">
        <thead>
        <tr>
            <th>

            </th>
            <th>
                name
            </th>
            <th>
                category
            </th>
            <th>
                price
            </th>
            <th>
                quantity
            </th>
            <th>
                Created At
            </th>
            <th>
                Actions
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <td >@if($product->image)<img height="50" src="{{asset('storage/'.$product->image)}}">@endif</td>
                <td><a href="{{route('admin.products.edit',[$product->id])}}">{{$product->name}}</a></td>
                <td>{{$product->category->name}}</td>
                <td>{{$product->price}}</td>
                <td>{{$product->quantity}}</td>
                <td>{{$product->created_at}}</td>
                <td class="d-flex justify-content-between">
                    <form action="{{route('admin.products.destroy',[$product->id])}}" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-outline-danger">delete</button>
                        <br>
                    </form>
                    @if($product->trashed())
                    <form action="{{route('admin.products.restore',[$product->id])}}" method="post">
                        @csrf
                        @method('put')
                        <button type="submit" class="btn btn-outline-info">restore</button>
                        <br>
                    </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>
    {{$products->links()}}



@endsection
