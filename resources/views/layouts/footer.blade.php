<footer class="py-3 bg-light border-top mt-auto">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-muted small">
                    &copy; {{ date('Y') }} <span class="fw-bold">{{ config('app.name', 'Altamash Mobiles') }}</span> 
                    - All Rights Reserved
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0 text-muted small">
                    Version 1.0 | 
                    <i class="fas fa-code"></i> Built with 
                    <span class="text-primary fw-bold">Laravel 12</span> & 
                    <span class="text-primary fw-bold">Bootstrap 5</span>
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
footer {
    margin-top: auto;
    position: relative;
    bottom: 0;
    width: 100%;
}
</style>