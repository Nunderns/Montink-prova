@if(isset($coupon) && $coupon->exists)
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @method('PUT')
@else
    <form action="{{ route('admin.coupons.store') }}" method="POST">
@endif
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="code" class="form-label">Código do Cupom</label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                       id="code" name="code" 
                       value="{{ old('code', $coupon->code ?? '') }}" required>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="type" class="form-label">Tipo de Desconto</label>
                <select class="form-select @error('type') is-invalid @enderror" 
                        id="type" name="type" required>
                    <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>
                        Valor Fixo (R$)
                    </option>
                    <option value="percent" {{ old('type', $coupon->type ?? '') === 'percent' ? 'selected' : '' }}>
                        Percentual (%)
                    </option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="value" class="form-label">
                    <span id="valueLabel">Valor do Desconto (R$)</span>
                </label>
                <input type="number" step="0.01" min="0" 
                       class="form-control @error('value') is-invalid @enderror" 
                       id="value" name="value" 
                       value="{{ old('value', $coupon->value ?? '') }}" required>
                @error('value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="min_order_value" class="form-label">
                    Valor Mínimo do Pedido (R$) <span class="text-muted">(opcional)</span>
                </label>
                <input type="number" step="0.01" min="0" 
                       class="form-control @error('min_order_value') is-invalid @enderror" 
                       id="min_order_value" name="min_order_value" 
                       value="{{ old('min_order_value', $coupon->min_order_value ?? '') }}">
                @error('min_order_value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Deixe em branco para não exigir valor mínimo.</small>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="valid_until" class="form-label">Válido Até</label>
                <input type="datetime-local" 
                       class="form-control @error('valid_until') is-invalid @enderror" 
                       id="valid_until" name="valid_until" 
                       value="{{ old('valid_until', isset($coupon->valid_until) ? $coupon->valid_until->format('Y-m-d\TH:i') : '') }}" 
                       required>
                @error('valid_until')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="usage_limit" class="form-label">
                    Limite de Usos <span class="text-muted">(opcional)</span>
                </label>
                <input type="number" min="1" 
                       class="form-control @error('usage_limit') is-invalid @enderror" 
                       id="usage_limit" name="usage_limit" 
                       value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}">
                @error('usage_limit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Deixe em branco para uso ilimitado.</small>
            </div>
        </div>
    </div>
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
               value="1" {{ old('is_active', isset($coupon) ? $coupon->is_active : true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Ativo</label>
    </div>
    
    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Salvar
        </button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const valueLabel = document.getElementById('valueLabel');
        const valueInput = document.getElementById('value');
        
        function updateValueLabel() {
            if (typeSelect.value === 'percent') {
                valueLabel.textContent = 'Percentual de Desconto (%)';
                valueInput.step = '1';
                valueInput.max = '100';
            } else {
                valueLabel.textContent = 'Valor do Desconto (R$)';
                valueInput.step = '0.01';
                valueInput.removeAttribute('max');
            }
        }
        
        typeSelect.addEventListener('change', updateValueLabel);
        
        // Inicializa o label corretamente
        updateValueLabel();
    });
</script>
@endpush
