

<link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('bower_components/font-awesome/css/font-awesome.min.css')}}">
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>


<style type="text/css">
  
/*@import url("//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css");*/
.login-block{
    background: #DE6262;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #f4f4f4, #DEB887);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #f4f4f4, #66b3ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
/*#f4f4f4, #3c8dbc)*/
float:left;
width:100%;
padding : 50px 0;
height: 100%;
}

.banner-sec{background:url({{asset('dist/img/aaa.png')}})  no-repeat left bottom; background-size:cover; min-height:500px; border-radius: 0 10px 10px 0; padding:0;}
.container{background:#fff; border-radius: 10px; box-shadow:15px 20px 0px rgba(0,0,0,0.1);}
.carousel-inner{border-radius:0 10px 10px 0;}
.carousel-caption{text-align:left; left:5%;}
.login-sec{padding: 50px 30px; position:relative;}
.login-sec .copy-text{position:absolute; width:80%; bottom:20px; font-size:13px; text-align:center;}
.login-sec .copy-text i{color:#FEB58A;}
.login-sec .copy-text a{color:#E36262;}
.login-sec h2{margin-bottom:30px; font-weight:800; font-size:30px; color: #DE6262;}
.login-sec h2:after{content:" "; width:100px; height:5px; background:#FEB58A; display:block; margin-top:20px; border-radius:3px; margin-left:auto;margin-right:auto}
.btn-login{background: #DE6262; color:#fff; font-weight:600;}
.banner-text{width:70%; position:absolute; bottom:40px; padding-left:20px;}
.banner-text h2{color:#fff; font-weight:600;}
.banner-text h2:after{content:" "; width:100px; height:5px; background:#FFF; display:block; margin-top:20px; border-radius:3px;}
.banner-text p{color:#fff;}


</style>


  <section class="login-block">
      <div class="container">
          <div class="row">

              <div class="col-md-8 banner-sec">
              <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">

              </div>
              </div>


              <div class="col-md-4 login-sec" >
                  <img src="{{asset('dist/img/com-logo.jpg')}}" class="img-circle" alt="User Image" style="height: 102px;width: 164px; display: block;margin-left: auto; margin-right: auto;">
                  <!-- <img src="{{asset('dist/img/com-logo.jpg')}}" class="" alt="User Image" style="height: 102px;width: 164px; display: block;margin-left: auto; margin-right: auto;"> -->
                  
                  <h3 style="text-align: center; color: #007F3D">{{Config::get('configaration.company_name')}}</h3>

                  <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="text-uppercase">Email</label>
                            <input type="text" class="form-control" placeholder="Email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1" class="text-uppercase">Password</label>
                            <input type="password" class="form-control" placeholder="Password" name="password">
                        </div>

                        <div class="form-check" style="margin-left: 271px;">
                            <button type="submit" class="btn btn-success float-right">Submit</button>
                        </div>

                  </form>


                  <div class="copy-text">Powered <i class="fa fa-heart"></i> by <a href="http://i-infotechsolution.com" target="_blank">i-infotech</a></div>
              </div>

          </div>
      </div>
  </section>

