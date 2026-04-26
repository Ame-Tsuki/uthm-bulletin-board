@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Group</h1>
        
        <form action="{{ route('student.community-hub.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Group Name *</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:border-uthm-blue">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Description *</label>
                <textarea name="description" rows="4" required class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:border-uthm-blue"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Category *</label>
                <select name="category" required class="w-full border border-gray-300 rounded-lg p-2">
                    <option value="Academic">Academic</option>
                    <option value="Sports">Sports</option>
                    <option value="Arts">Arts</option>
                    <option value="Technology">Technology</option>
                    <option value="Social">Social</option>
                    <option value="Career">Career</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Privacy *</label>
                <select name="privacy" required class="w-full border border-gray-300 rounded-lg p-2">
                    <option value="public">Public - Anyone can join</option>
                    <option value="by_approval">By Approval - Request to join</option>
                    <option value="private">Private - Invite only</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Max Members (Optional)</label>
                <input type="number" name="max_members" class="w-full border border-gray-300 rounded-lg p-2">
                <p class="text-sm text-gray-500 mt-1">Leave empty for unlimited</p>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('student.community-hub') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700">Create Group</button>
            </div>
        </form>
    </div>
</div>
@endsection