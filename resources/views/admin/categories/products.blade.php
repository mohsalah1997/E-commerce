@extends('layouts.admin')
@section('content')


    <div class="d-flex justify-content-between mb-5">
        <h1>{{$category->name}}</h1>
        <div>
            <a class="btn btn-outline-dark" href="{{route('admin.categories.index')}}">back</a>
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
                Product #
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

        </tr>
        </thead>
        <tbody>
        @foreach($category->products as $category)
            <tr>
                <td>{{$category->name}}</td>
                <td>{{$category->products_count}}</td>
                <td>{{$category->price}}</td>
                <td>{{$category->quantity}}</td>
                <td>{{$category->created_at}}</td>

            </tr>
        @endforeach
        </tbody>
    </table>




@endsection
