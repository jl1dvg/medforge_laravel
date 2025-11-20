@extends('legacy.layouts.app')

@section('title', 'Log in')
@section('body_class', 'hold-transition theme-primary bg-img')
@section('showShell', false)

@section('content')
<div class="container h-p100" style="background-image: url({{ asset('build/assets/bg-1-DqybnYZf.jpg') }}); background-size: cover;">
    <div class="row align-items-center justify-content-md-center h-p100">
        <div class="col-12">
            <div class="row justify-content-center g-0">
                <div class="col-lg-5 col-md-5 col-12">
                    <div class="bg-white rounded10 shadow-lg">
                        <div class="content-top-agile p-20 pb-0">
                            <h2 class="text-primary">Empecemos</h2>
                            <p class="mb-0">Inicia sesión para continuar a Doclinic.</p>
                        </div>
                        <div class="p-40">
                            @if(request()->boolean('error'))
                                <div class="alert alert-danger text-center">Credenciales incorrectas.</div>
                            @endif
                            <form action="/auth/login" method="post">
                                @csrf
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-transparent"><i class="ti-user"></i></span>
                                        <label for="username" class="visually-hidden">Usuario</label>
                                        <input type="text" id="username" name="username"
                                               class="form-control ps-15 bg-transparent" placeholder="Username"
                                               required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text  bg-transparent"><i class="ti-lock"></i></span>
                                        <label for="password" class="visually-hidden">Contraseña</label>
                                        <input type="password" id="password" name="password"
                                               class="form-control ps-15 bg-transparent"
                                               placeholder="Password" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="checkbox">
                                            <input type="checkbox" id="basic_checkbox_1">
                                            <label for="basic_checkbox_1">Acuérdate de mí</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="fog-pwd text-end">
                                            <a href="#" class="hover-warning"><i class="ion ion-locked"></i> ¿Olvidaste la contraseña?</a><br>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-danger mt-10">INICIAR SESIÓN</button>
                                    </div>
                                </div>
                            </form>
                            <div class="text-center">
                                <p class="mt-15 mb-0">¿No tienes una cuenta?<a href="auth_register.php" class="text-warning ms-5">Regístrate</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="mt-20 text-white">- Resgístrate con -</p>
                        <p class="gap-items-2 mb-20">
                            <a class="btn btn-social-icon btn-round btn-facebook" href="#"><i class="fa fa-facebook"></i></a>
                            <a class="btn btn-social-icon btn-round btn-twitter" href="#"><i class="fa fa-twitter"></i></a>
                            <a class="btn btn-social-icon btn-round btn-instagram" href="#"><i class="fa fa-instagram"></i></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
