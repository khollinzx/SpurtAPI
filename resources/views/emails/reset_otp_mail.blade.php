@extends('master_email')
@section('content')
    <tr>
        <td style="background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
            <div style="display: block; margin: 0px; padding:40px; padding-top: 20px; background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
                <div>
                    <h2 style="text-align:center;color:#121212;font-weight:600;font-size:1.5rem">Hi {{ $name }}</h2>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Your new OTP has been sent.</p>
                    <br>
                    <div>
                        <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Here is your OTP ({{$otp}}) to activate your account.</p>
                    </div>
                    {{--                    <div>--}}
                    {{--                        <span style="color: #707070; padding-top:50px; padding-bottom: 5px; display: block;">Or use this link to login</span>--}}
                    {{--                        <a style="color: #00ACCB; font-size: 14px ;max-width: 75%; margin: 0 auto; line-height: 1.5; display: block; " href="{{ url('login') }}">https://santa.crowdyvest.com/login</a>--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </td>
    </tr>
@endsection
