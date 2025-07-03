@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Cupons</h1>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Cupom
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Mínimo</th>
                            <th>Validade</th>
                            <th>Usos</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td><code>{{ $coupon->code }}</code></td>
                                <td>{{ $coupon->type === 'percent' ? 'Percentual' : 'Valor Fixo' }}</td>
                                <td>
                                    @if($coupon->type === 'percent')
                                        {{ $coupon->value }}%
                                    @else
                                        R$ {{ number_format($coupon->value, 2, ',', '.') }}
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->min_order_value)
                                        R$ {{ number_format($coupon->min_order_value, 2, ',', '.') }}
                                    @else
                                        <span class="text-muted">Nenhum</span>
                                    @endif
                                </td>
                                <td>{{ $coupon->valid_until->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($coupon->usage_limit)
                                        {{ $coupon->usage_count }} / {{ $coupon->usage_limit }}
                                    @else
                                        {{ $coupon->usage_count }} (sem limite)
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $coupon->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este cupom?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Nenhum cupom cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $coupons->links() }}
        </div>
    </div>
</div>
@endsection
