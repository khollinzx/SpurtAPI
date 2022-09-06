@extends('master_email')
@section('content')
    <tr>
        <td style="background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
            <div style="display: block; margin: 0px; padding:40px; padding-top: 20px; background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
                <div>
                    <h2 style="text-align:center;color:#121212;font-weight:600;font-size:1.5rem">Hi {{ $name }}</h2>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Your {{ $phrase }} with ticket no: {{$ticket_no}} has been approved</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Your payment invoice will be sent to you for payment</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Please use the following information to gain access to our portal</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">https://admin.spurt.group/</p>
                    <br>
                    <div>
                        <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">User Type: {{$userType}} </p>
                    </div>
                    <div>
                        <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Email: {{$email}}</p>
                    </div>
                    <div>
                        <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Password: {{$password}}</p>
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
