<!DOCTYPE html>
<html>
<head>
    <title>Table View: {{ $table }}</title>
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
        }
        table, th, td {
            border: 1px solid #444;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
        }
        p {
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Table: {{ $table }}</h2>

    @if(count($rows))
        <table>
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($columns as $column)
                            <td>{{ $row[$column] }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No data found in this table.</p>
    @endif

</body>
</html>
