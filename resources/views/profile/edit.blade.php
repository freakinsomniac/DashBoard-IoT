@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4" style="color:#2563eb; letter-spacing:1px; font-weight:700;">
        <i class="bi bi-person-circle"></i> Profile
    </h2>
    <div class="row">
        <div class="col-md-8">
            {{-- Shortcut Section --}}
            <div class="mb-4 p-4 bg-light border rounded-4 shadow-sm">
                <h5 class="mb-3" style="color:#2563eb; font-weight:600;">Menu Akun</h5>
                <div class="d-grid gap-2 gap-md-3 d-md-flex">
                    <a href="#profile-info" class="btn btn-outline-primary flex-fill mb-2 mb-md-0">
                        <i class="bi bi-person-lines-fill"></i> Profile Information
                    </a>
                    <a href="#update-password" class="btn btn-outline-secondary flex-fill mb-2 mb-md-0">
                        <i class="bi bi-key"></i> Update Password
                    </a>
                    <a href="#delete-account" class="btn btn-outline-danger flex-fill mb-2 mb-md-0">
                        <i class="bi bi-trash"></i> Delete Account
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline w-100 w-md-auto">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark flex-fill">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            {{-- Hidden Sections --}}
            <div id="profile-info" class="mb-4 p-4 bg-white shadow rounded-4 d-none">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div id="update-password" class="mb-4 p-4 bg-white shadow rounded-4 d-none">
                @include('profile.partials.update-password-form')
            </div>
            <div id="delete-account" class="mb-4 p-4 bg-white shadow rounded-4 d-none">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Responsive shortcut: show section, hide others
    document.querySelectorAll('.btn[href^="#"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Hide all sections
            document.querySelectorAll('#profile-info, #update-password, #delete-account').forEach(el => el.classList.add('d-none'));
            // Show selected section
            const target = document.querySelector(this.getAttribute('href'));
            if(target) target.classList.remove('d-none');
            // Scroll to section for mobile
            if(target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
</script>
@endpush
@endsection
