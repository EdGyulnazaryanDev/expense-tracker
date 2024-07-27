@extends('layouts.app')
@section('content')
    <div class="container mt-3">
        <div id="restricted-content">
            <a href="{{ redirect()->back() }}"><i class="fa fa-arrow-left"></i></a>
            <h1>Welcome to the Members Only Area</h1>
            <p>This content is available exclusively to our members.</p>
        </div>
    </div>
<style>
    #restricted-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
</style>

@endsection
