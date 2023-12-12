@extends('layouts.forntlogin')
@section('forntlogin')
    <section id="main">
        <div class="overlay"></div>
        <div class="container">

            <div class="row justify-content-center align-items-center mt-5">
                <div class="col-md-5 align-self-center">
                    <img src="{{ asset('logo.png') }}" alt="Img Prob" class="w-100">
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="row">
                            <div class="col-md-4 offset-md-4">
                                <p class="text-center mb-0 user">
                                    <i class="far fa-user"></i>
                                </p>
                                <h5 class="mb-3 font-weight-bold text-center mt-3"></h5>

                                <h5 class="danger text-center">
                                    @if ($errors->has('status'))
                                        <strong>{{ $errors->first('status') }}</strong>
                                    @endif
                                </h5>


                            </div>
                        </div>

                        <div class="card-body text-center">
                            @if ($errors->any())
                                <h4><span style="color: red;">{{ $errors->first() }}</span></h4>
                            @endif

                            <form method="POST" action="{{ route('user.login') }}">
                                @csrf
                                <input type="text" required autocomplete="username" name="username" autofocus
                                    placeholder="Username">

                                @if ($errors->has('username'))
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                                <input id="password" type="password" class="mt-2" name="password" required
                                    placeholder="Enter Password">

                                @if ($errors->has('password'))
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

                                {{-- <div class="row mt-5">
                                    <div class="col-md-6 btm">
                                        <input type="checkbox" id="remember" {{ old('remember') ? 'checked' : '' }}>
                          <label for="remember">
                            Remember Me
                          </label>
                                    </div>
                                    <div class="col-md-6 btm">
                                        @if (Route::has('password.request'))
                                            <a style="color: #f4205f" href="{{ route('password.request') }}">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        @endif
                                    </div>

                                </div> --}}
                                <button class="mt-5">
                                    Login
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
