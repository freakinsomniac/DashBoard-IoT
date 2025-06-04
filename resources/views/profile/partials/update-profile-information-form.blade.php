<section>
    <header class="mb-4">
        <h2 class="h5 fw-bold text-primary mb-1">
            <i class="bi bi-person-circle"></i> {{ __('Profile Information') }}
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95em;">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form method="POST" action="{{ route('profile.update') }}" class="needs-validation" novalidate>
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="bi bi-person"></i> {{ __('Name') }}
            </label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope"></i> {{ __('Email') }}
            </label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2 py-2 px-3 small">
                    <i class="bi bi-exclamation-triangle"></i>
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="btn btn-link btn-sm p-0 ms-2 align-baseline">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <span class="text-success ms-2">
                            <i class="bi bi-check-circle"></i>
                            {{ __('A new verification link has been sent to your email address.') }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save"></i> {{ __('Save') }}
            </button>
            @if (session('status') === 'profile-updated')
                <span class="text-success ms-2">
                    <i class="bi bi-check-circle"></i> {{ __('Saved.') }}
                </span>
            @endif
        </div>
    </form>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-none">
        @csrf
    </form>
</section>
