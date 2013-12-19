<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{ HTML::style('packages/bootstrap/css/bootstrap.min.css') }}
    {{ HTML::style('css/main.css')}}
    <title>IRCBot Authentication</title>
  </head>
 
  <body>
      <!-- Fixed navbar -->
      <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Project name</a>
          </div>
          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="#">Home</a></li>
              <li>{{ HTML::link('users/register', 'Register') }}</li>   
              <li>{{ HTML::link('users/login', 'Login') }}</li>
              <li>TESTTEST</li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
      
      
      
      
      <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="navbar-inner">
         <div class="container">
            <ul class="nav">  
               <li>{{ HTML::link('users/register', 'Register') }}</li>   
               <li>{{ HTML::link('users/login', 'Login') }}</li>   
            </ul>  
         </div>
      </div>
   </div>
      
    <div class="container">
      @if(Session::has('message'))
         <p class="alert">{{ Session::get('message') }}</p>
      @endif
      
      {{ $content }}
      
    </div>   
      
  </body>
</html>