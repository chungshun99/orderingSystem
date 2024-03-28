@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        
        @include('management.inc.sidebar')

        <div class="col-md-8">
            <i class="fa-solid fa-utensils"></i> Menu
            <a href="/management/menu/create" class="btn btn-success btn-small float-right"><i class="fas fa-plus"></i> Create Menu</a>
            <hr>

            @if(Session()->has('status'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">X</button>
                    {{Session()->get('status')}}
                </div>

            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Menu</th>
                        <th scope="col">Price</th>
                        <th scope="col">Picture</th>
                        <th scope="col">Description</th>
                        <th scope="col">Category</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($menus as $menu)
                    <tr>
                        <td>{{$menu -> id}}</td>
                        <td>{{$menu -> menuName}}</td>
                        <td>{{$menu -> price}}</td>
                        <td>
                            <img src="{{asset('menu_images')}}/{{$menu -> menuImage}}" alt="{{$menu -> menuName}}" 
                            width="120px" height="120px" class="img-thumbnail">
                        </td>
                        <td>{{$menu -> menuDescription}}</td>
                        <td>{{$menu -> category -> categoryName}}</td>
                        <td><a href="/management/menu/{{$menu -> id}}/edit" class="btn btn-warning">Edit</a></td>
                        <td>
                            <form action="/management/menu/{{$menu -> id}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="Delete" class="btn btn-danger">
                            </form>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>
</div>

@endsection
