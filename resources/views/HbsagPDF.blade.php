<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HBSAG Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .header {
            text-align: center;
            margin: 5px 0 10px;
        }

        .header h2 {
            font-size: 22px;
            margin-bottom: 2px;
        }

        .header h3 {
            font-size: 12px;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        .section-title {
            font-weight: bold;
            text-align: center;
            font-size: 15px;
        }

        .bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .ml-100 {
            margin-left: 100px;
            font-size: 15px;
        }


        .mb-10 {

            margin-top: 12px;
        }

        .mb-10 tr td {
            font-size: 15px;

        }

        .mt-15 {
            margin-top: 15px;
        }

        .mb-15 {
            margin-bottom: 15px;
        }

        .text-indent-30 {
            text-indent: 30px;
        }

        .text-indent-30 tr td b {
            text-transform: capitalize;
        }

        .text-indent-10 {
            text-indent: 10px;
        }


        .text-indent-40 {
            text-indent: 40px;
        }

        .text-indent-50 {
            text-indent: 50px;
        }

        .mt--10 {
            margin-top: -10px;
        }

        hr {
            height: 2px;
            /* thickness of the line */
            background-color: #000;
            /* solid black */
            border: none;
            margin-top: -13px;
            margin-bottom: -13px;
        }


        .analysis-table {
            font-size: 15px;
        }

        .cpn_logo {
            position: absolute;
            top: 10px;
            left: 100px;
            height: 85px;
            width: 85px;
        }

        .lab_logo {
            position: absolute;
            top: 5px;
            right: 100px;
            height: 95px;
            width: 95px;
        }

        .pathologist {
            position: relative;
        }

        .sig {
            position: absolute;
            top: -50px;
            left: 50px;
            height: 110px;
            width: 120px;
        }


        .cap {
            text-transform: uppercase;
        }

        /* .pad-left {
            
            padding-left: 40px;
        } */

        /* .pad-right{
            padding-right: 40px;
        } */

        .margin {
            padding: 10px;
        }




        .spaced-text {
            word-spacing: 430px;
            /* Adjust space between words */
            text-indent: 4px;
        }

        .pad-top {

            border-bottom: solid 2px black;

        }

        .pad-bot {
            padding-bottom: 150px;
        }

        .pl {
            padding-left: 100px;
        }
    </style>
</head>

<body>


    <div class="header">
        <img class="cpn_logo"
            src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/cpn_log.jpg'))) }}">

        <h2>CONCEPCION DIAGNOSTIC CENTER</h2>
        <h3>LOCAL GOVERNMENT CONCEPCION TARLAC - CLINICAL LABORATORY</h3>
        <h3>SAN NICOLAS POBLACION CONCEPCION TARLAC</h3>
        <h3>cpdiagnosticlab@gmail.com | (045) 9317-925</h3>
        <img class="lab_logo"
            src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/lab_logo.png'))) }}">
    </div>

    <br>
    <hr>

    <table class="ml-100 mb-10">
        <tr>
            <td><strong>Name &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; :</strong> {{ ucwords($patient->name ?? ' ') }}
            </td>
            <td>
                <strong>Age:</strong> {{ $patient->age ?? ' ' }} &nbsp;
                <strong>Gender:</strong> {{ ucfirst($patient->gender ?? ' ') }}
            </td>
        </tr>
        <tr>
            <td><strong>Date of Birth :</strong>
                {{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('F j, Y') : ' ' }}
            </td>
            <td><strong>Reported:</strong>
                {{ \Carbon\Carbon::parse($serology->reported_at ?? now())->format('F j, Y') }}
            </td>

        </tr>
        <tr>
            <td><strong>Company&nbsp; &nbsp; &nbsp; &nbsp;:</strong> {{ $patient->company ?? 'OPD' }}</td>

        </tr>
        <tr>
            <td colspan="2"><strong>Address &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;:</strong> {{ $patient->address ?? ' ' }}
            </td>
        </tr>
    </table>

    <br>
    <hr>
    <p class="section-title">SEROLOGY RESULT</p>
    <hr>






    <table class="analysis-table mt-15 mb-15" style="width: 100%;">

        <tr>
            <td class="pad-top pl"><b>Test</b></td>
            <td class="pad-top"><b>Result:</b></td>
        </tr>

        <tr>
            <td class="pl"><b>Hepatitis B Virus surface antigen Screening</b></td>
            @if (!empty($serology->hbsag_result))
                @php
                    $hbsag_res = is_array($serology->hbsag_result)
                        ? $serology->hbsag_result
                        : json_decode($serology->hbsag_result, true);

                    // Get string if wrapped in array
                    $result = is_array($hbsag_res) ? $hbsag_res[0] : $hbsag_res;

                    // Determine dynamic padding
                    $padding = strtoupper($result) === 'REACTIVE' ? '80px' : '40px';
                @endphp

                <td style="padding-right: {{ $padding }};">
                    <b class="cap" style="color: {{ strtoupper($result) === 'REACTIVE' ? 'red' : 'black' }};">
                        {{ ucfirst(strtolower($result)) }}
                    </b>
                </td>
            @endif
        </tr>


        <tr>
            <td class="pl">Kit use: {{ $serology->hbsag_kit ?? ' ' }}</td>
            <td></td>
        </tr>

        <tr>
            <td class="pl">LOT NO. {{ $serology->hbsag_lot_no ?? ' ' }}</td>
            <td></td>
        </tr>

        <tr>
            <td class="pl">Expiration Date:
                {{ \Carbon\Carbon::parse($serology->hbsag_expiration_date ?? now())->format('F Y') }}</td>
            <td></td>
        </tr>

        <tr>
            <td class="pad-bot"></td>
            <td class="pad-bot"></td>
        </tr>






    </table>


    <hr>
    <p class="ml-100"><strong>Remarks:</strong> {{ $serology->hbsag_remarks ?? 'N/A' }}</p>

    <table class="ml-100">
        <tr>
            <td>
                <p><strong>{{ $serology->medical_technologist ?? 'JULLIUS ANUSENCION, RMT' }}</strong></p>
                <p class="mt--10">
                    Medical Technologist &nbsp;&nbsp;&nbsp;&nbsp; Lic. No.:

                    @if ($serology->medical_technologist === 'JULLIUS D. ANUSENCION, RMT')
                        0056624
                    @elseif ($serology->medical_technologist === 'KRISTINA CASSANDRA F. SANTOS, RMT')
                        0099818
                    @elseif ($serology->medical_technologist === 'KATE ANGELINE M. SALAS, RMT')
                        0115834
                    @elseif ($serology->medical_technologist === 'MARY GRACE L. BERNARDO, RMT')
                        0105656
                    @elseif ($serology->medical_technologist === 'JANIELLE M. PASAMONTE, RMT')
                        0092719
                    @endif
                </p>
            </td>
            <td class="pathologist">
                <img class="sig"
                    src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/images/sig.png'))) }}">
                <p><strong>NICK R. FERNANDEZ, M.D.</strong></p>
                <p class="mt--10">
                    Pathologist &nbsp;&nbsp;&nbsp;&nbsp; Lic. No.: 0100691
                </p>
            </td>
        </tr>
    </table>

</body>

</html>