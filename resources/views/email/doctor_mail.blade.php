@component('mail::message')
# Introduction

Welcome Doctor {{ $doctor->name }}

<strong>Your Name: {{ $doctor->name }}</strong><br>
<strong>Your Name: {{ $doctor->qualify }}</strong><br>
<strong>Email: {{ $doctor->doctor_email }}</strong><br>
<strong>Specialization: {{ $doctor->doctor_specilization }}</strong><br>
<strong>Contact: {{ $doctor->doctor_phone }}</strong><br>
<strong>Consultency fee:{{ $doctor->consultency_fee }}</strong><br>
<strong>Password: Is Your Phone Number</strong><br>

@component('mail::button', ['url' => ''])
view your profile
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
