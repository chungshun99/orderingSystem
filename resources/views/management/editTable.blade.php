@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        
    @include('management.inc.sidebar')

        <div class="col-md-8">
            <i class="fa-solid fa-chair"></i> Edit Table
            <hr>
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="/management/table/{{$table -> id}}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="tableName">Table Name</label>
                    <input type="text" name="tableName" value="{{$table -> tableName}}" class="form-control" placeholder="Table...">
                </div>

                <button type="submit" class="btn btn-warning">Edit</button>
            </form>
    
        </div>
    </div>
</div>

@endsection
