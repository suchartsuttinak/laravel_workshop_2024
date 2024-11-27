<div class="mx-auto flex flex-col items-center justify-center min-h-screen bg-orange-200 h-screen bg-gradient-to-tr from-orange-500 to-white">
    <div class="w-full max-w-md border border-orange-400 rounded-md bg-white p-4 ">
        <div class="text-2xl font-bold">
            <i class="fa fa-user me-2"></i>
            Sign In To BackOffice
        </div>
            <form class="mt-5" wire:submit ="sigin">
                <div>User Name</div>
                <input type="text" wire:model="username" placeholder="username" class="form-control">
                @if (isset($errorUsername))
                    <div class="text-red-500 mt-2">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        {{ $errorUsername }}
                    </div>
                @endif

                <div class="mt-4 ">Password</div>
                <input type="password" wire:model="password" placeholder="password" class="form-control">
                @if (isset($errorPassword))
                    <div class="text-red-500 mt-2">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        {{ $errorPassword }}
                    </div>
                @endif
                <button type="submit" class="btn btn-primary mt-5">Sign In</button>
            </form>
            @if (isset($error))
                <div class="text-red-500 mt-4">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    {{ $error }}
                </div>
            @endif
        </div>
   
</div>
