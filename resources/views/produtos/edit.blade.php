@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2>Editar Produto</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('produtos.update', $produto->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('produtos._form')
        </form>
    </div>
</div>
@endsection
