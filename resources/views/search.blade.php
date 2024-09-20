@extends('layout')
@section('title', 'ค้นหา')
@section('content')

    <style>

        html, body {
            height: 100%;
            min-height: 100%;
        }

        body {
            margin: 0;
        }

        .tb {
            display: table;
            width: 100%;
        }

        .td {
            display: table-cell;
            vertical-align: middle;
        }

        input, button {
            color: #e45656;
            font-family: Nunito;
            padding: 0;
            margin: 0;
            border: 0;
            background-color: transparent;
        }

        #cover {
            margin: 20px auto;
            padding: 15px;
            width: 700px;
            background-color: #ff7575;
            border-radius: 10px;
            box-shadow: 0 5px 15px #ff7c7c, 0 0 0 10px #787171eb;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
        }

        form {
            height: 40px;
        }

        input[type="text"] {
            width: 100%;
            height: 35px;
            font-size: 30px;
            line-height: 1;
        }

        input[type="text"]::placeholder {
            color: #e16868;
        }

        #s-cover {
            width: 1px;
            padding-left: 10px;
        }

        button {
            position: relative;
            display: block;
            width: 35px;
            height: 35px;
            cursor: pointer;
        }

        #s-circle {
            position: relative;
            top: -5px;
            left: 0;
            width: 30px;
            height: 30px;
            border-width: 5px;
            border: 5px solid #e57171;
            background-color: transparent;
            border-radius: 50%;
            transition: 0.5s ease all;
        }

        button span {
            position: absolute;
            top: 35px;
            left: 20px;
            display: block;
            width: 20px;
            height: 5px;
            background-color: transparent;
            border-radius: 10px;
            transform: rotateZ(52deg);
            transition: 0.5s ease all;
        }

        button span:before, button span:after {
            content: "";
            position: absolute;
            bottom: 0;
            right: 0;
            width: 20px;
            height: 5px;
            background-color: #e94d4d;
            border-radius: 10px;
            transform: rotateZ(0);
            transition: 0.5s ease all;
        }

        #s-cover:hover #s-circle {
            top: -1px;
            width: 30px;
            height: 5px;
            border-width: 0;
            background-color: #9f5f5f;
            border-radius: 20px;
        }

        #s-cover:hover span {
            top: 50%;
            left: 25px;
            width: 15px;
            margin-top: -5px;
            transform: rotateZ(0);
        }

        #s-cover:hover button span:before {
            bottom: 5px;
            transform: rotateZ(52deg);
        }

        #s-cover:hover button span:after {
            bottom: -5px;
            transform: rotateZ(-52deg);
        }

        #s-cover:hover button span:before, #s-cover:hover button span:after {
            right: -3px;
            width: 15px;
            background-color: #e54e4e;
        }
    </style>

    <div class="search-container">
        <h2>Search Bookings</h2>
        <form method="get" action="{{ route('search') }}">
            <div class="tb">
                <div class="td"><input type="text" name="search" placeholder="Search by phone number" required value="{{ $searchTerm ?? '' }}"></div>
                <div class="td" id="s-cover">
                    <button type="submit">
                        <div id="s-circle"></div>
                        <span></span>
                    </button>
                </div>
            </div>
        </form>

        @if(isset($results))
            <h3>Search Results</h3>
            @if(count($results) > 0)
                <table class="search-results">
                    <thead>
                        <tr>
                            <th>Phone</th>
                            <th>Name</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr>
                                <td>{{ $result['Phone'] }}</td>
                                <td>{{ $result['Name'] }}</td>
                                <td>{{ $result['Origin'] }}</td>
                                <td>{{ $result['Destination'] }}</td>
                                <td>{{ $result['Amount'] }}</td>
                                <td>
                                    @if($result['PaymentStatus'] === 0)
                                        Waiting for Confirmation
                                    @elseif($result['PaymentStatus'] === 1)
                                        Confirmed
                                    @else
                                        Unknown
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No results found for "{{ $searchTerm }}"</p>
            @endif
        @endif
    </div>

@endsection
