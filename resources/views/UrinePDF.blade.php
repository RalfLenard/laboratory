<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Urinalysis Result</title>
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
    </style>
</head>

<body>

    @php
        use Carbon\Carbon;
        $casts = $clinical->urinalysis_casts ?? [];
        $crystals = $clinical->urinalysis_crystals ?? [];
        $fungal = $clinical->urinalysis_fungal_elements ?? [];
        $parasites = $clinical->urinalysis_parasite ?? [];
    @endphp

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
                {{ \Carbon\Carbon::parse($clinical->created_at)->format('F j, Y') }}
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
    <p class="section-title">CLINICAL MICROSCOPY RESULT</p>
    <hr>
    <p class="ml-100"><strong>Test:</strong> Urinalysis</p>

    <hr>

    <table class="analysis-table ml-100 mt-15 mb-15" style="width: 80%;">
        <tr>
            <td class="bold underline" colspan="2">Physical Analysis</td>

            <td class="bold underline" colspan="3">Microscopic Analysis</td>


        </tr>
        <tr>
        <td class="text-indent-30">Color:</td>
            <td><b class="cap">{{ $clinical->urinalysis_color ?? ' ' }}</b></td>
            <td class="text-indent-30">WBC:</td>
            <td><b class="cap">{{ $clinical->urinalysis_wbc ?? ' ' }}</b></td>
            <td>/HPF</td>
        </tr>

        <tr>
        <td class="text-indent-30">Tranparency:</td>
            <td><b class="cap">{{ $clinical->urinalysis_transparency ?? ' ' }}</b></td>
            <td class="text-indent-30">RBC:</td>
            <td><b class="cap">{{ $clinical->urinalysis_rbc ?? ' ' }}</b></td>
            <td>/HPF</td>
        </tr>

        <tr>
            <td class="bold underline" colspan="2">Physical Analysis</td>
            <td class="text-indent-30">Bacteria:</td>
            <td><b class="cap">{{ $clinical->urinalysis_bacteria ?? ' ' }}</b></td>
            <td>/LPF</td>
        </tr>

        <tr>
        <td class="text-indent-30">Glucose:</td>
            <td><b class="cap">{{ $clinical->urinalysis_glucose ?? ' ' }}</b></td>
            <td class="text-indent-30">Epithelial Cells:</td>
            <td><b class="cap">{{ $clinical->urinalysis_epithelial_cells ?? ' ' }}</b></td>
            <td>/LPF</td>
        </tr>

        <tr>
        <td class="text-indent-30">Protein:</td>
            <td><b class="cap">{{ $clinical->urinalysis_protein ?? ' ' }}</b></td>

            @if (!empty($clinical->urinalysis_amorphous))
            <td class="text-indent-30">Amorphous Urates:</td>
                <td><b class="cap">{{ $clinical->urinalysis_amorphous }}</b></td>
            @elseif (!empty($clinical->urinalysis_phospates))
            <td class="text-indent-30">Amorphous Phospates:</td>
                <td><b class="cap">{{ $clinical->urinalysis_phospates }}</b></td>
            @else
                <td colspan="2">N/A</td>
            @endif
            <td>/LPF</td>
        </tr>

        <tr>
        <td class="text-indent-30">pH:</td>
            <td><b class="cap">{{ $clinical->ph ?? ' ' }}</b></td>
            <td class="text-indent-30">Mucus Thread:</td>
            <td><b class="cap">{{ $clinical->urinalysis_mucus_threads ?? ' ' }}</b></td>
            <td>/LPF</td>
        </tr>

        <tr>
        <td class="text-indent-30">Sp. Gravity:</td>
            <td><b class="cap">{{ $clinical->urinalysis_spgravity ?? ' ' }}</b></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td ></td>
            <td></td>
            <td>Others:</td>
            <td></td>
            <td></td>
        </tr>

          {{-- Cast --}}
          @if (!empty($clinical->urinalysis_casts))
            @php
                $casts = is_array($clinical->urinalysis_casts)
                    ? $clinical->urinalysis_casts
                    : json_decode($clinical->urinalysis_casts, true);
            @endphp
            @if (!empty($casts))
                <tr>
                    <td></td>
                    <td></td>
                    <td class="text-indent-40"><b>Cast:</b></td>
                </tr>
                @foreach ($casts as $cast)
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-indent-50">
                            {{ ucfirst(str_replace('_', ' ', $cast['type'])) }}
                        </td>
                        <td>
                            <b class="cap">{{ implode(', ', $cast['details']) }}</b>
                        </td>
                        <td>/LPF</td>
                    </tr>
                @endforeach
            @endif
        @endif

         {{-- Crystals --}}
         @if (!empty($clinical->urinalysis_crystals))
            @php
                $crystals = is_array($clinical->urinalysis_crystals)
                    ? $clinical->urinalysis_crystals
                    : json_decode($clinical->urinalysis_crystals, true);
            @endphp
            @if (!empty($crystals))
                <tr>
                    <td></td>
                    <td></td>
                    <td class="text-indent-40"><b>Crystals:</b></td>
                </tr>
                @foreach ($crystals as $crystal)
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-indent-50">
                            {{ ucfirst(str_replace('_', ' ', $crystal['type'])) }}
                        </td>
                        <td>
                            <b class="cap">{{ implode(', ', $crystal['details']) }}</b>
                        </td>
                        <td>/LPF</td>
                    </tr>
                @endforeach
            @endif
        @endif

         {{-- Fungal Elements --}}
         @if (!empty($clinical->urinalysis_fungal_elements))
            @php
                $fungals = is_array($clinical->urinalysis_fungal_elements)
                    ? $clinical->urinalysis_fungal_elements
                    : json_decode($clinical->urinalysis_fungal_elements, true);
            @endphp
            @if (!empty($fungals))
                <tr>
                    <td></td>
                    <td></td>
                    <td class="text-indent-40"><b>Fungal Elements:</b></td>
                </tr>
                @foreach ($fungals as $fungus)
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-indent-50">
                            {{ ucfirst(str_replace('_', ' ', $fungus['type'])) }}
                        </td>
                        <td><b class="cap">{{ implode(', ', $fungus['details']) }}</b></td>
                        <td>/HPF</td>
                    </tr>
                @endforeach
            @endif
        @endif

          {{-- Parasite --}}
        @if (!empty($clinical->urinalysis_parasite))
            @php
                $parasites = is_array($clinical->urinalysis_parasite)
                    ? $clinical->urinalysis_parasite
                    : json_decode($clinical->urinalysis_parasite, true);
            @endphp
            @if (!empty($parasites))
                <tr>
                    <td></td>
                    <td></td>
                    <td class="text-indent-40"><b>Parasite:</b></td>
                </tr>
                @foreach ($parasites as $parasite)
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-indent-50">
                            {{ ucfirst(str_replace('_', ' ', $parasite['type'])) }} 
                        </td>
                        <td><b class="cap"> {{ implode(', ', $parasite['details']) }}</b></td>
                        <td>/HPF</td>
                    </tr>
                @endforeach
            @endif
        @endif 
      
    </table>
    </table>

    <hr>
    <p class="ml-100"><strong>Remarks:</strong> {{ $clinical->urinalysis_remarks ?? 'N/A' }}</p>

    <table class="ml-100">
        <tr>
            <td>
                <p><strong>{{ $clinical->medical_technologist ?? 'JULLIUS ANUSENCION, RMT' }}</strong></p>
                <p class="mt--10">
                    Medical Technologist &nbsp;&nbsp;&nbsp;&nbsp; Lic. No.:

                    @if ($clinical->medical_technologist === 'JULLIUS D. ANUSENCION, RMT')
                        0056624
                    @elseif ($clinical->medical_technologist === 'KRISTINA CASSANDRA F. SANTOS, RMT')
                        0099818
                    @elseif ($clinical->medical_technologist === 'KATE ANGELINE M. SALAS, RMT')
                        0115834
                    @elseif ($clinical->medical_technologist === 'MARY GRACE L. BERNARDO, RMT')
                        0105656
                    @elseif ($clinical->medical_technologist === 'JANIELLE M. PASAMONTE, RMT')
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