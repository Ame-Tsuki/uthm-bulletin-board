@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back button -->
        <a href="{{ route('student.community-hub') }}" class="inline-flex items-center text-uthm-blue hover:text-blue-700 mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Back to Community Hub
        </a>
        
        <!-- Group Header -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-uthm-blue to-blue-700 h-32"></div>
            <div class="px-6 py-4 relative">
                <div class="absolute -top-12 left-6">
                    <div class="w-24 h-24 bg-white rounded-xl shadow-lg flex items-center justify-center">
                        <i class="fas fa-users text-uthm-blue text-4xl"></i>
                    </div>
                </div>
                <div class="ml-32 mt-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $group->name }}</h1>
                            <p class="text-gray-600 mt-1">Created by {{ $group->creator->name }}</p>
                        </div>
                        @if(!$isMember)
                            <form action="{{ route('student.community-hub.join', $group->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-uthm-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                    Join Group
                                </button>
                            </form>
                        @else
                            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg">
                                <i class="fas fa-check-circle mr-2"></i> Member
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Group Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <i class="fas fa-users text-uthm-blue text-2xl mb-2"></i>
                <p class="text-2xl font-bold">{{ $group->members_count }}</p>
                <p class="text-gray-600">Members</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <i class="fas fa-lock text-uthm-blue text-2xl mb-2"></i>
                <p class="text-2xl font-bold">{{ ucfirst($group->privacy) }}</p>
                <p class="text-gray-600">Privacy</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <i class="fas fa-tag text-uthm-blue text-2xl mb-2"></i>
                <p class="text-2xl font-bold">{{ $group->category }}</p>
                <p class="text-gray-600">Category</p>
            </div>
        </div>
        
        <!-- Group Description -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-3">About this group</h3>
            <p class="text-gray-700">{{ $group->description }}</p>
            @if($group->max_members)
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Capacity</span>
                        <span class="font-medium">{{ $group->members_count }}/{{ $group->max_members }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-uthm-blue h-2 rounded-full" style="width: {{ ($group->members_count / $group->max_members) * 100 }}%"></div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Members List -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Members ({{ $group->members_count }})</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($members as $member)
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center">
                            <span class="font-bold text-uthm-blue">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-sm">{{ $member->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($member->role) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $members->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
