@extends('master_email')
@section('content')
    <tr>
        <td style="background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
            <div style="display: block; margin: 0px; padding:40px; padding-top: 20px; background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
                <div>
                    <h2 style="text-align:center;color:#121212;font-weight:600;font-size:1.5rem">Hi {{ $name }}</h2>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Your {{ $phrase }} invoice with ticket no: {{$ticket_no}} has been generated</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Your payable invoice no is {{$invoice_no}}, and your preferred currency is in {{$currency}}</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">You can login into you dashboard and proceed with payments, or use the 'Pay Invoice' on any of our website</p>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Best Regards</p>
                </div>
            </div>
        </td>
    </tr>
@endsection
