<x-guest-layout>
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-base-content">Verify Email</h2>
        <p class="text-sm opacity-50 mt-1">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?') }}
        </p>
    </div>


    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-block">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="flex justify-center">
            @csrf
            <button type="submit" class="btn btn-ghost btn-sm opacity-60">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
