@extends('layouts.master')

@section('subtitle', 'Databases')
@section('content_header_title', 'Settings')
@section('content_header_subtitle', 'All Databases')

@push('css')
    <style>
        .table td,
        .table th {
            vertical-align: top;
        }

        .col-list {
            column-count: 2;
        }

        /* two‑column checkbox grid */
        .col-list .item {
            break-inside: avoid;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Fade out animation */
        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        .toast-animation {
            animation: fadeIn 0.5s ease forwards;
            /* Optional: make it look like a toast */
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 15px 25px;
            border-radius: 4px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .toast-fadeout {
            animation: fadeOut 0.5s ease forwards;
        }
        
        .disabled-checkbox {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content_body')
    <h2 class="text-center mb-4">Access Database Tables</h2>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                $(function() {
                    $.notify(@js($error), {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                });
            </script>
        @endforeach
    @endif

    @if (session('queued'))
        <div id="queued-toast"
             class="alert alert-success toast-animation">
            Your request was queued successfully!
        </div>
    @endif

    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-body">

                        @if (count($result))
                            <table class="table table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>Table Name &nabla;</th>
                                        <th>Columns <small class="text-muted">(tick what you need)</small> &nabla;</th>
                                        <th style="width:14%">Action &nabla;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($result as $key => $table)
                                        <tr>
                                            {{-- ───────────── single form per row ───────────── --}}
                                            <form>
                                                @csrf

                                                {{-- table name cell --}}
                                                <td class="fw-semibold align-middle">
                                                    {{ $table['name'] }}
                                                    <input type="hidden"
                                                           name="table"
                                                           value="{{ $table['name'] }}">
                                                </td>

                                                {{-- columns with check‑boxes --}}
                                                <td class="col-list">
                                                    @foreach ($table['columns'] as $column)
                                                        @continue($column === 'id')
                                                        
                                                        @php
                                                            $isUserinfoTable = $table['name'] === 'userinfo';
                                                            $isRequiredColumn = in_array($column, ['USERID','Badgenumber','name']);
                                                            $isChecked = $isUserinfoTable && $isRequiredColumn;
                                                            $isDisabled = $isUserinfoTable && !$isRequiredColumn;
                                                        @endphp
                                                        
                                                        <div class="form-check item">
                                                            <input class="form-check-input {{ $isDisabled ? 'disabled-checkbox' : '' }}"
                                                                   type="checkbox"
                                                                   name="columns[]"
                                                                   value="{{ $column }}"
                                                                   id="chk_{{ $key }}_{{ $column }}"
                                                                   {{ $isChecked ? 'checked' : '' }}
                                                                   {{ $isDisabled ? 'disabled' : '' }}>
                                                            <label class="form-check-label {{ $isDisabled ? 'text-muted' : '' }}"
                                                                   for="chk_{{ $key }}_{{ $column }}">
                                                                {{ $column }}
                                                                @if($isDisabled)
                                                                    <small class="text-muted">(disabled)</small>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </td>

                                                {{-- buttons cell --}}
                                                <td class="align-middle">
                                                    <button type="submit"
                                                            class="btn btn-sm btn-primary w-100 mb-2"
                                                            formmethod="GET"
                                                            formaction="{{ route('admin.table.showSelected') }}">
                                                        View Data
                                                    </button>

                                                    <button type="submit"
                                                            class="btn btn-sm btn-warning w-100"
                                                            formaction="#">
                                                        Send Selected
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center mb-0">No tables found.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const toast = document.getElementById('queued-toast');
        if (toast) {
            // After 3 seconds, add the fade-out animation class
            setTimeout(() => {
                toast.classList.add('toast-fadeout');
            }, 3000);

            // After the fade-out animation completes (0.5s), remove the element
            toast.addEventListener('animationend', (event) => {
                if (event.animationName === 'fadeOut') {
                    toast.remove();
                }
            });

            // Optional: allow user to click the toast to dismiss immediately
            toast.addEventListener('click', () => {
                toast.classList.add('toast-fadeout');
            });
        }
    </script>
@endpush