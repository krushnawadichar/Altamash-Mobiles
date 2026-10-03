@extends('layouts.public')

@section('title', 'My Dashboard - ALTAMASH MOBILE')

@section('content')
<div class="bg-light py-4 border-bottom mb-5">
    <div class="container">
        <h1 class="fw-bold mb-0">My Dashboard</h1>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-user fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small mb-0">{{ $user->email }}</p>
                </div>
            </div>

            <div class="list-group shadow-sm border-0 rounded-4">
                <a href="#" class="list-group-item list-group-item-action active border-0 py-3"><i class="fa-solid fa-box me-2"></i> My Orders</a>
                <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action border-0 py-3"><i class="fa-solid fa-user-gear me-2"></i> Account Settings</a>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="list-group-item list-group-item-action border-0 py-3 text-danger"><i class="fa-solid fa-sign-out-alt me-2"></i> Logout</button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h4 class="fw-bold mb-4">Order History</h4>
                    
                    @if(count($orders) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $order->invoice_number }}</td>
                                        <td>{{ $order->sale_date->format('d M, Y') }}</td>
                                        <td class="fw-bold">₹{{ number_format($order->grand_total, 2) }}</td>
                                        <td>
                                            @if($order->payment_status == 'paid')
                                                <span class="badge bg-success">Processing</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $order->payment_method }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa-solid fa-box-open text-muted fs-1 mb-3"></i>
                            <h5 class="text-muted">No orders found.</h5>
                            <p class="mb-4">You haven't placed any orders yet.</p>
                            <a href="{{ route('shop') }}" class="btn btn-primary-custom px-4">Start Shopping</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


