@extends('layouts.app')

@section('content')
    <div class="w3-padding-large" id="main">
        <header class="w3-container w3-padding-32 w3-center w3-black" id="home">
            <h1 class="w3-jumbo">Unciv Cheats</h1>
            <p>Unciv tool collection</p>
            <img src="{{ asset('images/map.jpg') }}" alt="enable images please :'(" class="w3-image" width="992" height="1108">
        </header>

        <div class="w3-content w3-justify w3-text-grey w3-padding-64" id="about">
            <h2 class="w3-text-light-grey">Why cheating</h2>
            <hr style="width:200px" class="w3-opacity">
            <p>
                Cheating is bad, loosing is fun. But, for tests and theorycraft purpose it's not that bad to cheat.
            </p>
        </div>
@endsection