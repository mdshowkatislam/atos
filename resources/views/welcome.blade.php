<!DOCTYPE html>
<html>
<head>
  <title>Smart Attendance</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      text-align: center;
      background-color: #f4f4f4;
    }

    .header {
      background-color: #333;
      color: white;
      padding: 10px;
      text-align: right;
    }

    .container {
      max-width: 800px;
      margin: 30px auto;
      padding: 20px;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .logo {
      max-width: 150px;
      margin: 0 auto;
    }

    .logo img {
      width: 100%;
    }

    .welcome-text {
      margin: 20px 0;
    }

    .fingerprint-img img {
      max-width: 100%;
      height: auto;
      border-radius: 5px;
    }
  </style>
</head>
<body style="background-color: #07415D">

  
        @if (Route::has('login'))
            <nav >
                @auth
                    <a href="{{ url('/home') }}"style="color:white"
                        >
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" style="color:white"
                       >
                        Log in
                    </a>
                    /     /
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" style="color:yellow"
                           >
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif


  <div class="container">
    <div class="logo">
      <img src="{{ asset('images/sl1.png') }}" alt="Logo"> <!-- Replace with your actual logo path -->
    </div>

    <div class="welcome-text">
      <h2>Welcome to Smart Attendance</h2>
      <p>This system uses fingerprint authentication for fast and secure student attendance in school.</p>
    </div>

    <div class="fingerprint-img">
      <img src="{{ asset('images/f1.png') }}" alt="Fingerprint Authentication"> <!-- Replace with your actual image path -->
    </div>
  </div>

</body>
</html>