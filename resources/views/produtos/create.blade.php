@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2>Novo Produto</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data">
            @include('produtos._form')
        </form>
    </div>
</div>
@endsection
