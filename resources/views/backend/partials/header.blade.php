@php
$userData = \Illuminate\Support\Facades\Auth::user();
$languageList = \App\Models\Languages::all();
$currentLanguage = \App\Models\Languages::getBackLanguageId();
$lang = \App\Models\Languages::getBackLang();
$langData = \App\Models\Languages::getCurrentLangData();

@endphp
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        {{--@foreach($languageList as $language)
            @php
                $activeClass = '';
                if($currentLanguage == $language->language_id){
                    $activeClass = 'active';
                }
            @endphp
            <li class="multiple-language-scec nav-item d-none d-sm-inline-block">
                <a href="{{route('admin.language',$language->language_id)}}" class="multiple-language-link nav-link">
        <img class="multiple-language-img" src="{{asset($language->icon)}}">{{$language->name}}
        </a>
        </li>
        @endforeach--}}
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="multiple-language-scec nav-item dropdown">
            <a class="multiple-language-link nav-link dropdown-toggle" href="{{route('admin.language',$currentLanguage)}}" id="dropdown09" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="flag-icon flag-icon-us"><img class="multiple-language-img" src="{{asset($langData['icon'])}}"></span> {{$langData['name']}}
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdown09">
                @foreach($languageList as $language)
                <a class="dropdown-item" href="{{route('admin.language',$language->language_id)}}"><span class="flag-icon flag-icon-fr"><img class="multiple-language-img" src="{{asset($language->icon)}}"> </span>{{$language->name}}
                </a>
                @endforeach
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
