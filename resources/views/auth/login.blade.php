
<!--
<div style="font-size: 40px; text-align: center; padding-top: 60px; color: red;">
    Your system is shut down due to a 12-months overdue bill.
    Please contact with your IT Department.
</div> -->

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login - HRM System</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('login_page/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('login_page/css/fontawesome-all.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('login_page/css/style.css') }}">

    <style>
        .client-info {
            max-width: 440px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px
        }
        .client-img {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 15px;
            height: 130px;
            width: 130px;
            border-radius: 9999px;
            border: 2px solid#f1f5f9;
            padding: 17px;
        }
        .client-name {
            font-size: 20px;
            font-weight: 600;
            color: #4e3333;

            /* background: #105C05;
            background: -webkit-linear-gradient(to right, #105C05 0%, #19CC12 50%, #0D911F 100%);
            background: -moz-linear-gradient(to right, #105C05 0%, #19CC12 50%, #0D911F 100%);
            background: linear-gradient(to right, #105C05 0%, #19CC12 50%, #0D911F 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent; */
        }

        .visit-us {
            bottom: 40px;
        }

        .fxt-template-layout31 .fxt-content-wrap {
            align-items: center;
            justify-content: center;
        }

        .fxt-template-layout31 .fxt-form-content {
            width: 35%;
        }

        .fxt-template-layout31 .fxt-form-content .fxt-main-form {
            padding: 50px;
        }

        @media (max-width: 991px) {
            /* .fxt-inner-wrap {
                margin-bottom: 60px;
            } */

            .fxt-template-layout31 .fxt-form-content {
                width: 100%;
            }

            .fxt-template-layout31 .fxt-form-content .fxt-main-form {
                padding: 20px;
            }
        }

        @media (min-width: 450px) {
            .client-name {
                font-size: 25px;
            }
            .visit-us {
                bottom: 60px;
            }
        }
    </style>
</head>

<body>

    {{-- <p style="text-align:center;font-size: 60px; color:red; margin: 20px; background-color: black;">Your system has been shut down due to an overdue bill pending <br>  <span style="color:lightgreen;">for 3 months .</span>  <br>Please make the payment to restore uninterrupted service.</p> --}}

    {{-- <div style="display: flex; align-items: center; justify-content: center; height: 100vh; background-color: white; ">
        <p style="text-align: center; font-size: 46px; color: red; font-weight: bold; max-width: 1200px; line-height: 1.5;">
            ৭ মাসের সার্ভার খরচ এবং সাপোর্ট ও মেইনটেন্যান্স বিল বকেয়া রয়েছে।
            অনুগ্রহ করে বকেয়া বিল পরিশোধ করে সার্ভিসটি কন্টিনিউ করার জন্য সহযোগিতা কামনা করছি।
            সাময়িক অসুবিধার জন্য আন্তরিকভাবে দুঃখিত এবং আপনার সদয় অনুধাবনের জন্য ধন্যবাদ।
        </p>
    </div> --}}




    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <section class="fxt-template-animation fxt-template-layout31">
        <div class="fxt-content-wrap">
            <div class="fxt-form-content">
                <div class="fxt-main-form">
                    <h3 style="font-weight: 700; font-size: 30px">HRM System</h3>

                    <div class="client-info">
                        <div class="client-img">
                            <img src="{{ asset('dist/img/com-logo999.jpg') }}" alt="" height="90" width="90">
                        </div>

                        <h4 style="margin: 0; margin-bottom: 10px; text-align: center;">{{ config('configaration.company_name') }}</h4>
                    </div>

                    <div class="fxt-inner-wrap">
                        <form action="{{ route('login') }}" method="post">
                            @csrf

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">

                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="position-relative">
                                            <input id="password" type="password" class="form-control" name="password" placeholder="********">
                                            <i toggle="#password" class="fa fa-fw fa-eye toggle-password field-icon" style="cursor: pointer"></i>
                                        </div>

                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="fxt-checkbox-wrap">
                                            <div class="fxt-checkbox-box mr-3">
                                                <input id="checkbox1" type="checkbox" name="remember">
                                                <label for="checkbox1" class="ps-4">Keep me logged in</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="fxt-btn-fill">Log in</button>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mt-5">
                                        <span>Developed by</span>
                                        <a href="https://amanahsoft.com/" target="_blank">
                                            Amanah Soft
                                            <i class="fas fa-external-link-alt" style="font-size:10px;margin-left:5px"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- jquery-->
    <script src="{{ asset('login_page/js/jquery-3.5.0.min.js') }}"></script>

    <!-- Bootstrap js -->
    <script src="{{ asset('login_page/js/bootstrap.min.js') }}"></script>

    <!-- Imagesloaded js -->
    <script src="{{ asset('login_page/js/imagesloaded.pkgd.min.js') }}"></script>

    <script src="{{ asset('login_page/js/main.js') }}"></script>
</body>

</html>
