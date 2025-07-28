<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CBC Result</title>
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

        .pad-left{
            padding-left: 60px;
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
                {{ \Carbon\Carbon::parse($hematology->reported_at ?? now())->format('F j, Y') }}
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
    <p class="section-title">HEMATOLOGY RESULT</p>
    <hr>
    

    <table class="analysis-table ml-100 mt-15 mb-15" style="width: 87%;">
        <tr>
            <td><b>TEST</b></th>
            <td><b>RESULT</b></th>
            <td><b>UNIT</b></th>
            <td><b>REF. RANGE</b></th>
            <td class="pad-left"><b>TEST</b></th>
            <td><b>RESULT</b></th>
            <td><b>UNIT</b></th>
            <td><b>REF. RANGE</b></th>
        </tr>

        <tr>
            <td>
                <b>WBC:</b>
            </td>
            <td>
                <b class="cap"> {{ $hematology->cbc_wbc ?? ' ' }}</b>
            </td>
            <td>
                x 10^9/L
            </td>
            <td>
                3.5 - 9.5
            </td>
           
            <td class="pad-left"><b>MCV:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_mcv ?? ' ' }}</b></td>
            <td>fl</td>
            <td>82 - 100</td>
        </tr>

        <tr>
            <td><b>NEU%:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_neu ?? ' ' }}</b></td>
            <td>%</td>
            <td>40 - 75</td>
            <td class="pad-left"><b>MCH:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_mch ?? ' ' }}</b></td>
            <td>pg</td>
            <td>27 - 34</td>
        </tr>

        <tr>
            <td><b>LYM%:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_lym ?? ' ' }}</b></td>
            <td>%</td>
            <td>20 - 50</td>
            <td class="pad-left"><b>MCHC:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_mchc ?? ' ' }}</b></td>
            <td>g/l</td>
            <td>316 - 354</td>
        </tr>

        <tr>
            <td><b>MON%:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_mon ?? '' }}</b></td>
            <td>%</td>
            <td>3 - 12</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td><b>EOS%:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_eos ?? ' ' }}</b></td>
            <td>%</td>
            <td>0.5 - 5</td>
            <td class="pad-left"><b>PLT:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_plt ?? ' ' }}</b></td>
            <td>x 10^9/L</td>
            <td>125 - 350</td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td><b>RBC:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_rbc ?? ' ' }}</b></td>
            <td>x 10^12/L</td>
            <td>3.8 - 5.1</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td><b>HGB:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_hgb ?? ' ' }}</b></td>
            <td>g/L</td>
            <td>115 - 150</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td><b>HCT:</b></td>
            <td><b class="cap"> {{ $hematology->cbc_hct ?? ' ' }}</b></td>
            <td>%</td>
            <td>35 - 45</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        

     
      
      
    </table>
   

    <hr>
    <p class="ml-100"><strong>Remarks:</strong> {{ $hematology->cbc_remarks ?? 'N/A' }}</p>

    <table class="ml-100">
        <tr>
            <td>
                <p><strong>{{ $hematology->medical_technologist ?? 'JULLIUS ANUSENCION, RMT' }}</strong></p>
                <p class="mt--10">
                    Medical Technologist &nbsp;&nbsp;&nbsp;&nbsp; Lic. No.:

                    @if ($hematology->medical_technologist === 'JULLIUS D. ANUSENCION, RMT')
                        0056624
                    @elseif ($hematology->medical_technologist === 'KRISTINA CASSANDRA F. SANTOS, RMT')
                        0099818
                    @elseif ($hematology->medical_technologist === 'KATE ANGELINE M. SALAS, RMT')
                        0115834
                    @elseif ($hematology->medical_technologist === 'MARY GRACE L. BERNARDO, RMT')
                        0105656
                    @elseif ($hematology->medical_technologist === 'JANIELLE M. PASAMONTE, RMT')
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