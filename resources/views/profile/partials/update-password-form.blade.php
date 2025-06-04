<section>
    <header class="mb-4">
        <h2 class="h5 fw-bold text-primary mb-1">
            <i class="bi bi-shield-lock"></i> {{ __('Update Password') }}
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95em;">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form id="update-password-form" method="POST" action="{{ route('password.update') }}" class="needs-validation" novalidate autocomplete="off">
        @csrf

        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <label for="update_password_current_password" class="form-label">
                    <i class="bi bi-key"></i> {{ __('Current Password') }}
                </label>
                <div class="input-group">
                    <input id="update_password_current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" required minlength="8">
                    <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1"><i class="bi bi-eye"></i></button>
                </div>
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6 mb-3">
                <label for="update_password_password" class="form-label">
                    <i class="bi bi-lock"></i> {{ __('New Password') }}
                </label>
                <div class="input-group">
                    <input id="update_password_password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" required minlength="8" pattern=".{8,}">
                    <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1"><i class="bi bi-eye"></i></button>
                </div>
                <div class="form-text">
                    {{ __('Password must be at least 8 characters.') }}
                </div>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6 mb-3">
                <label for="update_password_password_confirmation" class="form-label">
                    <i class="bi bi-lock-fill"></i> {{ __('Confirm Password') }}
                </label>
                <div class="input-group">
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" required minlength="8">
                    <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1"><i class="bi bi-eye"></i></button>
                </div>
                <div class="invalid-feedback" id="password-match-error" style="display:none;">
                    {{ __('Password confirmation does not match.') }}
                </div>
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-arrow-repeat"></i> {{ __('Update Password') }}
            </button>
            @if (session('status') === 'password-updated')
                <span class="text-success ms-2">
                    <i class="bi bi-check-circle"></i> {{ __('Password updated!') }}
                </span>
            @endif
        </div>
    </form>

    <script>
        // Toggle show/hide password
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                if (input.type === 'password') {
                    input.type = 'text';
                    this.innerHTML = '<i class="bi bi-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    this.innerHTML = '<i class="bi bi-eye"></i>';
                }
            });
        });

        // Bootstrap validation + password match check
        document.getElementById('update-password-form').addEventListener('submit', function(event) {
            let form = this;
            let password = form.password.value;
            let confirm = form.password_confirmation.value;
            let matchError = document.getElementById('password-match-error');
            let valid = true;

            // HTML5 validation
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                valid = false;
            }

            // Password match validation
            if (password !== confirm) {
                event.preventDefault();
                event.stopPropagation();
                matchError.style.display = 'block';
                form.password_confirmation.classList.add('is-invalid');
                valid = false;
            } else {
                matchError.style.display = 'none';
                form.password_confirmation.classList.remove('is-invalid');
            }

            form.classList.add('was-validated');
            return valid;
        });
    </script>
</section>
