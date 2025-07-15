<div class="loginwrapper">
    <div class="lg-inner-column">
        <div class="right-column  relative">
            <div class="inner-content h-full flex flex-col bg-white dark:bg-slate-800">
                <div class="auth-box h-full flex flex-col justify-center">
                    <div class="mobile-logo text-center mb-6  block flex justify-center">

                        {{-- <img src="assets/images/logo/logo.svg" alt="" class="mb-10 dark_logo"> --}}
                        <img src="{{ asset('images/logo/dhalia-logo.jpeg') }}" alt=""
                            class="dark_logo" width="250px">
                        <img src="{{ asset('images/logo/dhalia-logo.jpeg') }}" alt=""
                            class="white_logo" width="250px">

                    </div>
                    <div class="text-center 2xl:mb-10">
                        <h4 class="font-medium">Sign In</h4>
                        <div class="text-slate-500 text-base">
                            Sign in to your account to continue
                        </div>
                    </div>
                    <!-- BEGIN: Login Form -->
                    <form class="space-y-4">
                        @csrf
                        <div class="formGroup">
                            <!-- Typo corrected from "fromGroup" to "formGroup" -->
                            <label class="block capitalize form-label">Username</label>
                            <div class="relative">
                                <input type="text" name="username"
                                    class="form-control py-2 @if($errors->first('username')) !border-danger-500 @endif"
                                    placeholder="Username" wire:model="username" required>
                            </div>

                            @if ($errors->first('username'))
                            <span id="nameErrorMsg" class="font-Inter text-sm text-danger-500 pt-2 hidden mt-1"
                                style="display: inline;">
                                {{ $errors->first('username') }}
                            </span>
                            @endif

                        </div>
                        <div class="formGroup">
                            <!-- Typo corrected from "fromGroup" to "formGroup" -->
                            <label class="block capitalize form-label">Password</label>
                            <!-- Typo corrected from "passwrod" to "Password" -->
                            <div class="relative">
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    @class(["form-control pr-9", "!border-danger-500" => $errors->first('password')]) 
                                    placeholder="Password" 
                                    wire:model="password"
                                    required>
                                <button 
                                    id="passIcon" 
                                    class="passIcon absolute top-2.5 right-3 text-slate-300 text-xl p-0 leading-none" 
                                    type="button" 
                                    onclick="togglePassword()">
                                    <iconify-icon id="passwordhide" class="inline-block hidden" icon="mdi:eye-outline"></iconify-icon>
                                    <iconify-icon id="passwordshow" class="inline-block" icon="mdi:eye-off-outline"></iconify-icon>
                                </button>
                            </div>
                            @if ($errors->first('password'))
                            <span id="nameErrorMsg" class="font-Inter text-sm text-danger-500 pt-2 hidden mt-1"
                                style="display: inline;">
                                {{ $errors->first('password') }}
                            </span>
                            @endif
                        </div>
                        <div class="flex justify-between">
                    
                       
                        </div>
                        <button class="btn btn-dark block w-full text-center" wire:click="checkUser">Login</button>
                    </form>
                    <!-- END: Login Form -->
                </div>
                <div class="auth-footer text-center">
                    <p>Copyright 2025</p>
                </div>
            </div>
        </div>
    </div>
</div>
