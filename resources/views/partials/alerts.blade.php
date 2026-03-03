{{-- SweetAlert2 Authentication Alerts Partial --}}
{{-- This file is included in both guest.blade.php and app.blade.php layouts --}}
{{-- It handles all authentication-related flash messages with modal popups --}}

<script>
    // Centralized SweetAlert2 configuration for authentication modals
    // Use var to allow re-declaration during Turbo Drive navigations
    var authSwalConfig = {
        glassCustomClass: {
            popup: 'rounded-[2rem] bg-slate-900/95 backdrop-blur-xl text-white border border-white/10 shadow-3xl',
            title: 'text-2xl font-bold text-white',
            htmlContainer: 'text-white/80 text-sm',
            confirmButton: 'btn btn-primary px-12 rounded-xl h-12 font-bold uppercase tracking-widest text-xs shadow-lg shadow-sky-500/20 hover:shadow-sky-500/40 transition-all',
            cancelButton: 'btn btn-ghost px-8 rounded-xl h-12 font-bold text-white/60 hover:bg-white/5'
        },
        toastConfig: {
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: 'rgba(15, 23, 42, 0.95)',
            color: '#fff',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        }
    };

    // Function to escape HTML in error messages
    function escapeHtml(text) {
        if (!text) return '';
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Flag to track if alerts have been shown
    // We attach these to window to make them truly global and persistent/reset-safe
    window.authAlertsShown = false;
    window.swalRetryCount = 0;
    var MAX_SWAL_RETRIES = 10;

    // Function to display authentication alerts
    function showAuthAlerts() {
        // Prevent showing alerts multiple times per page load
        if (window.authAlertsShown) return;
        
        // Check if SweetAlert2 is available - if not, retry
        if (typeof Swal === 'undefined') {
            if (window.swalRetryCount < MAX_SWAL_RETRIES) {
                window.swalRetryCount++;
                console.warn(`SweetAlert2 not loaded, retrying (${window.swalRetryCount}/${MAX_SWAL_RETRIES})...`);
                setTimeout(showAuthAlerts, 500);
            } else {
                console.error('SweetAlert2 failed to load after multiple attempts. Alerts will not be displayed.');
            }
            return;
        }

        var Toast = Swal.mixin(authSwalConfig.toastConfig);
        var alertDisplayed = false;

        // ============================================
        // VALIDATION ERRORS - Check $errors variable from Laravel
        // ============================================
        @if ($errors->any())
            console.log('Validation errors detected:', @json($errors->all()));
            
            var validationErrors = @json($errors->all());
            var errorTitle = 'Validation Failed';
            var errorIcon = 'error';

            // Check for invalid credentials (login)
            var hasInvalidCredentials = validationErrors.some(e => 
                e.toLowerCase().includes('invalid') || 
                e.toLowerCase().includes('credentials') ||
                e.toLowerCase().includes('email or password') ||
                e.toLowerCase().includes('incorrect') ||
                e.toLowerCase().includes('do not match') ||
                e.toLowerCase().includes('failed')
            );

            // Check for password confirmation mismatch
            var hasPasswordMismatch = validationErrors.some(e => 
                e.toLowerCase().includes('confirm') || 
                e.toLowerCase().includes('match') ||
                e.toLowerCase().includes('passwords')
            );

            // Check for weak password
            var hasWeakPassword = validationErrors.some(e => 
                e.toLowerCase().includes('weak') || 
                e.toLowerCase().includes('strong') ||
                e.toLowerCase().includes('characters') ||
                e.toLowerCase().includes('password must')
            );

            if (hasInvalidCredentials) {
                errorTitle = 'Invalid Credentials';
                errorIcon = 'warning';
            } else if (hasPasswordMismatch) {
                errorTitle = 'Password Mismatch';
            } else if (hasWeakPassword) {
                errorTitle = 'Password Requirements';
            }

            // Build error list HTML
            var errorHtml = '<ul class="text-left text-sm space-y-2 py-2 max-h-60 overflow-y-auto">';
            validationErrors.forEach(function(error) {
                errorHtml += `<li class="flex items-start gap-2"><div class="w-1.5 h-1.5 rounded-full bg-error flex-shrink-0 mt-1.5"></div><span>${escapeHtml(error)}</span></li>`;
            });
            errorHtml += '</ul>';

            Swal.fire({
                icon: errorIcon,
                title: errorTitle,
                html: errorHtml,
                customClass: authSwalConfig.glassCustomClass,
                buttonsStyling: false,
                allowOutsideClick: true,
                allowEscapeKey: true
            });
            alertDisplayed = true;
        @endif

        // ============================================
        // SUCCESS MESSAGES
        // ============================================

        {{-- Registration Success --}}
        @if (session('success_message') && str_contains(session('success_message'), 'Account created'))
            Swal.fire({
                icon: 'success',
                title: '🎉 Registration Successful!',
                html: `<p class="text-white/80">{{ session('success_message') }}</p>`,
                customClass: authSwalConfig.glassCustomClass,
                buttonsStyling: false,
                allowOutsideClick: false,
                timer: 5000,
                timerProgressBar: true
            });
            alertDisplayed = true;
        @endif

        {{-- Login Success --}}
        @if (session('success_message') && (str_contains(session('success_message'), 'Welcome') || str_contains(session('success_message'), 'back')))
            Swal.fire({
                icon: 'success',
                title: '👋 Welcome Back!',
                html: `<p class="text-white/80">{{ session('success_message') }}</p>`,
                customClass: authSwalConfig.glassCustomClass,
                buttonsStyling: false,
                allowOutsideClick: false,
                timer: 4000,
                timerProgressBar: true
            });
            alertDisplayed = true;
        @endif

        {{-- Generic Success Message --}}
        @if (session('success') && !session('success_message'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                customClass: authSwalConfig.glassCustomClass,
                buttonsStyling: false,
                timer: 4000,
                timerProgressBar: true
            });
            alertDisplayed = true;
        @endif

        {{-- Generic Success Message (alternative key) --}}
        @if (session('success_message') && !str_contains(session('success_message'), 'Account created') && !str_contains(session('success_message'), 'Welcome'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success_message') }}",
                customClass: authSwalConfig.glassCustomClass,
                buttonsStyling: false,
                timer: 4000,
                timerProgressBar: true
            });
            alertDisplayed = true;
        @endif

        // ============================================
        // STATUS MESSAGES (Password Reset)
        // ============================================

        {{-- Password Reset Link Sent --}}
        @if (session('status') && str_contains(session('status'), 'reset link'))
            Swal.fire({
                icon: 'success',
                title: '📧 Reset Link Sent!',
                html: `<p class="text-white/80">{{ session('status') }}</p>
                       <p class="text-white/50 text-xs mt-2">Please check your email inbox (and spam folder) for the password reset link.</p>`,
                customClass: authSwalConfig.glassCustomClass,
                buttonsStyling: false,
                allowOutsideClick: false,
                timer: 6000,
                timerProgressBar: true
            });
            alertDisplayed = true;
        @endif

        {{-- Password Reset Successful --}}
        @if (session('status') && (str_contains(session('status'), 'reset') || str_contains(session('status'), 'password')))
            Swal.fire({
                icon: 'success',
                title: '🔐 Password Reset Complete!',
                html: `<p class="text-white/80">{{ session('status') }}</p>
                       <p class="text-white/50 text-xs mt-2">You can now sign in with your new password.</p>`,
                customClass: authSwalConfig.glassCustomClass,
                buttonsStyling: false,
                allowOutsideClick: false,
                confirmButtonText: 'Go to Login',
                showCancelButton: true,
                cancelButtonText: 'Stay Here',
                preConfirm: () => {
                    window.location.href = '{{ route("login") }}';
                }
            });
            alertDisplayed = true;
        @endif

        {{-- Generic Status Message (for other status messages like logout, etc.) --}}
        @if (session('status') && !str_contains(session('status'), 'reset') && !str_contains(session('status'), 'verification') && session('status') !== 'verification-link-sent')
            Toast.fire({
                icon: 'info',
                title: "{{ session('status') }}"
            });
            alertDisplayed = true;
        @endif

        // ============================================
        // ERROR MESSAGES
        // ============================================

        {{-- General Error Messages --}}
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops! Something went wrong',
                text: "{{ session('error') }}",
                customClass: authSwalConfig.glassCustomClass,
                buttonsStyling: false,
                allowOutsideClick: true,
                allowEscapeKey: true
            });
            alertDisplayed = true;
        @endif

        // ============================================
        // LARAVEL BREEZE EMAIL VERIFICATION
        // ============================================

        {{-- Email Verification Notice / Link Sent --}}
        @if (session('status') && (str_contains(session('status'), 'verification') || session('status') === 'verification-link-sent'))
            @php
                $verificationStatus = session('status');
                $verificationMessage = $verificationStatus === 'verification-link-sent' 
                    ? 'A new verification link has been sent to your email address.' 
                    : $verificationStatus;
            @endphp
            Swal.fire({
                icon: 'success',
                title: '📧 Verification Email Sent!',
                html: `<p class="text-white/80">{{ $verificationMessage }}</p>
                       <p class="text-white/50 text-xs mt-2">Please check your inbox and click the verification link.</p>`,
                customClass: authSwalConfig.glassCustomClass,
                buttonsStyling: false,
                allowOutsideClick: true,
                timer: 6000,
                timerProgressBar: true
            });
            alertDisplayed = true;
        @endif

        // Mark as shown if any alert was displayed
        if (alertDisplayed) {
            window.authAlertsShown = true;
            // Also store in sessionStorage for the current session to prevent re-firing on back/forward
            // especially during Turbo restoration
            sessionStorage.setItem('lastAlertShown', Date.now());
        }
    }

    // Function to initialize alert listeners
    function initAuthAlerts() {
        // Skip if this is a Turbo preview
        if (document.documentElement.hasAttribute('data-turbo-preview')) {
            return;
        }

        // Reset the flag on new page loads (Turbo will re-run this)
        window.authAlertsShown = false;
        
        // Try to show alerts immediately (for cached pages)
        showAuthAlerts();
        
        // Also try after a delay to handle CDN loading
        setTimeout(showAuthAlerts, 500);
        setTimeout(showAuthAlerts, 1000);
        setTimeout(showAuthAlerts, 2000);
    }

    // Listen for DOMContentLoaded (initial page load)
    document.addEventListener('DOMContentLoaded', initAuthAlerts);

    // Listen for Turbo Drive navigation events
    document.addEventListener('turbo:load', initAuthAlerts);
    
    // Also listen for turbo:render for older Turbo versions
    document.addEventListener('turbo:render', initAuthAlerts);

    // Fallback: Listen for any page visibility change
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            setTimeout(showAuthAlerts, 300);
        }
    });
</script>
