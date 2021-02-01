@extends('layouts.admin')
@section('content')


    <div class="d-flex justify-content-between mb-5">
        <h1>Categories</h1>
        <div >
            <a class="btn btn-outline-dark" href="{{route('admin.categories.create')}}">create New</a>
        </div>
    </div>

    @include('_alert')


    <table class="table">
        <thead>
        <tr>
            <th>
              name
            </th>
            <th>
Parent
            </th>
            <th>
Product #
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
        @foreach($categories as $category)
        <tr>
            <td><a href="{{route('admin.categories.edit',[$category->id])}}">{{$category->name}}</a></td>
            <td>{{$category->parent->name}}</td>
            <td>{{$category->products_count}}</td>
            <td>{{$category->created_at}}</td>
            <td><form action="{{route('admin.categories.delete',[$category->id])}}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-outline-danger">delete</button><br>
                </form></td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{$categories->links()}}


@endsection
