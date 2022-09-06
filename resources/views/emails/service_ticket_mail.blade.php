@extends('master_email')
@section('content')
    <tr>
        <td style="background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
            <div style="display: block; margin: 0px; padding:40px; padding-top: 20px; background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
                <div>
                    <h2 style="text-align:center;color:#121212;font-weight:600;font-size:1.5rem">Hello {{ $name }}</h2>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Thank's for your request!</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">This is to confirm that your
                        {{$phrase}} with ticket no: {{$ticket_no}} has been received, one of our staff's will be in touch with you within 24 hours</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">In the meantime, you can access a pool of resources here: https://admin.spurt.group/</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Best Regards</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Spurt!</p>
                </div>
            </div>
        </td>
    </tr>
@endsection
