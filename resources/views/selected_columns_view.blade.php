@extends('layouts.master')

@push('css')
    <style>
        .content-wrapper {
            min-height: calc(100vh - 100px);
        }

        .main-footer {
            display: none !important;
        }

        .mybtn:hover {
            background-color: red !important;
            color: white !important;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content_body')
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0 text-dark">Selected Columns:</h4>
            <button class="btn btn-sm btn-warning mybtn"
                    id="sendDataBtn">Send This Data</button>
        </div>

        <!-- DATA TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        @foreach ($columns as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            @foreach ($columns as $col)
                                <td>{{ $row->$col }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-center mt-3">
            {{ $data->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
        </div>


        <!-- LAST SYNC LOG -->
        <!-- LAST SYNC LOG -->
        <!-- LAST SYNC LOG -->
        <div class="mt-4">
            <h4>Last Sync Log</h4>
            <div class="border rounded p-3 bg-light">
                @if ($lastLog)
                    <div class="row">
                        <!-- 3-column fields -->
                        <div class="col-md-4 mb-2"><strong>Log ID:</strong> {{ $lastLog->id }}</div>
                        <div class="col-md-4 mb-2"><strong>Chunk Size:</strong> {{ $lastLog->chunk_size }}</div>
                               <div class="col-md-4 mb-2"><strong>Created At:</strong>
                            {{ \Carbon\Carbon::parse($lastLog->created_at)->format('d M Y, h:i:s A') }}</div>
                       

                        <div class="col-md-4 mb-2"><strong>Inserted Count:</strong> {{ $lastLog->inserted_count }}</div>
                        <div class="col-md-4 mb-2"><strong>Errors Count:</strong> {{ $lastLog->errors_count ?? 0 }}</div>

                         <div class="col-md-4 mb-2"><strong>Updated At:</strong>
                            {{ \Carbon\Carbon::parse($lastLog->updated_at)->format('d M Y, h:i:s A') }}</div>
                    

                       <div class="col-md-4 mb-2"><strong>Success:</strong> {{ $lastLog->success == 1 ? 'Yes' : 'No' }}
                        </div>
                           <div class="col-md-4 mb-2"><strong>Response Status:</strong> {{ $lastLog->response_status }}</div>
                        <div class="col-md-4 mb-2">
                            @if (!empty($lastLog->error_details))
                                <strong>Error Details:</strong>
                                <pre>{{ $lastLog->error_details }}</pre>
                            @endif
                        </div>
                    </div>

                    <!-- Full-width fields for long content -->
                    <div class="mt-3">
                        <strong>Duplicate Existing Before:</strong>
                        <pre style="max-height: 200px; overflow-y: auto;">
                            @if (!empty($lastLog->duplicate_existing_before))
                            {{ $lastLog->duplicate_existing_before }}
                            @else
                            None
                            @endif
                </pre>
                    </div>

                    <div class="mt-3">
                        <strong>Duplicate From Race Condition:</strong>
                        <pre style="max-height: 200px; overflow-y: auto;">
                                {{ $lastLog->duplicate_from_race_condition }}   
                </pre>
                    </div>
                @else
                    <p>No sync logs found yet.</p>
                @endif
            </div>
        </div>




        <!-- LOADING OVERLAY -->
        <div class="loading-overlay"
             id="loadingOverlay">
            <div class="spinner"></div>
        </div>

    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sendBtn = document.getElementById('sendDataBtn');
            const loadingOverlay = document.getElementById('loadingOverlay');

            sendBtn.addEventListener('click', function() {
                loadingOverlay.style.display = 'flex';

                fetch('{{ route('send.all.userid') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            table: "{{ $table }}",
                            columns: @json($columns)
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        loadingOverlay.style.display = 'none';
                        if (data.success) {
                            alert("Sync started for " + data.total_records +
                                " records.\n\nJob running in background.");
                        } else {
                            alert("Error: " + data.message);
                        }
                    })
                    .catch(error => {
                        loadingOverlay.style.display = 'none';
                        alert("Something went wrong.");
                        console.error(error);
                    });
            });
        });
    </script>
@endpush
