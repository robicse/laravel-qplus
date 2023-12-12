@extends('backend.layouts.master')
@section("title","Stocks Lists")

@section('title','Not Found')
@section('content')
    <div class="main-content">
        <section id="error">
            <div class="container">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-3 align-self-center">
                                <h1 class="mb-0 error-number">
                                    404
                                </h1>
                            </div>
                            <div class="col-md-7 align-self-center">
                                <p class="mb-0 error-text">

                                    <span>Oops! You're lost.</span>

                                    The page you are looking for was not found.

                                </p>
                            </div>
                        </div>

                        <form class="d-flex m-auto mt-3" action="{{url('/search')}}" method="get" role="search" >
                            @csrf
                            <input class="form-control w-75 top-search-box" value="{{Request::segment(2)}}" name="keyword" required type="search" placeholder="Search" aria-label="Search">
                            <button class="btn top-search-btn" aria-label="Left Align" type="submit"><img src="{{url('storage/app/files/shares/backend/icon/search.webp?timestamp=1646411368')}}" width="24" height="24"  alt="search icon"></button>
                        </form>

                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
        </section>



    </div>

@endsection()
