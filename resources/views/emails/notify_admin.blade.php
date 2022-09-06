@extends('master_email')
@section('content')
    <tr>
        <td style="background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
            <div style="display: block; margin: 0px; padding:40px; padding-top: 20px; background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
                <div>
                    <h2 style="text-align:center;color:#121212;font-weight:600;font-size:1.5rem">Hi, i'm AdminBot</h2>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">{{$phrase}}</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Find details below:</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Name: {{$name}} </p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Ticket No: {{$ticket_no}} </p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Best Regards</p>
                </div>
            </div>
        </td>
    </tr>
@endsection
