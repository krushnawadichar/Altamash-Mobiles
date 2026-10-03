@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Inventory History</h2>
        <p class="text-muted">Track all stock movements</p>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Previous Stock</th>
                        <th>New Stock</th>
                        <th>Reference</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr>
                        <td>{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                        <td class="fw-bold">{{ $tx->product->name }}</td>
                        <td>
                            @if($tx->transaction_type == 'purchase')
                                <span class="badge bg-success">Purchase (In)</span>
                            @elseif($tx->transaction_type == 'sale')
                                <span class="badge bg-primary">Sale (Out)</span>
                            @elseif($tx->transaction_type == 'adjustment')
                                <span class="badge bg-warning text-dark">Adjustment</span>
                            @elseif($tx->transaction_type == 'return')
                                <span class="badge bg-info text-dark">Return (In)</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($tx->transaction_type) }}</span>
                            @endif
                        </td>
                        <td>
                            @if(in_array($tx->transaction_type, ['purchase', 'return']))
                                <span class="text-success fw-bold">+{{ $tx->quantity }}</span>
                            @elseif(in_array($tx->transaction_type, ['sale', 'damage']))
                                <span class="text-danger fw-bold">-{{ $tx->quantity }}</span>
                            @else
                                {{ $tx->quantity }}
                            @endif
                        </td>
                        <td class="text-muted">{{ $tx->previous_stock }}</td>
                        <td class="fw-bold">{{ $tx->new_stock }}</td>
                        <td>{{ $tx->reference ?? 'N/A' }}</td>
                        <td>{{ $tx->user->name ?? 'System' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
</div>
@endsection


