<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Prescription</title>
</head>
<body>
    <h2>Your Prescription</h2>
    <p>Dear {{ $prescription->patient->name }},</p>

    <p>Your doctor, {{ $prescription->doctor->name }}, has prescribed the following medications:</p>

    <h3>Medication Details:</h3>
    <p>{{ $prescription->medication_details }}</p>

    <h3>Instructions:</h3>
    <p>{{ $prescription->instructions }}</p>

    <h3>Doctor's Notes:</h3>
    <p>{{ $prescription->doctor_notes ?? 'No notes available.' }}</p>

    <p>Prescription Date:
        {{ $prescription->prescription_date ? \Carbon\Carbon::parse($prescription->prescription_date)->format('Y-m-d') : 'Not specified' }}
    </p>


    <p>Thank you for choosing our service!</p>
</body>
</html>
