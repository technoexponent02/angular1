@extends('layouts.public')

@section('pageTitle', 'Browser not supported - Swolk')

@section('content')
<div class="login-wrapper">
    <div class="bg-pic">
        <img src="{{asset('assets/img/new-york-city-buildings-sunrise-morning-hd-wallpaper.jpg')}}" data-src="{{asset('assets/img/new-york-city-buildings-sunrise-morning-hd-wallpaper.jpg')}}" data-src-retina="{{asset('assets/img/demo/new-york-city-buildings-sunrise-morning-hd-wallpaper.jpg')}}" alt="" class="lazy">
        <div class="bg-caption pull-bottom sm-pull-bottom text-white p-l-20 m-b-20">
            <h2 class="semi-bold text-white">
            Pages make it easy to enjoy what matters the most in the life</h2>
            <p class="small">images Displayed are solely for representation purposes only, All work copyright of respective owner, otherwise © 2016-<?php echo date("Y"); ?> SWOLK.</p>
        </div>
    </div>
    <div class="login-container bg-white">
        <div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
            <h3 style="margin-top: 200px;font-size:20px;font-weight:bold;">Please update your browser</h3>
            <br>
            <div style="font-size:16px;line-height: 23px;">You are using an old version of Internet Explorer<br>
                Please update it or try one of these options.
            </div>
            <br>
            <br>

            <div style="display: inline-block">
                <a target="_blank" href="https://www.mozilla.org/en-US/firefox/new/" style="text-decoration: none;">
                    <img src="https://i.imgur.com/icGtPQC.png"/><br>
                </a>
            </div>

            <div style="display: inline-block;width:50px;height:1px;"></div>

            <div style="display: inline-block">
                <a target="_blank" href="https://www.google.com/chrome/browser/desktop/index.html" style="text-decoration: none;">
                    <img src="https://i.imgur.com/2J8FJ1Z.png"/><br>
                </a>
            </div>
        </div>
    </div>
</div>
@stop