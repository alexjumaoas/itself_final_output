<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <title>ITSELF - Admin Dashboard</title>
        <meta
            content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
            name="viewport"
        />
        <link
            rel="icon"
            href="{{ asset('assets/img/kaiadmin/favicon.ico') }}"
            type="image/x-icon"
        />

        <!-- Fonts and icons -->
        <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>

        <script>
            WebFont.load({
                google: { families: ["Public Sans:300,400,500,600,700"] },
                custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
                },
                active: function () {
                sessionStorage.fonts = true;
                },
            });
        </script>
        <!-- CSS Files -->
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

        <!-- CSS Just for demo purpose, don't include it in your project -->
        <!-- <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" /> -->
    </head>

    <body>
        <!-- Section: Design Block -->
        <section class="background-radial-gradient overflow-hidden">
            <style>
                .background-radial-gradient {
                background-color: hsl(218, 41%, 15%);
                background-image: radial-gradient(650px circle at 0% 0%,
                    hsl(218, 41%, 35%) 15%,
                    hsl(218, 41%, 30%) 35%,
                    hsl(218, 41%, 20%) 75%,
                    hsl(218, 41%, 19%) 80%,
                    transparent 100%),
                    radial-gradient(1250px circle at 100% 100%,
                    hsl(218, 41%, 45%) 15%,
                    hsl(218, 41%, 30%) 35%,
                    hsl(218, 41%, 20%) 75%,
                    hsl(218, 41%, 19%) 80%,
                    transparent 100%);
                }

                #radius-shape-1 {
                height: 220px;
                width: 220px;
                top: -60px;
                left: -130px;
                background: radial-gradient(#44006b, #ad1fff);
                overflow: hidden;
                }

                #radius-shape-2 {
                border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
                bottom: -60px;
                right: -110px;
                width: 300px;
                height: 300px;
                background: radial-gradient(#44006b, #ad1fff);
                overflow: hidden;
                }

                .bg-glass {
                background-color: hsla(0, 0%, 100%, 0.9) !important;
                backdrop-filter: saturate(200%) blur(25px);
                }

                /* ----------------------------------------------------- */
                .card.bg-glass {
                    max-width: 450px;
                    padding: 5px;
                }
                .background-radial-gradient {
                    min-height: 100vh;
                    width: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                /* ----------------------------------------------------- */
                /* html, body {
                    height: 100%;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .background-radial-gradient {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                } */

                /* ----------------------------------------------------- */
            </style>

            <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
                <div class="row gx-lg-5 align-items-center mb-5">
                    <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                        <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                            Having technical issues?<br />
                            <span style="color: hsl(218, 81%, 75%); font-size: 35px;">Our IT team is ready to assist you.</span>
                        </h1>
                        <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
                            Please submit your support request, and we’ll work quickly to resolve any software, hardware, or network problems you're experiencing.
                        </p>
                    </div>

                    <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                        <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                        <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>
                        <div class="card bg-glass">
                            <div class="card-body px-4 py-5 px-md-5">
                            <form method="post" action="{{ route('login.user') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input 
                                        type="text" 
                                        class="form-control @error('username') is-invalid @enderror" 
                                        id="username" 
                                        name="username" 
                                        value="{{ old('username') }}" autofocus
                                        required
                                    />
                                    @error('username')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="password">Password</label>
                                    <input 
                                        type="password" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        id="password" 
                                        name="password" 
                                        required
                                    />
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group text-center">
                                    <button class="btn btn-primary w-100" type="submit">Login</button>
                                </div>

                                <div class="mt-4 text-center">
                                    <small>Note: Please login using your ID Number</small>
                                </div>
                            </form>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section: Design Block -->


        <!--   Core JS Files   -->
        <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
        <!-- <script src="{{ asset('assets/js/core/popper.min.js') }}"></script> -->
        <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

        <!-- jQuery Scrollbar -->
        <!-- <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script> -->

        <!-- Chart JS -->
        <!-- <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script> -->

        <!-- jQuery Sparkline -->
        <!-- <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script> -->

        <!-- Chart Circle -->
        <!-- <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script> -->

        <!-- Datatables -->
        <!-- <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script> -->

        <!-- Bootstrap Notify -->
        <!-- <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script> -->

        <!-- jQuery Vector Maps -->
        <!-- <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script> -->
        <!-- <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script> -->

        <!-- Sweet Alert -->
        <!-- <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script> -->

        <!-- Kaiadmin JS -->
        <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

        <!-- Kaiadmin DEMO methods, don't include it in your project! -->
        <!-- <script src="{{ asset('assets/js/setting-demo.js') }}"></script> -->
    </body>
</html>



















