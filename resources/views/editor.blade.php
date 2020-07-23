@extends('layouts.app')

@section('content')
    <div class="w3-padding-large" id="main">
        <header class="w3-container w3-padding-32 w3-center w3-black" id="home">
            <h1 class="w3-jumbo">Save Editor</h1>
        </header>

        <div class="w3-content w3-justify w3-text-grey w3-padding-64" id="about">
            <h2 class="w3-text-light-grey">Copy</h2>
            <hr style="width:200px" class="w3-opacity">
            <p>
                <img src="{{ asset('images/how_to_copy.png') }}" alt="enable images please :'(" class="w3-image" width="992" height="1108">
            </p>
            <h2 class="w3-text-light-grey">Paste</h2>
            <hr style="width:200px" class="w3-opacity">
            @if($errors->any())
                <h4>{{$errors->first()}}</h4>
            @endif
            <form action="/editor" method="POST">
                @csrf
                
                <p><input class="w3-input w3-padding-16" type="text" placeholder="Paste your save here" required name="save"></p>
                <p>
                    <button class="w3-button w3-light-grey w3-padding-large w3-section" type="submit">
                        <i class="fa fa-cogs"></i> Process save
                    </button>
                </p>
            </form>
        </div>
@endsection