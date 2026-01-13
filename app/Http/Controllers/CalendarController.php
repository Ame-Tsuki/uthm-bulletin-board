@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Calendar content here -->
            @switch(auth()->user()->role)
                @case('student')
                    @include('student.calendar')
                    @break
                @case('staff')
                    @include('staff.calendar')
                    @break
                @case('club_admin')
                    @include('club.calendar')
                    @break
                @case('admin')
                    @include('admin.calendar')
                    @break
                @default
                    @include('student.calendar')
            @endswitch
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Calendar JavaScript can go here
    </script>
@endsection